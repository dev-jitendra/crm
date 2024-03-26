<?php


namespace Espo\Core\Utils\File;

use Espo\Core\Utils\Json;
use Espo\Core\Utils\Util;
use Espo\Core\Utils\File\Exceptions\FileError;
use Espo\Core\Utils\File\Exceptions\PermissionError;

use stdClass;
use Throwable;
use InvalidArgumentException;

use const E_USER_DEPRECATED;

class Manager
{
    private Permission $permission;

    
    private $permissionDeniedList = [];

    protected string $tmpDir = 'data/tmp';

    protected const RENAME_RETRY_NUMBER = 10;
    protected const RENAME_RETRY_INTERVAL = 0.1;
    protected const GET_SAFE_CONTENTS_RETRY_NUMBER = 10;
    protected const GET_SAFE_CONTENTS_RETRY_INTERVAL = 0.1;

    
    public function __construct(?array $defaultPermissions = null)
    {
        $params = null;

        if ($defaultPermissions) {
            $params = [
                'defaultPermissions' => $defaultPermissions,
            ];
        }

        $this->permission = new Permission($this, $params);
    }

    public function getPermissionUtils(): Permission
    {
        return $this->permission;
    }

    
    public function getDirList(string $path): array
    {
        
        return $this->getFileList($path, false, '', false);
    }

    
    public function getFileList(
        string $path,
        $recursively = false,
        $filter = '',
        $onlyFileType = null,
        bool $returnSingleArray = false
    ): array {

        $result = [];

        if (!file_exists($path) || !is_dir($path)) {
            return $result;
        }

        $cdir = scandir($path) ?: [];

        foreach ($cdir as $value) {
            if (in_array($value, [".", ".."])) {
                continue;
            }

            $add = false;

            if (is_dir($path . Util::getSeparator() . $value)) {
                
                if (
                    !is_int($recursively) && $recursively ||
                    is_int($recursively) && $recursively !== 0
                ) {
                    $nextRecursively = is_int($recursively) ? ($recursively - 1) : $recursively;

                    $result[$value] = $this->getFileList(
                        $path . Util::getSeparator() . $value,
                        $nextRecursively,
                        $filter,
                        $onlyFileType
                    );
                }
                else if (!isset($onlyFileType) || !$onlyFileType) { 
                    $add = true;
                }
            }
            else if (!isset($onlyFileType) || $onlyFileType) { 
                $add = true;
            }

            if (!$add) {
                continue;
            }

            if (!empty($filter)) {
                if (preg_match('/'.$filter.'/i', $value)) {
                    $result[] = $value;
                }

                continue;
            }

            $result[] = $value;
        }

        if ($returnSingleArray) {
            
            return $this->getSingleFileList($result, $onlyFileType, $path);
        }

        
        return $result;
    }

    
    private function getSingleFileList(
        array $fileList,
        $onlyFileType = null,
        $basePath = null,
        $parentDirName = ''
    ): array {

        $singleFileList = [];

        foreach ($fileList as $dirName => $fileName) {
            if (is_array($fileName)) {
                $currentDir = Util::concatPath($parentDirName, $dirName);

                if (
                    !isset($onlyFileType) ||
                    $onlyFileType == $this->isFilenameIsFile($basePath . '/' . $currentDir)
                ) {
                    $singleFileList[] = $currentDir;
                }

                $singleFileList = array_merge(
                    $singleFileList, $this->getSingleFileList($fileName, $onlyFileType, $basePath, $currentDir)
                );
            }
            else {
                $currentFileName = Util::concatPath($parentDirName, $fileName);

                if (
                    !isset($onlyFileType) ||
                    $onlyFileType == $this->isFilenameIsFile($basePath . '/' . $currentFileName)
                ) {
                    $singleFileList[] = $currentFileName;
                }
            }
        }

        return $singleFileList;
    }

    
    public function getContents($path): string
    {
        

        if (is_array($path)) {
            
            
            trigger_error(
                'Array parameter is deprecated for FileManager::getContents.',
                E_USER_DEPRECATED
            );

            $path = $this->concatPaths($path);
        }
        else if (!is_string($path)) {
            throw new InvalidArgumentException();
        }

        if (!file_exists($path)) {
            throw new FileError("File '{$path}' does not exist.");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new FileError("Could not open file '{$path}'.");
        }

        return $contents;
    }

    
    public function getPhpContents(string $path)
    {
        if (!file_exists($path)) {
            throw new FileError("File '$path' does not exist.");
        }

        if (strtolower(substr($path, -4)) !== '.php') {
            throw new FileError("File '$path' is not PHP.");
        }

        return include($path);
    }

    
    public function getPhpSafeContents(string $path)
    {
        if (!file_exists($path)) {
            throw new FileError("Can't get contents from non-existing file '{$path}'.");
        }

        if (!strtolower(substr($path, -4)) == '.php') {
            throw new FileError("Only PHP file are allowed for getting contents.");
        }

        $counter = 0;

        while ($counter < self::GET_SAFE_CONTENTS_RETRY_NUMBER) {
            $data = include($path);

            if (is_array($data) || $data instanceof stdClass) {
                return $data;
            }

            usleep((int) (self::GET_SAFE_CONTENTS_RETRY_INTERVAL * 1000000));

            $counter ++;
        }

        throw new FileError("Bad data stored in file '{$path}'.");
    }

    
    public function putContents(string $path, $data, int $flags = 0, bool $useRenaming = false): bool
    {
        if ($this->checkCreateFile($path) === false) {
            throw new PermissionError('Permission denied for '. $path);
        }

        $result = false;

        if ($useRenaming) {
            $result = $this->putContentsUseRenaming($path, $data);
        }

        if (!$result) {
            $result = (file_put_contents($path, $data, $flags) !== false);
        }

        if ($result) {
            $this->opcacheInvalidate($path);
        }

        return (bool) $result;
    }

    
    private function putContentsUseRenaming(string $path, $data): bool
    {
        $tmpDir = $this->tmpDir;

        if (!$this->isDir($tmpDir)) {
            $this->mkdir($tmpDir);
        }

        if (!$this->isDir($tmpDir)) {
            return false;
        }

        $tmpPath = tempnam($tmpDir, 'tmp');

        if ($tmpPath === false) {
            return false;
        }

        $tmpPath = $this->getRelativePath($tmpPath);

        if (!$tmpPath) {
            return false;
        }

        if (!$this->isFile($tmpPath)) {
            return false;
        }

        if (!$this->isWritable($tmpPath)) {
            return false;
        }

        $h = fopen($tmpPath, 'w');

        if ($h === false) {
            return false;
        }

        fwrite($h, $data);
        fclose($h);

        $this->getPermissionUtils()->setDefaultPermissions($tmpPath);

        if (!$this->isReadable($tmpPath)) {
            return false;
        }

        $result = rename($tmpPath, $path);

        if (!$result && stripos(\PHP_OS, 'WIN') === 0) {
            $result = $this->renameInLoop($tmpPath, $path);
        }

        if ($this->isFile($tmpPath)) {
            $this->removeFile($tmpPath);
        }

        return (bool) $result;
    }

