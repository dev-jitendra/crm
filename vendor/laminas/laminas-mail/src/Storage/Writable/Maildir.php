<?php

namespace Laminas\Mail\Storage\Writable;

use Laminas\Mail\Exception as MailException;
use Laminas\Mail\Storage;
use Laminas\Mail\Storage\Exception as StorageException;
use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Exception\InvalidArgumentException;
use Laminas\Mail\Storage\Exception\RuntimeException;
use Laminas\Mail\Storage\Folder;
use Laminas\Stdlib\ErrorHandler;
use RecursiveIteratorIterator;

use function array_flip;
use function array_keys;
use function array_search;
use function array_values;
use function closedir;
use function copy;
use function dirname;
use function explode;
use function fclose;
use function fgets;
use function file_exists;
use function file_put_contents;
use function filemtime;
use function filesize;
use function fopen;
use function fread;
use function fwrite;
use function get_resource_type;
use function getmypid;
use function implode;
use function is_array;
use function is_dir;
use function is_file;
use function is_numeric;
use function is_resource;
use function link;
use function microtime;
use function mkdir;
use function opendir;
use function php_uname;
use function readdir;
use function rename;
use function rmdir;
use function rtrim;
use function sleep;
use function str_contains;
use function str_starts_with;
use function stream_copy_to_stream;
use function strlen;
use function strpos;
use function strrpos;
use function strtok;
use function substr;
use function time;
use function trim;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const E_WARNING;
use const FILE_APPEND;

