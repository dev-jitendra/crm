<?php


namespace Espo\Tools\PopupNotification;

use Espo\Core\InjectableFactory;
use Espo\Core\ServiceFactory;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;

use Espo\Entities\User;

use stdClass;
use Throwable;

class Service
{
    private Metadata $metadata;
    private ServiceFactory $serviceFactory;
    private User $user;
    private Log $log;
    private InjectableFactory $injectableFactory;

    public function __construct(
        Metadata $metadata,
        ServiceFactory $serviceFactory,
        User $user,
        Log $log,
        InjectableFactory $injectableFactory
    ) {
        $this->metadata = $metadata;
        $this->serviceFactory = $serviceFactory;
        $this->user = $user;
        $this->log = $log;
        $this->injectableFactory = $injectableFactory;
    }

    
    public function getGrouped(): array
    {
        $data = $this->metadata->get(['app', 'popupNotifications']) ?? [];

        $data = array_filter($data, function ($item) {
            if (!($item['grouped'] ?? false)) {
                return false;
            }

            if ($item['disabled'] ?? false) {
                return false;
            }

            if (
                empty($item['providerClassName']) &&
                (empty($item['serviceName']) || empty($item['methodName']))
            ) {
                return false;
            }

            $portalDisabled = $item['portalDisabled'] ?? false;

            if ($portalDisabled && $this->user->isPortal()) {
                return false;
            }

            return true;
        });

        $result = [];

        foreach ($data as $type => $item) {
            
            $className = $item['providerClassName'] ?? null;

            try {
                if ($className) {
                    $provider = $this->injectableFactory->create($className);

                    $result[$type] = $provider->get($this->user);

                    continue;
                }

                

                $serviceName = $item['serviceName'];
                $methodName = $item['methodName'];

                $service = $this->serviceFactory->create($serviceName);

                $itemList = array_map(
                    function ($raw) {
                        if ($raw instanceof stdClass) {
                            return new Item($raw->id ?? null, $raw->data);
                        }

                        return new Item($raw['id'] ?? null, $raw['data']);
                    },
                    $service->$methodName($this->user->getId())
                );

                $result[$type] = $itemList;
            }
            catch (Throwable $e) {
                $this->log->error("Popup notifications: " . $e->getMessage());
            }
        }

        return $result;
    }
}