    private function renameInLoop(string $source, string $destination): bool
    {
        $counter = 0;

        while ($counter < self::RENAME_RETRY_NUMBER) {
            if (!$this->isWritable($destination)) {
                break;
            }

            $result = rename($source, $destination);

            if ($result !== false) {
                return true;
            }

            usleep((int) (self::RENAME_RETRY_INTERVAL * 1000000));

            $counter++;
        }

        return false;
    }

    
    public function putPhpContents(string $path, $data, bool $withObjects = false, bool $useRenaming = false): bool
    {
        return $this->putContents($path, $this->wrapForDataExport($data, $withObjects), LOCK_EX, $useRenaming);
    }

    
    public function putJsonContents(string $path, $data): bool
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $contents = Json::encode($data, $options);

        return $this->putContents($path, $contents, LOCK_EX);
    }

    
    public function mergeJsonContents(string $path, array $data): bool
    {
        $currentData = [];

        if ($this->isFile($path)) {
            $currentContents = $this->getContents($path);

            $currentData = Json::decode($currentContents, true);
        }

        if (!is_array($currentData)) {
            throw new FileError("Neither array nor object in '{$path}'.");
        }

        $mergedData = Util::merge($currentData, $data);

        $stringData = Json::encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return (bool) $this->putContents($path, $stringData);
    }

    
    public function appendContents(string $path, $data): bool
    {
        return $this->putContents($path, $data, FILE_APPEND | LOCK_EX);
    }

    
    public function unsetJsonContents(string $path, array $unsets): bool
    {
        if (!$this->isFile($path)) {
            return true;
        }

        $currentContents = $this->getContents($path);

        $currentData = Json::decode($currentContents, true);

        $unsettedData = Util::unsetInArray($currentData, $unsets, true);

        if (empty($unsettedData)) {
            return $this->unlink($path);
        }

        return (bool) $this->putJsonContents($path, $unsettedData);
    }

    
    private function concatPaths($paths)
    {
        if (is_string($paths)) {
            return Util::fixPath($paths);
        }

        $fullPath = '';

        foreach ($paths as $path) {
            $fullPath = Util::concatPath($fullPath, $path);
        }

        return $fullPath;
    }

    
    public function mkdir(string $path, $permission = null): bool
    {
        if (file_exists($path) && is_dir($path)) {
            return true;
        }

        $parentDirPath = dirname($path);

        if (!file_exists($parentDirPath)) {
            $this->mkdir($parentDirPath, $permission);
        }

        $defaultPermissions = $this->getPermissionUtils()->getRequiredPermissions($path);

        if (!isset($permission)) {
            $permission = (int) base_convert((string) $defaultPermissions['dir'], 8, 10);
        }

        if (is_dir($path)) {
            return true;
        }

        $umask = umask(0);

        $result = mkdir($path, $permission);

        if ($umask) {
            umask($umask);
        }

        if (!$result && is_dir($path)) {
            
            return true;
        }

        if (!empty($defaultPermissions['user'])) {
            $this->getPermissionUtils()->chown($path);
        }

        if (!empty($defaultPermissions['group'])) {
            $this->getPermissionUtils()->chgrp($path);
        }

        return $result;
    }

    
    public function copy(
        string $sourcePath,
        string $destPath,
        bool $recursively = false,
        array $fileList = null,
        bool $copyOnlyFiles = false
    ): bool {

        if (!isset($fileList)) {
            $fileList = is_file($sourcePath) ?
                (array) $sourcePath :
                $this->getFileList($sourcePath, $recursively, '', true, true);
        }

        $permissionDeniedList = [];

        

        foreach ($fileList as $file) {
            if ($copyOnlyFiles) {
                $file = pathinfo($file, PATHINFO_BASENAME);
            }

            $destFile = Util::concatPath($destPath, $file);

            $isFileExists = file_exists($destFile);

            if ($this->checkCreateFile($destFile) === false) {
                $permissionDeniedList[] = $destFile;
            }
            else if (!$isFileExists) {
                $this->removeFile($destFile);
            }
        }

        if (!empty($permissionDeniedList)) {
            $betterPermissionList = $this->getPermissionUtils()->arrangePermissionList($permissionDeniedList);

            throw new PermissionError("Permission denied for <br>". implode(", <br>", $betterPermissionList));
        }

        $res = true;

        foreach ($fileList as $file) {
            if ($copyOnlyFiles) {
                $file = pathinfo($file, PATHINFO_BASENAME);
            }

            $sourceFile = is_file($sourcePath) ?
                $sourcePath :
                Util::concatPath($sourcePath, $file);

            $destFile = Util::concatPath($destPath, $file);

            if (file_exists($sourceFile) && is_file($sourceFile)) {
                $res &= copy($sourceFile, $destFile);

                $this->getPermissionUtils()->setDefaultPermissions($destFile);
                $this->opcacheInvalidate($destFile);
            }
        }

        return (bool) $res;
    }

    
    public function checkCreateFile(string $filePath): bool
    {
        $defaultPermissions = $this->getPermissionUtils()->getRequiredPermissions($filePath);

        if (file_exists($filePath)) {
            if (
                !is_writable($filePath) &&
                !in_array(
                    $this->getPermissionUtils()->getCurrentPermission($filePath),
                    [$defaultPermissions['file'], $defaultPermissions['dir']]
                )
            ) {
                return $this->getPermissionUtils()->setDefaultPermissions($filePath);
            }

            return true;
        }

        $pathParts = pathinfo($filePath);

        
        $dirname = $pathParts['dirname'] ?? null;

        if (!file_exists($dirname)) {
            $dirPermissionOriginal = $defaultPermissions['dir'];

            $dirPermission = is_string($dirPermissionOriginal) ?
                (int) base_convert($dirPermissionOriginal, 8, 10) :
                $dirPermissionOriginal;

            if (!$this->mkdir($dirname, $dirPermission)) {
                throw new PermissionError('Permission denied: unable to create a folder on the server ' . $dirname);
            }
        }

        $touchResult = touch($filePath);

        if (!$touchResult) {
            return false;
        }

        $setPermissionsResult = $this->getPermissionUtils()->setDefaultPermissions($filePath);

        if (!$setPermissionsResult) {
            $this->unlink($filePath);

            
            return true;
        }

        return true;
    }

    
    public function unlink($filePaths): bool
    {
        return $this->removeFile($filePaths);
    }

    
    public function rmdir($dirPaths): bool
    {
        if (!is_array($dirPaths)) {
            $dirPaths = (array) $dirPaths;
        }

        $result = true;

        foreach ($dirPaths as $dirPath) {
            if (is_dir($dirPath) && is_writable($dirPath)) {
                $result &= rmdir($dirPath);
            }
        }

        return (bool) $result;
    }

    
    public function removeDir($dirPaths): bool
    {
        return $this->rmdir($dirPaths);
    }

    
    public function removeFile($filePaths, $dirPath = null): bool
    {
        if (!is_array($filePaths)) {
            $filePaths = (array) $filePaths;
        }

        $result = true;

        foreach ($filePaths as $filePath) {
            if (isset($dirPath)) {
                $filePath = Util::concatPath($dirPath, $filePath);
            }

            if (file_exists($filePath) && is_file($filePath)) {
                $this->opcacheInvalidate($filePath, true);

                $result &= unlink($filePath);
            }
        }

        return (bool) $result;
    }

    
    public function removeInDir(string $path, bool $removeWithDir = false): bool
    {
        
        $fileList = $this->getFileList($path, false);

        $result = true;

        
        if (is_array($fileList)) {
            foreach ($fileList as $file) {
                $fullPath = Util::concatPath($path, $file);

                if (is_dir($fullPath)) {
                    $result &= $this->removeInDir($fullPath, true);
                }
                else if (file_exists($fullPath)) {
                    $this->opcacheInvalidate($fullPath, true);

                    $result &= unlink($fullPath);
                }
            }
        }

        if ($removeWithDir && $this->isDirEmpty($path)) {
            $result &= $this->rmdir($path);
        }

        return (bool) $result;
    }

    
    public function remove($items, $dirPath = null, bool $removeEmptyDirs = false): bool
    {
        if (!is_array($items)) {
            $items = (array) $items;
        }

        $removeList = [];
        $permissionDeniedList = [];

        foreach ($items as $item) {
            if (isset($dirPath)) {
                $item = Util::concatPath($dirPath, $item);
            }

            if (!file_exists($item)) {
                continue;
            }

            $removeList[] = $item;

            if (!is_writable($item)) {
                $permissionDeniedList[] = $item;
            }
            else if (!is_writable(dirname($item))) {
                $permissionDeniedList[] = dirname($item);
            }
        }

        if (!empty($permissionDeniedList)) {
            $betterPermissionList = $this->getPermissionUtils()->arrangePermissionList($permissionDeniedList);

            throw new PermissionError("Permission denied for <br>". implode(", <br>", $betterPermissionList));
        }

        $result = true;

        foreach ($removeList as $item) {
            if (is_dir($item)) {
                $result &= $this->removeInDir($item, true);
            }
            else {
                $result &= $this->removeFile($item);
            }

            if ($removeEmptyDirs) {
                $result &= $this->removeEmptyDirs($item);
            }
        }

        return (bool) $result;
    }

    
    private function removeEmptyDirs(string $path): bool
    {
        $parentDirName = $this->getParentDirName($path);

        $res = true;

        if ($this->isDirEmpty($parentDirName)) {
            $res &= $this->rmdir($parentDirName);
            $res &= $this->removeEmptyDirs($parentDirName);
        }

        return (bool) $res;
    }

    
    public function isDir(string $dirPath): bool
    {
        return is_dir($dirPath);
    }

    
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    
    public function getSize(string $path): int
    {
        $size = filesize($path);

        if ($size === false) {
            throw new FileError("Could not get file size for `{$path}`.");
        }

        return $size;
    }

    
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    
    private function isFilenameIsFile(string $path): bool
    {
        if (file_exists($path)) {
            return is_file($path);
        }

        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

        if (!empty($fileExtension)) {
            return true;
        }

        return false;
    }

    
    public function isDirEmpty(string $path): bool
    {
        if (is_dir($path)) {
            $fileList = $this->getFileList($path, true);

            if (is_array($fileList) && empty($fileList)) {
                return true;
            }
        }

        return false;
    }

    
    public function getFileName(string $fileName, string $extension = ''): string
    {
        if (empty($extension)) {
            $dotIndex = strrpos($fileName, '.', -1);

            if ($dotIndex === false) {
                $dotIndex = strlen($fileName);
            }

            $fileName = substr($fileName, 0, $dotIndex);
        }
        else {
            if (substr($extension, 0, 1) != '.') {
                $extension = '.' . $extension;
            }

            if (substr($fileName, -(strlen($extension))) == $extension) {
                $fileName = substr($fileName, 0, -(strlen($extension)));
            }
        }

        $array = explode('/', Util::toFormat($fileName, '/'));

        return end($array);
    }

    
    public function getDirName(string $path, bool $isFullPath = true, bool $useIsDir = true): string
    {
        
        $dirName = preg_replace('/\/$/i', '', $path);

        $dirName = ($useIsDir && is_dir($dirName)) ?
            $dirName :
            pathinfo($dirName, PATHINFO_DIRNAME);

        if (!$isFullPath) {
            $pieces = explode('/', $dirName);
            $dirName = $pieces[count($pieces)-1];
        }

        return $dirName;
    }

    
    public function getParentDirName(string $path, bool $isFullPath = true): string
    {
        return $this->getDirName($path, $isFullPath, false);
    }

    
    public function wrapForDataExport($data, bool $withObjects = false)
    {
        if (!isset($data)) {
            return false;
        }

        if (!$withObjects) {
            return "<?php\n" .
                "return " . var_export($data, true) . ";\n";
        }

        return "<?php\n" .
            "return " . $this->varExport($data) . ";\n";
    }

    
    private function varExport($variable, int $level = 0): string
    {
        $tab = '';
        $tabElement = '  ';

        for ($i = 0; $i <= $level; $i++) {
            $tab .= $tabElement;
        }

        $prevTab = substr($tab, 0, strlen($tab) - strlen($tabElement));

        if ($variable instanceof stdClass) {
            return "(object) " . $this->varExport(get_object_vars($variable), $level);
        }

        if (is_array($variable)) {
            $array = [];

            foreach ($variable as $key => $value) {
                $array[] = var_export($key, true) . " => " . $this->varExport($value, $level + 1);
            }

            if (count($array) === 0) {
                return "[]";
            }

            return "[\n" . $tab . implode(",\n" . $tab, $array) . "\n" . $prevTab . "]";
        }

        return var_export($variable, true);
    }

    
    public function isWritableList(array $paths): bool
    {
        $permissionDeniedList = [];

        $result = true;

        foreach ($paths as $path) {
            $rowResult = $this->isWritable($path);

            if (!$rowResult) {
                $permissionDeniedList[] = $path;
            }

            $result &= $rowResult;
        }

        if (!empty($permissionDeniedList)) {
            $this->permissionDeniedList =
                $this->getPermissionUtils()->arrangePermissionList($permissionDeniedList);
        }

        return (bool) $result;
    }

    
    public function getLastPermissionDeniedList(): array
    {
        return $this->permissionDeniedList;
    }

    
    public function isWritable(string $path): bool
    {
        $existFile = $this->getExistsPath($path);

        return is_writable($existFile);
    }

    
    public function isReadable(string $path): bool
    {
        $existFile = $this->getExistsPath($path);

        return is_readable($existFile);
    }

    
    private function getExistsPath(string $path): string
    {
        if (!file_exists($path)) {
            return $this->getExistsPath(pathinfo($path, PATHINFO_DIRNAME));
        }

        return $path;
    }

    
    public function getRelativePath(string $path, ?string $basePath = null, ?string $dirSeparator = null): string
    {
        if (!$basePath) {
            $basePath = getcwd();
        }

        if ($basePath === false) {
            return '';
        }

        $path = Util::fixPath($path);
        $basePath = Util::fixPath($basePath);

        if (!$dirSeparator) {
            $dirSeparator = Util::getSeparator();
        }

        if (substr($basePath, -1) != $dirSeparator) {
            $basePath .= $dirSeparator;
        }

        
        return preg_replace('/^'. preg_quote($basePath, $dirSeparator) . '/', '', $path);
    }

    private function opcacheInvalidate(string $filepath, bool $force = false): void
    {
        if (!function_exists('opcache_invalidate')) {
            return;
        }

        try {
            opcache_invalidate($filepath, $force);
        }
        catch (Throwable $e) {}
    }
}
