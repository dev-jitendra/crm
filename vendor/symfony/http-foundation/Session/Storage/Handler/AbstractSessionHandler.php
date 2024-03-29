<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\SessionUtils;


abstract class AbstractSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private string $sessionName;
    private string $prefetchId;
    private string $prefetchData;
    private ?string $newSessionId = null;
    private string $igbinaryEmptyData;

    public function open(string $savePath, string $sessionName): bool
    {
        $this->sessionName = $sessionName;
        if (!headers_sent() && !\ini_get('session.cache_limiter') && '0' !== \ini_get('session.cache_limiter')) {
            header(sprintf('Cache-Control: max-age=%d, private, must-revalidate', 60 * (int) \ini_get('session.cache_expire')));
        }

        return true;
    }

    abstract protected function doRead(string $sessionId): string;

    abstract protected function doWrite(string $sessionId, string $data): bool;

    abstract protected function doDestroy(string $sessionId): bool;

    public function validateId(string $sessionId): bool
    {
        $this->prefetchData = $this->read($sessionId);
        $this->prefetchId = $sessionId;

        return '' !== $this->prefetchData;
    }

    public function read(string $sessionId): string
    {
        if (isset($this->prefetchId)) {
            $prefetchId = $this->prefetchId;
            $prefetchData = $this->prefetchData;
            unset($this->prefetchId, $this->prefetchData);

            if ($prefetchId === $sessionId || '' === $prefetchData) {
                $this->newSessionId = '' === $prefetchData ? $sessionId : null;

                return $prefetchData;
            }
        }

        $data = $this->doRead($sessionId);
        $this->newSessionId = '' === $data ? $sessionId : null;

        return $data;
    }

    public function write(string $sessionId, string $data): bool
    {
        
        $this->igbinaryEmptyData ??= \function_exists('igbinary_serialize') ? igbinary_serialize([]) : '';
        if ('' === $data || $this->igbinaryEmptyData === $data) {
            return $this->destroy($sessionId);
        }
        $this->newSessionId = null;

        return $this->doWrite($sessionId, $data);
    }

    public function destroy(string $sessionId): bool
    {
        if (!headers_sent() && filter_var(\ini_get('session.use_cookies'), \FILTER_VALIDATE_BOOLEAN)) {
            if (!isset($this->sessionName)) {
                throw new \LogicException(sprintf('Session name cannot be empty, did you forget to call "parent::open()" in "%s"?.', static::class));
            }
            $cookie = SessionUtils::popSessionCookie($this->sessionName, $sessionId);

            
            if (null === $cookie || isset($_COOKIE[$this->sessionName])) {
                $params = session_get_cookie_params();
                unset($params['lifetime']);
                setcookie($this->sessionName, '', $params);
            }
        }

        return $this->newSessionId === $sessionId || $this->doDestroy($sessionId);
    }
}
