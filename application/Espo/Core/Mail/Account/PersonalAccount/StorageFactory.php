<?php


namespace Espo\Core\Mail\Account\PersonalAccount;

use Espo\Core\Mail\Account\Storage\Params;
use Espo\Core\Mail\Account\StorageFactory as StorageFactoryInterface;
use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Exceptions\NoImap;
use Espo\Core\Mail\Mail\Storage\Imap;
use Espo\Core\Mail\Account\Storage\LaminasStorage;
use Espo\Core\Utils\Log;
use Espo\Core\InjectableFactory;
use Espo\Entities\UserData;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\ORM\EntityManager;

use LogicException;
use Throwable;

class StorageFactory implements StorageFactoryInterface
{
    public function __construct(
        private Log $log,
        private InjectableFactory $injectableFactory,
        private EntityManager $entityManager
    ) {}

    
    public function create(Account $account): LaminasStorage
    {
        $userLink = $account->getUser();

        if (!$userLink) {
            throw new LogicException("No user for mail account.");
        }

        $userId = $userLink->getId();

        $imapParams = $account->getImapParams();

        if (!$imapParams) {
            throw new NoImap("No IMAP params.");
        }

        $params = Params::createBuilder()
            ->setHost($imapParams->getHost())
            ->setPort($imapParams->getPort())
            ->setSecurity($imapParams->getSecurity())
            ->setUsername($imapParams->getUsername())
            ->setPassword($imapParams->getPassword())
            ->setEmailAddress($account->getEmailAddress())
            ->setUserId($userId)
            ->setId($account->getId())
            ->setImapHandlerClassName($account->getImapHandlerClassName())
            ->build();

        return $this->createWithParams($params);
    }

    public function createWithParams(Params $params): LaminasStorage
    {
        $rawParams = [
            'host' => $params->getHost(),
            'port' => $params->getPort(),
            'username' => $params->getUsername(),
            'password' => $params->getPassword(),
            'emailAddress' => $params->getEmailAddress(),
            'userId' => $params->getUserId(),
            'imapHandler' => $params->getImapHandlerClassName(),
            'id' => $params->getId(),
        ];

        if ($params->getSecurity()) {
            $rawParams['security'] = $params->getSecurity();
        }

        $emailAddress = $rawParams['emailAddress'] ?? null;
        $userId = $rawParams['userId'] ?? null;
        
        $handlerClassName = $rawParams['imapHandler'] ?? null;

        $handler = null;
        $imapParams = null;

        if ($handlerClassName && !empty($rawParams['id'])) {
            try {
                $handler = $this->injectableFactory->create($handlerClassName);
            }
            catch (Throwable $e) {
                $this->log->error(
                    "EmailAccount: Could not create Imap Handler. Error: " . $e->getMessage()
                );
            }

            if ($handler && method_exists($handler, 'prepareProtocol')) {
                
                $rawParams['ssl'] = $rawParams['security'] ?? null;

                $imapParams = $handler->prepareProtocol($rawParams['id'], $rawParams);
            }
        }

        if ($emailAddress && $userId && !$handlerClassName) {
            $emailAddress = strtolower($emailAddress);

            $userData = $this->getUserDataRepository()->getByUserId($userId);

            if ($userData) {
                $imapHandlers = $userData->get('imapHandlers') ?? (object) [];

                if (isset($imapHandlers->$emailAddress)) {
                    
                    $handlerClassName = $imapHandlers->$emailAddress;

                    try {
                        $handler = $this->injectableFactory->create($handlerClassName);
                    }
                    catch (Throwable $e) {
                        $this->log->error(
                            "EmailAccount: Could not create Imap Handler for {$emailAddress}. Error: " .
                            $e->getMessage()
                        );
                    }

                    if ($handler && method_exists($handler, 'prepareProtocol')) {
                        
                        $imapParams = $handler->prepareProtocol($userId, $emailAddress, $rawParams);
                    }
                }
            }
        }

        if (!$imapParams) {
            $imapParams = [
                'host' => $rawParams['host'],
                'port' => $rawParams['port'],
                'user' => $rawParams['username'],
                'password' => $rawParams['password'],
            ];

            if (!empty($rawParams['security'])) {
                $imapParams['ssl'] = $rawParams['security'];
            }
        }

        return new LaminasStorage(
            new Imap($imapParams)
        );
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }
}