class Maildir extends Folder\Maildir implements WritableInterface
{
    

    
    protected $quota;

    
    public static function initMaildir($dir)
    {
        if (file_exists($dir)) {
            if (! is_dir($dir)) {
                throw new StorageException\InvalidArgumentException('maildir must be a directory if already exists');
            }
        } else {
            ErrorHandler::start();
            $test  = mkdir($dir);
            $error = ErrorHandler::stop();
            if (! $test) {
                $dir = dirname($dir);
                if (! file_exists($dir)) {
                    throw new StorageException\InvalidArgumentException("parent $dir not found", 0, $error);
                } elseif (! is_dir($dir)) {
                    throw new StorageException\InvalidArgumentException("parent $dir not a directory", 0, $error);
                }

                throw new StorageException\RuntimeException('cannot create maildir', 0, $error);
            }
        }

        foreach (['cur', 'tmp', 'new'] as $subdir) {
            ErrorHandler::start();
            $test  = mkdir($dir . DIRECTORY_SEPARATOR . $subdir);
            $error = ErrorHandler::stop();
            if (! $test) {
                
                if (! file_exists($dir . DIRECTORY_SEPARATOR . $subdir)) {
                    throw new StorageException\RuntimeException('could not create subdir ' . $subdir, 0, $error);
                }
            }
        }
    }

    
    public function __construct($params)
    {
        if (is_array($params)) {
            $params = (object) $params;
        }

        if (
            ! empty($params->create)
            && isset($params->dirname)
            && ! file_exists($params->dirname . DIRECTORY_SEPARATOR . 'cur')
        ) {
            self::initMaildir($params->dirname);
        }

        parent::__construct($params);
    }

    
    public function createFolder($name, $parentFolder = null)
    {
        if ($parentFolder instanceof Folder) {
            $folder = $parentFolder->getGlobalName() . $this->delim . $name;
        } elseif ($parentFolder !== null) {
            $folder = rtrim($parentFolder, $this->delim) . $this->delim . $name;
        } else {
            $folder = $name;
        }

        $folder = trim($folder, $this->delim);

        
        $exists = null;
        try {
            $exists = $this->getFolders($folder);
        } catch (MailException\ExceptionInterface) {
            
        }
        if ($exists) {
            throw new StorageException\RuntimeException('folder already exists');
        }

        if (str_contains($folder, $this->delim . $this->delim)) {
            throw new StorageException\RuntimeException('invalid name - folder parts may not be empty');
        }

        if (str_starts_with($folder, 'INBOX' . $this->delim)) {
            $folder = substr($folder, 6);
        }

        $fulldir = $this->rootdir . '.' . $folder;

        
        if (
            str_contains($folder, DIRECTORY_SEPARATOR) || str_contains($folder, '/')
            || dirname($fulldir) . DIRECTORY_SEPARATOR != $this->rootdir
        ) {
            throw new StorageException\RuntimeException('invalid name - no directory separator allowed in folder name');
        }

        
        $parent = null;
        if (strpos($folder, $this->delim)) {
            
            $parent = substr($folder, 0, strrpos($folder, $this->delim));
            try {
                $this->getFolders($parent);
            } catch (MailException\ExceptionInterface) {
                
                $this->createFolder($parent);
            }
        }

        ErrorHandler::start();
        if (! mkdir($fulldir) || ! mkdir($fulldir . DIRECTORY_SEPARATOR . 'cur')) {
            $error = ErrorHandler::stop();
            throw new StorageException\RuntimeException(
                'error while creating new folder, may be created incompletely',
                0,
                $error
            );
        }
        ErrorHandler::stop();

        mkdir($fulldir . DIRECTORY_SEPARATOR . 'new');
        mkdir($fulldir . DIRECTORY_SEPARATOR . 'tmp');

        $localName                             = $parent ? substr($folder, strlen($parent) + 1) : $folder;
        $this->getFolders($parent)->$localName = new Folder($localName, $folder, true);

        return $fulldir;
    }

    
    public function removeFolder($name)
    {
        
        
        
        
        

        if ($name instanceof Folder) {
            $name = $name->getGlobalName();
        }

        $name = trim($name, $this->delim);
        if (str_starts_with($name, 'INBOX' . $this->delim)) {
            $name = substr($name, 6);
        }

        
        if (! $this->getFolders($name)->isLeaf()) {
            throw new StorageException\RuntimeException('delete children first');
        }

        if ($name == 'INBOX' || $name == DIRECTORY_SEPARATOR || $name == '/') {
            throw new StorageException\RuntimeException('wont delete INBOX');
        }

        if ($name == $this->getCurrentFolder()) {
            throw new StorageException\RuntimeException('wont delete selected folder');
        }

        foreach (['tmp', 'new', 'cur', '.'] as $subdir) {
            $dir = $this->rootdir . '.' . $name . DIRECTORY_SEPARATOR . $subdir;
            if (! file_exists($dir)) {
                continue;
            }
            $dh = opendir($dir);
            if (! $dh) {
                throw new StorageException\RuntimeException("error opening $subdir");
            }
            while (($entry = readdir($dh)) !== false) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                if (! unlink($dir . DIRECTORY_SEPARATOR . $entry)) {
                    throw new StorageException\RuntimeException("error cleaning $subdir");
                }
            }
            closedir($dh);
            if ($subdir !== '.') {
                if (! rmdir($dir)) {
                    throw new StorageException\RuntimeException("error removing $subdir");
                }
            }
        }

        if (! rmdir($this->rootdir . '.' . $name)) {
            
            mkdir($this->rootdir . '.' . $name . DIRECTORY_SEPARATOR . 'cur');
            throw new StorageException\RuntimeException("error removing maindir");
        }

