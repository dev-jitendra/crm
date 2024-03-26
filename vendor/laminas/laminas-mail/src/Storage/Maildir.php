<?php

namespace Laminas\Mail\Storage;

use Laminas\Mail;
use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Message\File;
use Laminas\Stdlib\ErrorHandler;

use function array_flip;
use function closedir;
use function count;
use function ctype_digit;
use function explode;
use function fclose;
use function feof;
use function fgets;
use function file_exists;
use function filesize;
use function fopen;
use function is_array;
use function is_dir;
use function is_file;
use function is_subclass_of;
use function opendir;
use function readdir;
use function sprintf;
use function str_contains;
use function strcmp;
use function stream_get_contents;
use function strlen;
use function substr;
use function trim;
use function usort;

use const E_WARNING;

class Maildir extends AbstractStorage
{
    
    protected $messageClass = File::class;

    
    protected $files = [];

    
    protected static $knownFlags = [
        'D' => Mail\Storage::FLAG_DRAFT,
        'F' => Mail\Storage::FLAG_FLAGGED,
        'P' => Mail\Storage::FLAG_PASSED,
        'R' => Mail\Storage::FLAG_ANSWERED,
        'S' => Mail\Storage::FLAG_SEEN,
        'T' => Mail\Storage::FLAG_DELETED,
    ];

    

    
    public function countMessages($flags = null)
    {
        if ($flags === null) {
            return count($this->files);
        }

        $count = 0;
        if (! is_array($flags)) {
            foreach ($this->files as $file) {
                if (isset($file['flaglookup'][$flags])) {
                    ++$count;
                }
            }
            return $count;
        }

        $flags = array_flip($flags);
        foreach ($this->files as $file) {
            foreach ($flags as $flag => $v) {
                if (! isset($file['flaglookup'][$flag])) {
                    continue 2;
                }
            }
            ++$count;
        }
        return $count;
    }

    
    protected function getFileData($id, $field = null)
    {
        if (! isset($this->files[$id - 1])) {
            throw new Exception\InvalidArgumentException('id does not exist');
        }

        if (! $field) {
            return $this->files[$id - 1];
        }

        if (! isset($this->files[$id - 1][$field])) {
            throw new Exception\InvalidArgumentException('field does not exist');
        }

        return $this->files[$id - 1][$field];
    }

    
    public function getSize($id = null)
    {
        if ($id !== null) {
            $filedata = $this->getFileData($id);
            return $filedata['size'] ?? filesize($filedata['filename']);
        }

        $result = [];
        foreach ($this->files as $num => $data) {
            $result[$num + 1] = $data['size'] ?? filesize($data['filename']);
        }

        return $result;
    }

    
    public function getMessage($id)
    {
        
        if (
            trim($this->messageClass, '\\') === File::class
            || is_subclass_of($this->messageClass, File::class)
        ) {
            return new $this->messageClass([
                'file'  => $this->getFileData($id, 'filename'),
                'flags' => $this->getFileData($id, 'flags'),
            ]);
        }

        return new $this->messageClass([
            'handler' => $this,
            'id'      => $id,
            'headers' => $this->getRawHeader($id),
            'flags'   => $this->getFileData($id, 'flags'),
        ]);
    }

    
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        $fh = fopen($this->getFileData($id, 'filename'), 'r');

        $content = '';
        while (! feof($fh)) {
            $line = fgets($fh);
            if (! trim($line)) {
                break;
            }
            $content .= $line;
        }

