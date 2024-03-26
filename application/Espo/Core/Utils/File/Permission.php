<?php


namespace Espo\Core\Utils\File;

use Espo\Core\Utils\Util;

use Throwable;

class Permission
{

    
    protected $permissionError = [];

    
    protected $permissionErrorRules = null;

    
    protected $writableMap = [
        'data' => [
            'recursive' => true,
        ],
        'application/Espo/Modules' => [
            'recursive' => false,
        ],
        'client/custom' => [
            'recursive' => true,
        ],
        'client/modules' => [
            'recursive' => false,
        ],
        'custom/Espo/Custom' => [
            'recursive' => true,
        ],
    ];

    
    protected $defaultPermissions = [
        'dir' => '0755',
        'file' => '0644',
        'user' => null,
        'group' => null,
    ];

    
    protected $writablePermissions = [
        'file' => '0664',
        'dir' => '0775',
    ];

    
    public function __construct(private Manager $fileManager, array $params = null)
    {
        if ($params) {
            foreach ($params as $paramName => $paramValue) {
                switch ($paramName) {
                    case 'defaultPermissions':
                        
                        $this->defaultPermissions = array_merge($this->defaultPermissions, $paramValue);

                        break;
                }
            }
        }
    }
    
    public function getDefaultPermissions(): array
    {
        return $this->defaultPermissions;
    }

    
    public function getWritableMap(): array
    {
        return $this->writableMap;
    }

    
    public function getWritableList(): array
    {
        return array_keys($this->writableMap);
    }

    
    public function getRequiredPermissions(string $path): array
    {
        $permission = $this->getDefaultPermissions();

        foreach ($this->getWritableMap() as $writablePath => $writableOptions) {
            if (!$writableOptions['recursive'] && $path == $writablePath) {
                
                return array_merge($permission, $this->writablePermissions);
            }

            if ($writableOptions['recursive'] && substr($path, 0, strlen($writablePath)) == $writablePath) {
                
                return array_merge($permission, $this->writablePermissions);
            }
        }

        return $permission;
    }

    
    public function setDefaultPermissions(string $path, bool $recurse = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $permission = $this->getRequiredPermissions($path);

        $result = $this->chmod($path, [$permission['file'], $permission['dir']], $recurse);

        if (!empty($permission['user'])) {
            $result &= $this->chown($path, $permission['user'], $recurse);
        }

        if (!empty($permission['group'])) {
            $result &= $this->chgrp($path, $permission['group'], $recurse);
        }

        return (bool) $result;
    }

    
    public function getCurrentPermission(string $filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        
        $fileInfo = stat($filePath);

        return substr(base_convert((string) $fileInfo['mode'], 10, 8), -4);
    }

    
    public function chmod(string $path, $octal, bool $recurse = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        

        $permission = [];

        if (is_array($octal)) {
            $count = 0;

            $rule = ['file', 'dir'];

            foreach ($octal as $key => $val) {
                $pKey = strval($key);

                if (!in_array($pKey, $rule)) {
                    $pKey = $rule[$count];
                }

                if (!empty($pKey)) {
                    $permission[$pKey]= $val;
                }

                $count++;
            }
        }
        else if (is_int((int) $octal)) { 
            $permission = [
                'file' => $octal,
                'dir' => $octal,
            ];
        }

        
        foreach ($permission as $key => $val) {
            if (is_string($val)) {
                $permission[$key] = base_convert($val, 8, 10);
            }
        }

        if (!$recurse) {
            if (is_dir($path)) {
                return $this->chmodReal($path, $permission['dir']);
            }

            return $this->chmodReal($path, $permission['file']);
        }

        return $this->chmodRecurse($path, $permission['file'], $permission['dir']);
    }

    
    protected function chmodRecurse(string $path, $fileOctal = 0644, $dirOctal = 0755): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!is_dir($path)) {
            return $this->chmodReal($path, $fileOctal);
        }

        $result = $this->chmodReal($path, $dirOctal);

        
        $allFiles = $this->fileManager->getFileList($path);

        foreach ($allFiles as $item) {
            $result &= $this->chmodRecurse($path . Util::getSeparator() . $item, $fileOctal, $dirOctal);
        }

        return (bool) $result;
    }

    
    public function chown(string $path, $user = '', bool $recurse = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        if (empty($user)) {
            $user = $this->getDefaultOwner();
        }

        if ($user === false) {
            
            $user = '';
        }

        if (!$recurse) {
            return $this->chownReal($path, $user);
        }

        return $this->chownRecurse($path, $user);
    }

    
    protected function chownRecurse(string $path, $user): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!is_dir($path)) {
            return $this->chownReal($path, $user);
        }

        $result = $this->chownReal($path, $user);

        
        $allFiles = $this->fileManager->getFileList($path);

        foreach ($allFiles as $item) {
            $result &= $this->chownRecurse($path . Util::getSeparator() . $item, $user);
        }

        return (bool) $result;
    }

    
    public function chgrp(string $path, $group = null, bool $recurse = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!isset($group)) {
            $group = $this->getDefaultGroup();
        }

        if ($group === false) {
            
            $group = '';
        }

        if (!$recurse) {
            return $this->chgrpReal($path, $group);
        }

        return $this->chgrpRecurse($path, $group);
    }

    
    protected function chgrpRecurse(string $path, $group): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!is_dir($path)) {
            return $this->chgrpReal($path, $group);
        }

        $result = $this->chgrpReal($path, $group);

        
        $allFiles = $this->fileManager->getFileList($path);

        foreach ($allFiles as $item) {
            $result &= $this->chgrpRecurse($path . Util::getSeparator() . $item, $group);
        }

        return (bool) $result;
    }

    
    protected function chmodReal(string $filename, $mode): bool
    {
        $result = @chmod($filename, $mode);

        if ($result) {
            return true;
        }

        $defaultOwner = $this->getDefaultOwner(true);
        $defaultGroup = $this->getDefaultGroup(true);

        if ($defaultOwner === false) {
            
            $defaultOwner = '';
        }

        if ($defaultGroup === false) {
            
            $defaultGroup = '';
        }

        $this->chown($filename, $defaultOwner);
        $this->chgrp($filename, $defaultGroup);

        return @chmod($filename, $mode);
    }

    
    protected function chownReal(string $path, $user): bool
    {
        return @chown($path, $user);
    }

    
    protected function chgrpReal(string $path, $group): bool
    {
        return @chgrp($path, $group);
    }

    
    public function getDefaultOwner(bool $usePosix = false)
    {
        $defaultPermissions = $this->getDefaultPermissions();

        $owner = $defaultPermissions['user'];

        if (empty($owner) && $usePosix) {
            $owner = function_exists('posix_getuid') ? posix_getuid() : null;
        }

        if (empty($owner)) {
            return false;
        }

        return $owner;
    }

    
    public function getDefaultGroup(bool $usePosix = false)
    {
        $defaultPermissions = $this->getDefaultPermissions();

        $group = $defaultPermissions['group'];

        if (empty($group) && $usePosix) {
            $group = function_exists('posix_getegid') ? posix_getegid() : null;
        }

        if (empty($group)) {
            return false;
        }

        return $group;
    }

    
    public function setMapPermission(): bool
    {
        $this->permissionError = [];
        $this->permissionErrorRules = [];

        $result = true;

        foreach ($this->getWritableMap() as $path => $options) {
            if (!file_exists($path)) {
                continue;
            }

            try {
                $this->chmod($path, $this->writablePermissions, $options['recursive']);
            }
            catch (Throwable $e) {}

            
            $res = is_writable($path);

            if (is_dir($path)) {
                try {
                    $name = uniqid();

                    $res &= $this->fileManager->putContents($path . '/' . $name, 'test');

                    $res &= $this->fileManager->removeFile($name, $path);
                }
                catch (Throwable $e) {
                    $res = false;
                }
            }

            if (!$res) {
                $result = false;

                $this->permissionError[] = $path;
                $this->permissionErrorRules[$path] = $this->writablePermissions;
            }
        }

        return (bool) $result;
    }

    
    public function getLastError()
    {
        return $this->permissionError;
    }

    
    public function getLastErrorRules()
    {
        return $this->permissionErrorRules;
    }

    
    public function arrangePermissionList(array $fileList): array
    {
        $betterList = [];

        foreach ($fileList as $fileName) {
            $pathInfo = pathinfo($fileName);
            
            $dirname = $pathInfo['dirname'] ?? null;

            $currentPath = $fileName;

            if ($this->getSearchCount($dirname, $fileList) > 1) {
                $currentPath = $dirname;
            }

            if (!$this->itemIncludes($currentPath, $betterList)) {
                $betterList[] = $currentPath;
            }
        }

        return $betterList;
    }

    
    protected function getSearchCount(string $search, array $array)
    {
        $searchQuoted = $this->getPregQuote($search);

        $number = 0;

        foreach ($array as $value) {
            if (preg_match('/^' . $searchQuoted . '/', $value)) {
                $number++;
            }
        }

        return $number;
    }

    
    protected function itemIncludes(string $item, array $array): bool
    {
        foreach ($array as $value) {
            $value = $this->getPregQuote($value);

            if (preg_match('/^' . $value . '/', $item)) {
                return true;
            }
        }

        return false;
    }

    
    protected function getPregQuote(string $string): string
    {
        return preg_quote($string, '/-+=.');
    }
}
