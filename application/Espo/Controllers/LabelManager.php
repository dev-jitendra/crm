<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\DataManager;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\User;
use Espo\Tools\LabelManager\LabelManager as LabelManagerTool;

use stdClass;

class LabelManager
{

    
    public function __construct(
        private User $user,
        private DataManager $dataManager,
        private LabelManagerTool $labelManagerTool
    ) {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function postActionGetScopeList(): array
    {
        return $this->labelManagerTool->getScopeList();
    }

    public function postActionGetScopeData(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        if (empty($data->scope) || empty($data->language)) {
            throw new BadRequest();
        }

        return $this->labelManagerTool->getScopeData($data->language, $data->scope);
    }

    public function postActionSaveLabels(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        if (empty($data->scope) || empty($data->language) || !isset($data->labels)) {
            throw new BadRequest();
        }

        $labels = get_object_vars($data->labels);

        $returnData = $this->labelManagerTool->saveLabels($data->language, $data->scope, $labels);

        $this->dataManager->clearCache();

        return $returnData;
    }
}
