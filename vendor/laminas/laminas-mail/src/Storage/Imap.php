<?php

namespace Laminas\Mail\Storage;

use Laminas\Mail;
use Laminas\Mail\Protocol;

use function array_key_exists;
use function array_pop;
use function array_push;
use function count;
use function in_array;
use function is_string;
use function ksort;
use function str_starts_with;
use function strrpos;
use function substr;

use const INF;
use const SORT_STRING;

class Imap extends AbstractStorage implements Folder\FolderInterface, Writable\WritableInterface
{
    
    

    
    protected $protocol;

    
    protected $currentFolder = '';

    
    protected $delimiter;

    
    protected static $knownFlags = [
        '\Passed'   => Mail\Storage::FLAG_PASSED,
        '\Answered' => Mail\Storage::FLAG_ANSWERED,
        '\Seen'     => Mail\Storage::FLAG_SEEN,
        '\Unseen'   => Mail\Storage::FLAG_UNSEEN,
        '\Deleted'  => Mail\Storage::FLAG_DELETED,
        '\Draft'    => Mail\Storage::FLAG_DRAFT,
        '\Flagged'  => Mail\Storage::FLAG_FLAGGED,
    ];

    
    protected static $searchFlags = [
        '\Recent'   => 'RECENT',
        '\Answered' => 'ANSWERED',
        '\Seen'     => 'SEEN',
        '\Unseen'   => 'UNSEEN',
        '\Deleted'  => 'DELETED',
        '\Draft'    => 'DRAFT',
        '\Flagged'  => 'FLAGGED',
    ];

    
    public function countMessages($flags = null)
    {
        if (! $this->currentFolder) {
            throw new Exception\RuntimeException('No selected folder to count');
        }

        if ($flags === null) {
            return count($this->protocol->search(['ALL']));
        }

        $params = [];
        foreach ((array) $flags as $flag) {
            if (isset(static::$searchFlags[$flag])) {
                $params[] = static::$searchFlags[$flag];
            } else {
                $params[] = 'KEYWORD';
                $params[] = $this->protocol->escapeString($flag);
            }
        }
        return count($this->protocol->search($params));
    }

    
    public function getSize($id = 0)
    {
        if ($id) {
            return $this->protocol->fetch('RFC822.SIZE', $id);
        }
        return $this->protocol->fetch('RFC822.SIZE', 1, INF);
    }

    
    public function getMessage($id)
    {
        $data   = $this->protocol->fetch(['FLAGS', 'RFC822.HEADER'], $id);
        $header = $data['RFC822.HEADER'];

        $flags = [];
        foreach ($data['FLAGS'] as $flag) {
            $flags[] = static::$knownFlags[$flag] ?? $flag;
        }

        return new $this->messageClass(['handler' => $this, 'id' => $id, 'headers' => $header, 'flags' => $flags]);
    }

    
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        
        return $this->protocol->fetch('RFC822.HEADER', $id);
    }

    
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        return $this->protocol->fetch('RFC822.TEXT', $id);
    }

    
    public function __construct($params)
    {
        $this->has['flags'] = true;

        if ($params instanceof Protocol\Imap) {
            $this->protocol = $params;
            try {
                $this->selectFolder('INBOX');
            } catch (Exception\ExceptionInterface $e) {
                throw new Exception\RuntimeException('cannot select INBOX, is this a valid transport?', 0, $e);
            }
            return;
        }

        $params = ParamsNormalizer::normalizeParams($params);

        if (! isset($params['user'])) {
            throw new Exception\InvalidArgumentException('need at least user in params');
        }

        $host     = $params['host'] ?? 'localhost';
        $password = $params['password'] ?? '';
        $port     = $params['port'] ?? null;
        $ssl      = $params['ssl'] ?? false;
        $folder   = $params['folder'] ?? 'INBOX';

        if (null !== $port) {
            $port = (int) $port;
        }

        if (! is_string($ssl)) {
            $ssl = (bool) $ssl;
        }

        $this->protocol = new Protocol\Imap();

        if (array_key_exists('novalidatecert', $params)) {
            $this->protocol->setNoValidateCert((bool) $params['novalidatecert']);
        }

        $this->protocol->connect((string) $host, $port, $ssl);
        if (! $this->protocol->login((string) $params['user'], (string) $password)) {
            throw new Exception\RuntimeException('cannot login, user or password wrong');
        }
        $this->selectFolder((string) $folder);
    }

    
    public function close()
    {
        $this->currentFolder = '';
        $this->protocol->logout();
    }

    
    public function noop()
    {
        if (! $this->protocol->noop()) {
            throw new Exception\RuntimeException('could not do nothing');
        }
    }

    
    public function removeMessage($id)
    {
        if (! $this->protocol->store([Mail\Storage::FLAG_DELETED], $id, null, '+')) {
            throw new Exception\RuntimeException('cannot set deleted flag');
        }
        
        if (! $this->protocol->expunge()) {
            throw new Exception\RuntimeException('message marked as deleted, but could not expunge');
        }
    }

    
    public function getUniqueId($id = null)
    {
        if ($id) {
            return $this->protocol->fetch('UID', $id);
        }

        return $this->protocol->fetch('UID', 1, INF);
    }

    
    public function getNumberByUniqueId($id)
    {
        
        $ids = $this->getUniqueId();
        foreach ($ids as $k => $v) {
            if ($v == $id) {
                return $k;
            }
        }

        throw new Exception\InvalidArgumentException('unique id not found');
    }

    
    public function getFolders($rootFolder = null)
    {
        $folders = $this->protocol->listMailbox((string) $rootFolder);
        if (! $folders) {
            throw new Exception\InvalidArgumentException('folder not found');
        }

        ksort($folders, SORT_STRING);
        $root         = new Folder('/', '/', false);
        $stack        = [null];
        $folderStack  = [null];
        $parentFolder = $root;
        $parent       = '';

        foreach ($folders as $globalName => $data) {
            do {
                if (! $parent || str_starts_with($globalName, ! is_string($parent) ? (string) $parent : $parent)) {
                    $pos = strrpos($globalName, (string) $data['delim']);
                    if ($pos === false) {
                        $localName = $globalName;
                    } else {
                        $localName = substr($globalName, $pos + 1);
                    }
                    $selectable = ! $data['flags'] || ! in_array('\\Noselect', $data['flags']);

                    array_push($stack, $parent);
                    $parent                   = $globalName . $data['delim'];
                    $folder                   = new Folder($localName, $globalName, $selectable);
                    $parentFolder->$localName = $folder;
                    array_push($folderStack, $parentFolder);
                    $parentFolder    = $folder;
                    $this->delimiter = $data['delim'];
                    break;
                } elseif ($stack) {
                    $parent       = array_pop($stack);
                    $parentFolder = array_pop($folderStack);
                }
            } while ($stack);
            if (! $stack) {
                throw new Exception\RuntimeException('error while constructing folder tree');
            }
        }

        return $root;
    }

    
    public function selectFolder($globalName)
    {
        $this->currentFolder = (string) $globalName;
        if (! $this->protocol->select($this->currentFolder)) {
            $this->currentFolder = '';
            throw new Exception\RuntimeException('cannot change folder, maybe it does not exist');
        }
    }

    
    public function getCurrentFolder()
    {
        return $this->currentFolder;
    }

    
    public function createFolder($name, $parentFolder = null)
    {
        
        if ($parentFolder instanceof Folder) {
            $folder = $parentFolder->getGlobalName() . '/' . $name;
        } elseif ($parentFolder !== null) {
            $folder = $parentFolder . '/' . $name;
        } else {
            $folder = $name;
        }

        if (! $this->protocol->create($folder)) {
            throw new Exception\RuntimeException('cannot create folder');
        }
    }

    
    public function removeFolder($name)
    {
        if ($name instanceof Folder) {
            $name = $name->getGlobalName();
        }

        if (! $this->protocol->delete($name)) {
            throw new Exception\RuntimeException('cannot delete folder');
        }
    }

    
    public function renameFolder($oldName, $newName)
    {
        if ($oldName instanceof Folder) {
            $oldName = $oldName->getGlobalName();
        }

        if (! $this->protocol->rename($oldName, $newName)) {
            throw new Exception\RuntimeException('cannot rename folder');
        }
    }

    
    public function appendMessage($message, $folder = null, $flags = null)
    {
        if ($folder === null) {
            $folder = $this->currentFolder;
        }

        if ($flags === null) {
            $flags = [Mail\Storage::FLAG_SEEN];
        }

        
        if (! $this->protocol->append($folder, $message, $flags)) {
            throw new Exception\RuntimeException(
                'cannot create message, please check if the folder exists and your flags'
            );
        }
    }

    
    public function copyMessage($id, $folder)
    {
        if (! $this->protocol->copy($folder, $id)) {
            throw new Exception\RuntimeException('cannot copy message, does the folder exist?');
        }
    }

    
    public function moveMessage($id, $folder)
    {
        $this->copyMessage($id, $folder);
        $this->removeMessage($id);
    }

    
    public function setFlags($id, $flags)
    {
        if (! $this->protocol->store($flags, $id)) {
            throw new Exception\RuntimeException(
                'cannot set flags, have you tried to set the recent flag or special chars?'
            );
        }
    }

    
    public function delimiter()
    {
        if (! isset($this->delimiter)) {
            $this->getFolders();
        }
        return $this->delimiter;
    }
}