        $parent    = strpos($name, $this->delim) ? substr($name, 0, strrpos($name, $this->delim)) : null;
        $localName = $parent ? substr($name, strlen($parent) + 1) : $name;
        unset($this->getFolders($parent)->$localName);
    }

    
    public function renameFolder($oldName, $newName)
    {
        

        if ($oldName instanceof Folder) {
            $oldName = $oldName->getGlobalName();
        }

        $oldName = trim($oldName, $this->delim);
        if (str_starts_with($oldName, 'INBOX' . $this->delim)) {
            $oldName = substr($oldName, 6);
        }

        $newName = trim($newName, $this->delim);
        if (str_starts_with($newName, 'INBOX' . $this->delim)) {
            $newName = substr($newName, 6);
        }

        if (str_starts_with($newName, $oldName . $this->delim)) {
            throw new StorageException\RuntimeException('new folder cannot be a child of old folder');
        }

        
        $folder = $this->getFolders($oldName);

        if ($oldName == 'INBOX' || $oldName == DIRECTORY_SEPARATOR || $oldName == '/') {
            throw new StorageException\RuntimeException('wont rename INBOX');
        }

        if ($oldName == $this->getCurrentFolder()) {
            throw new StorageException\RuntimeException('wont rename selected folder');
        }

        $newdir = $this->createFolder($newName);

        if (! $folder->isLeaf()) {
            foreach ($folder as $k => $v) {
                $this->renameFolder($v->getGlobalName(), $newName . $this->delim . $k);
            }
        }

        $olddir = $this->rootdir . '.' . $folder;
        foreach (['tmp', 'new', 'cur'] as $subdir) {
            $subdir = DIRECTORY_SEPARATOR . $subdir;
            if (! file_exists($olddir . $subdir)) {
                continue;
            }
            
            if (! rename($olddir . $subdir, $newdir . $subdir)) {
                throw new StorageException\RuntimeException('error while moving ' . $subdir);
            }
        }
        
        mkdir($olddir . DIRECTORY_SEPARATOR . 'cur');
        $this->removeFolder($oldName);
    }

    
    protected function createUniqueId()
    {
        $id  = '';
        $id .= microtime(true);
        $id .= '.' . getmypid();
        $id .= '.' . php_uname('n');

        return $id;
    }

    
    protected function createTmpFile($folder = 'INBOX')
    {
        if ($folder == 'INBOX') {
            $tmpdir = $this->rootdir . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        } else {
            $tmpdir = $this->rootdir . '.' . $folder . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        }
        if (! file_exists($tmpdir)) {
            if (! mkdir($tmpdir)) {
                throw new StorageException\RuntimeException('problems creating tmp dir');
            }
        }

        
        
        
        
        
        $maxTries = 5;
        for ($i = 0; $i < $maxTries; ++$i) {
            $uniq = $this->createUniqueId();
            if (! file_exists($tmpdir . $uniq)) {
                
                
                
                $fh = fopen($tmpdir . $uniq, 'w');
                if (! $fh) {
                    throw new StorageException\RuntimeException('could not open temp file');
                }
                break;
            }
            sleep(1);
        }

        if (! $fh) {
            throw new StorageException\RuntimeException(
                "tried {$maxTries} unique ids for a temp file, but all were taken - giving up"
            );
        }

        return [
            'dirname'  => $this->rootdir . '.' . $folder,
            'uniq'     => $uniq,
            'filename' => $tmpdir . $uniq,
            'handle'   => $fh,
        ];
    }

    
    protected function getInfoString(&$flags)
    {
        
        $wantedFlags = array_flip($flags);
        if (isset($wantedFlags[Storage::FLAG_RECENT])) {
            throw new StorageException\InvalidArgumentException('recent flag may not be set');
        }

        $info  = ':2,';
        $flags = [];
        foreach (Storage\Maildir::$knownFlags as $char => $flag) {
            if (! isset($wantedFlags[$flag])) {
                continue;
            }
            $info        .= $char;
            $flags[$char] = $flag;
            unset($wantedFlags[$flag]);
        }

        if (! empty($wantedFlags)) {
            $wantedFlags = implode(', ', array_keys($wantedFlags));
            throw new StorageException\InvalidArgumentException('unknown flag(s): ' . $wantedFlags);
        }

        return $info;
    }

    
    public function appendMessage($message, $folder = null, $flags = null, $recent = false)
    {
        if ($this->quota && $this->checkQuota()) {
            throw new StorageException\RuntimeException('storage is over quota!');
        }

        if ($folder === null) {
            $folder = $this->currentFolder;
        }

        if (! $folder instanceof Folder) {
            $folder = $this->getFolders($folder);
        }

        if ($flags === null) {
            $flags = [Storage::FLAG_SEEN];
        }
        $info     = $this->getInfoString($flags);
        $tempFile = $this->createTmpFile($folder->getGlobalName());

        
        if (is_resource($message) && get_resource_type($message) == 'stream') {
            stream_copy_to_stream($message, $tempFile['handle']);
        } else {
            fwrite($tempFile['handle'], $message);
        }
        fclose($tempFile['handle']);

        
        $size = filesize($tempFile['filename']);
        if ($size !== false) {
            $info = ',S=' . $size . $info;
        }
        $newFilename  = $tempFile['dirname'] . DIRECTORY_SEPARATOR;
        $newFilename .= $recent ? 'new' : 'cur';
        $newFilename .= DIRECTORY_SEPARATOR . $tempFile['uniq'] . $info;

        
        $exception = null;

        if (! link($tempFile['filename'], $newFilename)) {
            $exception = new StorageException\RuntimeException('cannot link message file to final dir');
        }

        ErrorHandler::start(E_WARNING);
        unlink($tempFile['filename']);
        ErrorHandler::stop();

        if ($exception) {
            throw $exception;
        }

        $this->files[] = [
            'uniq'     => $tempFile['uniq'],
            'flags'    => $flags,
            'filename' => $newFilename,
        ];
        if ($this->quota) {
            $this->addQuotaEntry((int) $size, 1);
        }
    }

    
    public function copyMessage($id, $folder)
    {
        if ($this->quota && $this->checkQuota()) {
            throw new StorageException\RuntimeException('storage is over quota!');
        }

        if (! $folder instanceof Folder) {
            $folder = $this->getFolders($folder);
        }

        $filedata = $this->getFileData($id);
        $oldFile  = $filedata['filename'];
        $flags    = $filedata['flags'];

        
        while (($key = array_search(Storage::FLAG_RECENT, $flags)) !== false) {
            unset($flags[$key]);
        }
        $info = $this->getInfoString($flags);

        
        $tempFile = $this->createTmpFile($folder->getGlobalName());
        
        fclose($tempFile['handle']);

        
        $size = filesize($oldFile);
        if ($size !== false) {
            $info = ',S=' . $size . $info;
        }

        $newFile = $tempFile['dirname'] . DIRECTORY_SEPARATOR . 'cur' . DIRECTORY_SEPARATOR . $tempFile['uniq'] . $info;

        
        $exception = null;

        if (! copy($oldFile, $tempFile['filename'])) {
            $exception = new StorageException\RuntimeException('cannot copy message file');
        } elseif (! link($tempFile['filename'], $newFile)) {
            $exception = new StorageException\RuntimeException('cannot link message file to final dir');
        }

        ErrorHandler::start(E_WARNING);
        unlink($tempFile['filename']);
        ErrorHandler::stop();

        if ($exception) {
            throw $exception;
        }

        if (
            $folder->getGlobalName() == $this->currentFolder
            || ($this->currentFolder == 'INBOX' && $folder->getGlobalName() == '/')
        ) {
            $this->files[] = [
                'uniq'     => $tempFile['uniq'],
                'flags'    => $flags,
                'filename' => $newFile,
            ];
        }

        if ($this->quota) {
            $this->addQuotaEntry((int) $size, 1);
        }
    }

    
    public function moveMessage($id, $folder)
    {
        if (! $folder instanceof Folder) {
            $folder = $this->getFolders($folder);
        }

        if (
            $folder->getGlobalName() == $this->currentFolder
            || ($this->currentFolder == 'INBOX' && $folder->getGlobalName() == '/')
        ) {
            throw new StorageException\RuntimeException('target is current folder');
        }

        $filedata = $this->getFileData($id);
        $oldFile  = $filedata['filename'];
        $flags    = $filedata['flags'];

        
        while (($key = array_search(Storage::FLAG_RECENT, $flags)) !== false) {
            unset($flags[$key]);
        }
        $info = $this->getInfoString($flags);

        
        $tempFile = $this->createTmpFile($folder->getGlobalName());
        fclose($tempFile['handle']);

        
        $size = filesize($oldFile);
        if ($size !== false) {
            $info = ',S=' . $size . $info;
        }

        $newFile = $tempFile['dirname'] . DIRECTORY_SEPARATOR . 'cur' . DIRECTORY_SEPARATOR . $tempFile['uniq'] . $info;

        
        $exception = null;

        if (! rename($oldFile, $newFile)) {
            $exception = new StorageException\RuntimeException('cannot move message file');
        }

        ErrorHandler::start(E_WARNING);
        unlink($tempFile['filename']);
        ErrorHandler::stop();

        if ($exception) {
            throw $exception;
        }

        unset($this->files[$id - 1]);
        
        $this->files = array_values($this->files);
    }

    
    public function setFlags($id, $flags)
    {
        $info     = $this->getInfoString($flags);
        $filedata = $this->getFileData($id);

        
        
        $newFilename = dirname($filedata['filename'], 2)
            . DIRECTORY_SEPARATOR
            . 'cur'
            . DIRECTORY_SEPARATOR
            . "$filedata[uniq]$info";

        ErrorHandler::start();
        $test  = rename($filedata['filename'], $newFilename);
        $error = ErrorHandler::stop();
        if (! $test) {
            throw new StorageException\RuntimeException('cannot rename file', 0, $error);
        }

        $filedata['flags']    = $flags;
        $filedata['filename'] = $newFilename;

        $this->files[$id - 1] = $filedata;
    }

    
    public function removeMessage($id)
    {
        $filename = $this->getFileData($id, 'filename');

        if ($this->quota) {
            $size = filesize($filename);
        }

        ErrorHandler::start();
        $test  = unlink($filename);
        $error = ErrorHandler::stop();
        if (! $test) {
            throw new StorageException\RuntimeException('cannot remove message', 0, $error);
        }
        unset($this->files[$id - 1]);
        
        $this->files = array_values($this->files);
        if ($this->quota) {
            $this->addQuotaEntry(0 - (int) $size, -1);
        }
    }

    
    public function setQuota($value)
    {
        $this->quota = $value;
    }

    
    public function getQuota($fromStorage = false)
    {
        if ($fromStorage) {
            ErrorHandler::start(E_WARNING);
            $fh    = fopen($this->rootdir . 'maildirsize', 'r');
            $error = ErrorHandler::stop();
            if (! $fh) {
                throw new StorageException\RuntimeException('cannot open maildirsize', 0, $error);
            }
            $definition = fgets($fh);
            fclose($fh);
            $definition = explode(',', trim($definition));
            $quota      = [];
            foreach ($definition as $member) {
                $key = $member[strlen($member) - 1];
                if ($key == 'S' || $key == 'C') {
                    $key = $key == 'C' ? 'count' : 'size';
                }
                $quota[$key] = substr($member, 0, -1);
            }
            return $quota;
        }

        return $this->quota;
    }

    
    protected function calculateMaildirsize()
    {
        $timestamps = [];
        $messages   = 0;
        $totalSize  = 0;

        if (is_array($this->quota)) {
            $quota = $this->quota;
        } else {
            try {
                $quota = $this->getQuota(true);
            } catch (StorageException\ExceptionInterface $e) {
                throw new StorageException\RuntimeException('no quota definition found', 0, $e);
            }
        }

        $folders = new RecursiveIteratorIterator($this->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($folders as $folder) {
            $subdir = $folder->getGlobalName();
            if ($subdir == 'INBOX') {
                $subdir = '';
            } else {
                $subdir = '.' . $subdir;
            }
            if ($subdir == 'Trash') {
                continue;
            }

            foreach (['cur', 'new'] as $subsubdir) {
                $dirname = $this->rootdir . $subdir . DIRECTORY_SEPARATOR . $subsubdir . DIRECTORY_SEPARATOR;
                if (! file_exists($dirname)) {
                    continue;
                }
                
                
                $timestamps[$dirname] = filemtime($dirname);

                $dh = opendir($dirname);
                
                
                if (! $dh) {
                    continue;
                }

                while (($entry = readdir()) !== false) {
                    if ($entry[0] == '.' || ! is_file($dirname . $entry)) {
                        continue;
                    }

                    if (strpos($entry, ',S=')) {
                        strtok($entry, '=');
                        $filesize = strtok(':');
                        if (is_numeric($filesize)) {
                            $totalSize += $filesize;
                            ++$messages;
                            continue;
                        }
                    }
                    $size = filesize($dirname . $entry);
                    if ($size === false) {
                        
                        continue;
                    }
                    $totalSize += $size;
                    ++$messages;
                }
            }
        }

        $tmp        = $this->createTmpFile();
        $fh         = $tmp['handle'];
        $definition = [];
        foreach ($quota as $type => $value) {
            if ($type == 'size' || $type == 'count') {
                $type = $type == 'count' ? 'C' : 'S';
            }
            $definition[] = $value . $type;
        }
        $definition = implode(',', $definition);
        fwrite($fh, "$definition\n");
        fwrite($fh, "$totalSize $messages\n");
        fclose($fh);
        rename($tmp['filename'], $this->rootdir . 'maildirsize');
        foreach ($timestamps as $dir => $timestamp) {
            if ($timestamp < filemtime($dir)) {
                unlink($this->rootdir . 'maildirsize');
                break;
            }
        }

        return [
            'size'  => $totalSize,
            'count' => $messages,
            'quota' => $quota,
        ];
    }

    
    protected function calculateQuota($forceRecalc = false)
    {
        $fh          = null;
        $totalSize   = 0;
        $messages    = 0;
        $maildirsize = '';
        if (
            ! $forceRecalc
            && file_exists($this->rootdir . 'maildirsize')
            && filesize($this->rootdir . 'maildirsize') < 5120
        ) {
            $fh = fopen($this->rootdir . 'maildirsize', 'r');
        }
        if ($fh) {
            $maildirsize = fread($fh, 5120);
            if (strlen($maildirsize) >= 5120) {
                fclose($fh);
                $fh          = null;
                $maildirsize = '';
            }
        }
        if (! $fh) {
            $result    = $this->calculateMaildirsize();
            $totalSize = $result['size'];
            $messages  = $result['count'];
            $quota     = $result['quota'];
        } else {
            $maildirsize = explode("\n", $maildirsize);
            if (is_array($this->quota)) {
                $quota = $this->quota;
            } else {
                $definition = explode(',', $maildirsize[0]);
                $quota      = [];
                foreach ($definition as $member) {
                    $key = $member[strlen($member) - 1];
                    if ($key == 'S' || $key == 'C') {
                        $key = $key == 'C' ? 'count' : 'size';
                    }
                    $quota[$key] = substr($member, 0, -1);
                }
            }
            unset($maildirsize[0]);
            foreach ($maildirsize as $line) {
                [$size, $count] = explode(' ', trim($line));
                $totalSize     += $size;
                $messages      += $count;
            }
        }

        $overQuota = false;
        $overQuota = $overQuota || (isset($quota['size']) && $totalSize > $quota['size']);
        $overQuota = $overQuota || (isset($quota['count']) && $messages > $quota['count']);
        
        
        
        
        if ($overQuota && ($maildirsize || filemtime($this->rootdir . 'maildirsize') > time() - 900)) {
            $result    = $this->calculateMaildirsize();
            $totalSize = $result['size'];
            $messages  = $result['count'];
            $quota     = $result['quota'];
            $overQuota = false;
            $overQuota = $overQuota || (isset($quota['size']) && $totalSize > $quota['size']);
            $overQuota = $overQuota || (isset($quota['count']) && $messages > $quota['count']);
        }

        if ($fh) {
            
            fclose($fh);
        }

        return [
            'size'       => $totalSize,
            'count'      => $messages,
            'quota'      => $quota,
            'over_quota' => $overQuota,
        ];
    }

    
    protected function addQuotaEntry($size, $count = 1)
    {
        
            
        
        file_put_contents($this->rootdir . 'maildirsize', "$size $count\n", FILE_APPEND);
    }

    
    public function checkQuota($detailedResponse = false, $forceRecalc = false)
    {
        $result = $this->calculateQuota($forceRecalc);
        return $detailedResponse ? $result : $result['over_quota'];
    }
}
