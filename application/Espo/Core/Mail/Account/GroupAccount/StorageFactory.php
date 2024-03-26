<?php


namespace Espo\Core\Mail\Account\GroupAccount;

use Espo\Core\Mail\Account\Storage\Params;
use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Account\StorageFactory as StorageFactoryInterface;
use Espo\Core\Mail\Account\Storage\LaminasStorage;
use Espo\Core\Mail\Exceptions\NoImap;
use Espo\Core\Mail\Mail\Storage\Imap;
use Espo\Core\Utils\Log;
use Espo\Core\InjectableFactory;

use Throwable;

class StorageFactory implements StorageFactoryInterface
{
    public function __construct(
        private Log $log,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(Account $account): LaminasStorage
    {
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
            'imapHandler' => $params->getImapHandlerClassName(),
            'id' => $params->getId(),
        ];

        if ($params->getSecurity()) {
            $rawParams['security'] = $params->getSecurity();
        }

        $imapParams = null;

        $handlerClassName = $rawParams['imapHandler'] ?? null;

        $handler = null;

        if ($handlerClassName && !empty($rawParams['id'])) {
            try {
                $handler = $this->injectableFactory->create($handlerClassName);
            }
            catch (Throwable $e) {
                $this->log->error("InboundEmail: Could not create Imap Handler. Error: " . $e->getMessage());
            }

            if ($handler && method_exists($handler, 'prepareProtocol')) {
                
                $rawParams['ssl'] = $rawParams['security'] ?? null;

                
                $imapParams = $handler->prepareProtocol($rawParams['id'], $rawParams);
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

        return new LaminasStorage(new Imap($imapParams));
    }
}
