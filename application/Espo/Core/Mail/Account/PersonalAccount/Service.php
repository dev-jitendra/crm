<?php


namespace Espo\Core\Mail\Account\PersonalAccount;

use Laminas\Mail\Message;

use Espo\Core\Mail\Account\Account as Account;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\Fetcher;
use Espo\Core\Mail\Account\Storage\Params;
use Espo\Core\Mail\Account\StorageFactory;
use Espo\Entities\User;

class Service
{
    public function __construct(
        private Fetcher $fetcher,
        private AccountFactory $accountFactory,
        private StorageFactory $storageFactory,
        private User $user
    ) {}

    
    public function fetch(string $id): void
    {
        $account = $this->accountFactory->create($id);

        $this->fetcher->fetch($account);
    }

    
    public function getFolderList(Params $params): array
    {
        $userId = $params->getUserId();

        if (
            $userId &&
            !$this->user->isAdmin() &&
            $userId !== $this->user->getId()
        ) {
            throw new Forbidden();
        }

        if ($params->getId()) {
            $account = $this->accountFactory->create($params->getId());

            $params = $params
                ->withPassword($this->getPassword($params, $account))
                ->withImapHandlerClassName($account->getImapHandlerClassName());
        }

        $storage = $this->storageFactory->createWithParams($params);

        return $storage->getFolderNames();
    }

    
    public function testConnection(Params $params): void
    {
        $userId = $params->getUserId();

        if (
            $userId &&
            !$this->user->isAdmin() &&
            $userId !== $this->user->getId()
        ) {
            throw new Forbidden();
        }

        if (!$params->getId() && $params->getPassword() === null) {
            throw new Forbidden();
        }

        if ($params->getId()) {
            $account = $this->accountFactory->create($params->getId());

            if (
                !$this->user->isAdmin() &&
                $account->getUser()->getId() !== $this->user->getId()
            ) {
                throw new Forbidden();
            }

            $params = $params
                ->withPassword($this->getPassword($params, $account))
                ->withImapHandlerClassName($account->getImapHandlerClassName());
        }

        $storage = $this->storageFactory->createWithParams($params);

        $storage->getFolderNames();
    }

    private function getPassword(Params $params, Account $account): ?string
    {
        $password = $params->getPassword();

        if ($password !== null) {
            return $password;
        }

        $imapParams = $account->getImapParams();

        return $imapParams?->getPassword();
    }

    
    public function storeSentMessage(string $id, Message $message): void
    {
        $account = $this->accountFactory->create($id);

        $folder = $account->getSentFolder();

        if (!$folder) {
            throw new Error("No sent folder for Email Account $id.");
        }

        $storage = $this->storageFactory->create($account);

        $storage->appendMessage($message->toString(), $folder);
    }
}
