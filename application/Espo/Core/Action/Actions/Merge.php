<?php


namespace Espo\Core\Action\Actions;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Action\Action;
use Espo\Core\Action\Actions\Merge\Merger;
use Espo\Core\Action\Data;
use Espo\Core\Action\Params;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;

use stdClass;

class Merge implements Action
{
    public function __construct(private Acl $acl, private Merger $merger)
    {}

    public function process(Params $params, Data $data): void
    {
        $entityType = $params->getEntityType();

        if (!$this->acl->checkScope($entityType, Table::ACTION_EDIT)) {
            throw new Forbidden();
        }

        $sourceIdList = $data->get('sourceIdList');
        $attributes = $data->get('attributes');

        if (!is_array($sourceIdList)) {
            throw new BadRequest("No 'sourceIdList'.");
        }

        if (!$attributes instanceof stdClass) {
            throw new BadRequest("No 'attributes'.");
        }

        $this->merger->process($params, $sourceIdList, $attributes);
    }
}
