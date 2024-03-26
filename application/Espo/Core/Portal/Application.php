<?php


namespace Espo\Core\Portal;

use Espo\Entities\Portal;
use Espo\ORM\EntityManager;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;

use Espo\Core\Application as BaseApplication;
use Espo\Core\Container\ContainerBuilder;
use Espo\Core\Portal\Container as PortalContainer;
use Espo\Core\Portal\Container\ContainerConfiguration as PortalContainerConfiguration;
use Espo\Core\Portal\Utils\Config;

class Application extends BaseApplication
{
    public function __construct(?string $portalId)
    {
        date_default_timezone_set('UTC');

        $this->initContainer();
        $this->initPortal($portalId);
        $this->initAutoloads();
        $this->initPreloads();
    }

    public function getContainer(): Container
    {
        $container = parent::getContainer();

        
        return $container;
    }

    protected function initContainer(): void
    {
        $container = (new ContainerBuilder())
            ->withConfigClassName(Config::class)
            ->withContainerClassName(PortalContainer::class)
            ->withContainerConfigurationClassName(PortalContainerConfiguration::class)
            ->build();

        if (!$container instanceof PortalContainer) {
            throw new Error("Wrong container created.");
        }

        $this->container = $container;
    }

    protected function initPortal(?string $portalId): void
    {
        if (!$portalId) {
            throw new Error("Portal ID was not passed to Portal\Application.");
        }

        $entityManager = $this->container->getByClass(EntityManager::class);

        $portal = $entityManager->getEntity(Portal::ENTITY_TYPE, $portalId);

        if (!$portal) {
            $portal = $entityManager
                ->getRDBRepository(Portal::ENTITY_TYPE)
                ->where(['customId' => $portalId])
                ->findOne();
        }

        if (!$portal) {
            throw new NotFound("Portal {$portalId} not found.");
        }

        if (!$portal->get('isActive')) {
            throw new Forbidden("Portal {$portalId} is not active.");
        }

        
        $container = $this->container;

        $container->setPortal($portal);
    }

    protected function initPreloads(): void
    {
        parent::initPreloads();

        foreach ($this->getMetadata()->get(['app', 'portalContainerServices']) ?? [] as $name => $defs) {
            if ($defs['preload'] ?? false) {
                $this->container->get($name);
            }
        }
    }
}
