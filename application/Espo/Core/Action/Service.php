<?php


namespace Espo\Core\Action;

use Espo\Core\Acl;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ReadParams;
use Espo\Core\Record\ServiceContainer as RecordServiceContainer;

use Espo\ORM\Entity;

use stdClass;

class Service
{
    public function __construct(
        private ActionFactory $factory,
        private Acl $acl,
        private RecordServiceContainer $recordServiceContainer
    ) {}

    
    public function process(string $entityType, string $action, string $id, stdClass $data): Entity
    {
        if (!$this->acl->checkScope($entityType)) {
            throw new ForbiddenSilent();
        }

        if (!$action || !$id) {
            throw new BadRequest();
        }

        $actionParams = new Params($entityType, $id);

        $actionProcessor = $this->factory->create($action, $entityType);

        $actionProcessor->process(
            $actionParams,
            Data::fromRaw($data)
        );

        $service = $this->recordServiceContainer->get($entityType);

        return $service->read($id, ReadParams::create());
    }
}