        fclose($fh);
        return $content;
    }

    
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        $fh = fopen($this->getFileData($id, 'filename'), 'r');

        while (! feof($fh)) {
            $line = fgets($fh);
            if (! trim($line)) {
                break;
            }
        }

        $content = stream_get_contents($fh);
        fclose($fh);
        return $content;
    }

    
    public function __construct($params)
    {
        $params = ParamsNormalizer::normalizeParams($params);

        if (! isset($params['dirname'])) {
            throw new Exception\InvalidArgumentException('no dirname provided in params');
        }

        $dirname = (string) $params['dirname'];

        if (! is_dir($dirname)) {
            throw new Exception\InvalidArgumentException(sprintf('Maildir "%s" is not a directory', $dirname));
        }

        if (! $this->isMaildir($dirname)) {
            throw new Exception\InvalidArgumentException('invalid maildir given');
        }

        $this->has['top']   = true;
        $this->has['flags'] = true;
        $this->openMaildir($dirname);
    }

    
    protected function isMaildir($dirname)
    {
        if (file_exists($dirname . '/new') && ! is_dir($dirname . '/new')) {
            return false;
        }
        if (file_exists($dirname . '/tmp') && ! is_dir($dirname . '/tmp')) {
            return false;
        }
        return is_dir($dirname . '/cur');
    }

    
    protected function openMaildir($dirname)
    {
        if ($this->files) {
            $this->close();
        }

        ErrorHandler::start(E_WARNING);
        $dh    = opendir($dirname . '/cur/');
        $error = ErrorHandler::stop();
        if (! $dh) {
            throw new Exception\RuntimeException('cannot open maildir', 0, $error);
        }
        $this->getMaildirFiles($dh, $dirname . '/cur/');
        closedir($dh);

        ErrorHandler::start(E_WARNING);
        $dh    = opendir($dirname . '/new/');
        $error = ErrorHandler::stop();
        if (! $dh) {
            throw new Exception\RuntimeException('cannot read recent mails in maildir', 0, $error);
        }

        $this->getMaildirFiles($dh, $dirname . '/new/', [Mail\Storage::FLAG_RECENT]);
        closedir($dh);
    }

    
    protected function getMaildirFiles($dh, $dirname, $defaultFlags = [])
    {
        while (($entry = readdir($dh)) !== false) {
            if ($entry[0] == '.' || ! is_file($dirname . $entry)) {
                continue;
            }

            if (str_contains($entry, ':')) {
                [$uniq, $info] = explode(':', $entry, 2);
            } else {
                $uniq = $entry;
                $info = '';
            }

            if (str_contains($uniq, ',')) {
                [, $size] = explode(',', $uniq, 2);
            } else {
                $size = '';
            }

            if (strlen($size) >= 2 && $size[0] === 'S' && $size[1] === '=') {
                $size = substr($size, 2);
            }

            if (! ctype_digit($size)) {
                $size = null;
            }

            if (str_contains($info, ',')) {
                [$version, $flags] = explode(',', $info, 2);
            } else {
                $version = $info;
                $flags   = '';
            }

            if ($version !== '2') {
                $flags = '';
            }

            $namedFlags = $defaultFlags;
            $length     = strlen($flags);
            for ($i = 0; $i < $length; ++$i) {
                $flag              = $flags[$i];
                $namedFlags[$flag] = static::$knownFlags[$flag] ?? $flag;
            }

            $data = [
                'uniq'       => $uniq,
                'flags'      => $namedFlags,
                'flaglookup' => array_flip($namedFlags),
                'filename'   => $dirname . $entry,
            ];
            if ($size !== null) {
                $data['size'] = (int) $size;
            }
            $this->files[] = $data;
        }

        usort($this->files, static fn($a, $b): int => strcmp($a['filename'], $b['filename']));
    }

    
    public function close()
    {
        $this->files = [];
    }

    
    public function noop()
    {
        return true;
    }

    
    public function removeMessage($id)
    {
        throw new Exception\RuntimeException('maildir is (currently) read-only');
    }

    
    public function getUniqueId($id = null)
    {
        if ($id) {
            return $this->getFileData($id, 'uniq');
        }

        $ids = [];
        foreach ($this->files as $num => $file) {
            $ids[$num + 1] = $file['uniq'];
        }
        return $ids;
    }

    
    public function getNumberByUniqueId($id)
    {
        foreach ($this->files as $num => $file) {
            if ($file['uniq'] == $id) {
                return $num + 1;
            }
        }

        throw new Exception\InvalidArgumentException('unique id not found');
    }
}
