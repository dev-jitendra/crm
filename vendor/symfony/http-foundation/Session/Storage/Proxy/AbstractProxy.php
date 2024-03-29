<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;


abstract class AbstractProxy
{
    
    protected $wrapper = false;

    
    protected $saveHandlerName;

    
    public function getSaveHandlerName(): ?string
    {
        return $this->saveHandlerName;
    }

    
    public function isSessionHandlerInterface(): bool
    {
        return $this instanceof \SessionHandlerInterface;
    }

    
    public function isWrapper(): bool
    {
        return $this->wrapper;
    }

    
    public function isActive(): bool
    {
        return \PHP_SESSION_ACTIVE === session_status();
    }

    
    public function getId(): string
    {
        return session_id();
    }

    
    public function setId(string $id)
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the ID of an active session.');
        }

        session_id($id);
    }

    
    public function getName(): string
    {
        return session_name();
    }

    
    public function setName(string $name)
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the name of an active session.');
        }

        session_name($name);
    }
}
