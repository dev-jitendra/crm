<?php


class SystemHelper extends \Espo\Core\Utils\System
{
    protected $config;

    protected $mainConfig;

    protected $apiPath;

    protected $modRewriteUrl = '/';

    protected $writableDir = 'data';

    protected $combineOperator = '&&';

    protected $writableMap;

    public function __construct()
    {
        $this->config = include('config.php');

        if (file_exists('data/config.php')) {
            $this->mainConfig = include('data/config.php');
        }

        $this->apiPath = $this->config['apiPath'];

        $permission = new \Espo\Core\Utils\File\Permission(new \Espo\Core\Utils\File\Manager());
        $this->writableMap = $permission->getWritableMap();
    }

    protected function getMainConfig($optionName, $returns = null)
    {
        if (isset($this->mainConfig[$optionName])) {
            return $this->mainConfig[$optionName];
        }

        return $returns;
    }

    public function initWritable()
    {
        if (is_writable($this->writableDir)) {
            return true;
        }

        return false;
    }

    public function getWritableDir()
    {
        return $this->writableDir;
    }

    public function getBaseUrl()
    {
        $pageUrl = 'http:

        if (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') {
            $pageUrl = 'https:
        }

        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == 'on') {
            $pageUrl = 'https:
        }

        if ($_SERVER["SERVER_PORT"] == '443') {
            $pageUrl = 'https:
        }

        if (in_array($_SERVER["SERVER_PORT"], ['80', '443'])) {
            $pageUrl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        } else {
            $pageUrl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        }

        $baseUrl = str_ireplace('/install/index.php', '', $pageUrl);

        return $baseUrl;
    }

    public function getApiPath()
    {
        return $this->apiPath;
    }

    public function getModRewriteUrl()
    {
        return $this->apiPath . $this->modRewriteUrl;
    }

    public function getChownCommand($path, $isSudo = false, $isCd = true)
    {
        $path = empty($path) ? '.' : $path;
        if (is_array($path)) {
            $path = implode(' ', $path);
        }

        $owner = function_exists('posix_getuid') ? posix_getuid() : null;
        $group = function_exists('posix_getegid') ? posix_getegid() : null;

        $sudoStr = $isSudo ? 'sudo ' : '';

        if (empty($owner) || empty($group)) {
            return null;
        }

        $cd = '';
        if ($isCd) {
            $cd = $this->getCd(true);
        }

        return $cd.$sudoStr.'chown -R '.$owner.':'.$group.' '.$path;
    }

    public function getChmodCommand($path, $permissions = ['755'], $isRecursive = true, $isSudo = false, $isFile = null)
    {
        $path = empty($path) ? '.' : $path;
        if (is_array($path)) {
            $path = implode(' ', $path);
        }

        $sudoStr = $isSudo ? 'sudo ' : '';

        if (is_string($permissions)) {
            $permissions = (array) $permissions;
        }

        if (!isset($isFile) && count($permissions) == 1) {
            if ($isRecursive) {
                return $sudoStr . 'find '. $path .' -type d -exec ' . $sudoStr . 'chmod '. $permissions[0] .' {} +';
            }
            return $sudoStr . 'chmod '. $permissions[0] .' '. $path;
        }

        $bufPerm = (count($permissions) == 1) ?  array_fill(0, 2, $permissions[0]) : $permissions;

        $commands = array();

        if ($isRecursive) {
            $commands[] = $sudoStr. 'find '.$path.' -type f -exec ' .$sudoStr.'chmod '.$bufPerm[0].' {} +';
            $commands[] = $sudoStr . 'find '.$path.' -type d -exec ' .$sudoStr. 'chmod '.$bufPerm[1].' {} +';
        } else {
            if (file_exists($path) && is_file($path)) {
                $commands[] = $sudoStr. 'chmod '. $bufPerm[0] .' '. $path;
            } else {
                $commands[] = $sudoStr. 'chmod '. $bufPerm[1] .' '. $path;
            }
        }

        if (count($permissions) >= 2) {
            return implode(' ' . $this->combineOperator . ' ', $commands);
        }

        return $isFile ? $commands[0] : $commands[1];
    }

    public function getFullPath($path)
    {
        if (is_array($path)) {
            $pathList = array();
            foreach ($path as $pathItem) {
                $pathList[] = $this->getFullPath($pathItem);
            }
            return $pathList;
        }

        if (!empty($path)) {
            $path = DIRECTORY_SEPARATOR . $path;
        }

        return $this->getRootDir() . $path;
    }

    
    public function getPermissionCommands($path, $permissions = ['644', '755'], $isSudo = false, $isFile = null, $changeOwner = true, $isCd = true)
    {
        if (is_string($path)) {
            $path = array_fill(0, 2, $path);
        }
        list($chmodPath, $chownPath) = $path;

        $commands = array();

        if ($isCd) {
            $commands[] = $this->getCd();
        }

        $chmodPath = (array) $chmodPath;

        $pathList = [];
        $recursivePathList = [];
        foreach ($chmodPath as $pathItem) {
            if (isset($this->writableMap[$pathItem]) && !$this->writableMap[$pathItem]['recursive']) {
                $pathList[] = $pathItem;
                continue;
            }
            $recursivePathList[] = $pathItem;
        }

        if (!empty($pathList)) {
            $commands[] = $this->getChmodCommand($pathList, $permissions, false, $isSudo, $isFile);
        }

        if (!empty($recursivePathList)) {
            $commands[] = $this->getChmodCommand($recursivePathList, $permissions, true, $isSudo, $isFile);
        }

        if ($changeOwner) {
            $chown = $this->getChownCommand($chownPath, $isSudo, false);
            if (isset($chown)) {
                $commands[] = $chown;
            }
        }
        return implode(' ' . $this->combineOperator . ' ', $commands).';';
    }

    protected function getCd($isCombineOperator = false)
    {
        $cd = 'cd '.$this->getRootDir();

        if ($isCombineOperator) {
            $cd .= ' '.$this->combineOperator.' ';
        }

        return $cd;
    }

    public function getRewriteRules()
    {
        return $this->config['rewriteRules'];
    }
}
