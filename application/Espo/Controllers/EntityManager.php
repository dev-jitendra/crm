<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Entities\User;
use Espo\Tools\EntityManager\EntityManager as EntityManagerTool;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Tools\ExportCustom\ExportCustom;
use Espo\Tools\ExportCustom\Params as ExportCustomParams;
use Espo\Tools\ExportCustom\Service as ExportCustomService;
use Espo\Tools\LinkManager\LinkManager;
use stdClass;

use const FILTER_SANITIZE_STRING;

class EntityManager
{
    
    public function __construct(
        private User $user,
        private EntityManagerTool $entityManagerTool,
        private LinkManager $linkManager,
        private InjectableFactory $injectableFactory
    ) {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function postActionCreateEntity(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        if (empty($data['name']) || empty($data['type'])) {
            throw new BadRequest();
        }

        $name = $data['name'];
        $type = $data['type'];

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $type = filter_var($type, FILTER_SANITIZE_STRING);

        if (!is_string($name) || !is_string($type)) {
            throw new BadRequest();
        }

        $params = [];

        if (!empty($data['labelSingular'])) {
            $params['labelSingular'] = $data['labelSingular'];
        }

        if (!empty($data['labelPlural'])) {
            $params['labelPlural'] = $data['labelPlural'];
        }

        if (!empty($data['stream'])) {
            $params['stream'] = $data['stream'];
        }

        if (!empty($data['disabled'])) {
            $params['disabled'] = $data['disabled'];
        }

        if (!empty($data['sortBy'])) {
            $params['sortBy'] = $data['sortBy'];
        }

        if (!empty($data['sortDirection'])) {
            $params['asc'] = $data['sortDirection'] === 'asc';
        }

        if (isset($data['textFilterFields']) && is_array($data['textFilterFields'])) {
            $params['textFilterFields'] = $data['textFilterFields'];
        }

        if (!empty($data['color'])) {
            $params['color'] = $data['color'];
        }

        if (!empty($data['iconClass'])) {
            $params['iconClass'] = $data['iconClass'];
        }

        if (isset($data['fullTextSearch'])) {
            $params['fullTextSearch'] = $data['fullTextSearch'];
        }

        if (isset($data['countDisabled'])) {
            $params['countDisabled'] = $data['countDisabled'];
        }

        if (isset($data['optimisticConcurrencyControl'])) {
            $params['optimisticConcurrencyControl'] = $data['optimisticConcurrencyControl'];
        }

        $params['kanbanViewMode'] = !empty($data['kanbanViewMode']);

        if (!empty($data['kanbanStatusIgnoreList'])) {
            $params['kanbanStatusIgnoreList'] = $data['kanbanStatusIgnoreList'];
        }

        $this->entityManagerTool->create($name, $type, $params);

        return true;
    }

    
    public function postActionUpdateEntity(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        if (empty($data['name'])) {
            throw new BadRequest();
        }

        $name = $data['name'];

        $name = filter_var($name, FILTER_SANITIZE_STRING);

        if (!is_string($name)) {
            throw new BadRequest();
        }

        $this->entityManagerTool->update($name, $data);

        return true;
    }

    
    public function postActionRemoveEntity(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        if (empty($data['name'])) {
            throw new BadRequest();
        }

        $name = $data['name'];

        $name = filter_var($name, FILTER_SANITIZE_STRING);

        if (!is_string($name)) {
            throw new BadRequest();
        }

        $this->entityManagerTool->delete($name);

        return true;
    }

    
    public function postActionCreateLink(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        $paramList = [
            'entity',
            'link',
            'linkForeign',
            'label',
            'linkType',
        ];

        $additionalParamList = [
            'entityForeign',
            'relationName',
            'labelForeign',
        ];

        $params = [];

        foreach ($paramList as $item) {
            if (empty($data[$item])) {
                throw new BadRequest();
            }

            $params[$item] = filter_var($data[$item], FILTER_SANITIZE_STRING);
        }

        foreach ($additionalParamList as $item) {
            $params[$item] = filter_var($data[$item] ?? null, FILTER_SANITIZE_STRING);
        }

        $params['labelForeign'] = $params['labelForeign'] ?? $params['linkForeign'];

        if (array_key_exists('linkMultipleField', $data)) {
            $params['linkMultipleField'] = $data['linkMultipleField'];
        }

        if (array_key_exists('linkMultipleFieldForeign', $data)) {
            $params['linkMultipleFieldForeign'] = $data['linkMultipleFieldForeign'];
        }

        if (array_key_exists('audited', $data)) {
            $params['audited'] = $data['audited'];
        }

        if (array_key_exists('auditedForeign', $data)) {
            $params['auditedForeign'] = $data['auditedForeign'];
        }

        if (array_key_exists('parentEntityTypeList', $data)) {
            $params['parentEntityTypeList'] = $data['parentEntityTypeList'];
        }

        if (array_key_exists('foreignLinkEntityTypeList', $data)) {
            $params['foreignLinkEntityTypeList'] = $data['foreignLinkEntityTypeList'];
        }

        if (array_key_exists('layout', $data)) {
            $params['layout'] = $data['layout'];
        }

        if (array_key_exists('layoutForeign', $data)) {
            $params['layoutForeign'] = $data['layoutForeign'];
        }

        

        $this->linkManager->create($params);

        return true;
    }

    
    public function postActionUpdateLink(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        $paramList = [
            'entity',
            'entityForeign',
            'link',
            'linkForeign',
            'label',
            'labelForeign',
        ];

        $params = [];

        foreach ($paramList as $item) {
            if (array_key_exists($item, $data)) {
                $params[$item] = filter_var($data[$item], FILTER_SANITIZE_STRING);
            }
        }

        if (array_key_exists('linkMultipleField', $data)) {
            $params['linkMultipleField'] = $data['linkMultipleField'];
        }
        if (array_key_exists('linkMultipleFieldForeign', $data)) {
            $params['linkMultipleFieldForeign'] = $data['linkMultipleFieldForeign'];
        }

        if (array_key_exists('audited', $data)) {
            $params['audited'] = $data['audited'];
        }

        if (array_key_exists('auditedForeign', $data)) {
            $params['auditedForeign'] = $data['auditedForeign'];
        }

        if (array_key_exists('parentEntityTypeList', $data)) {
            $params['parentEntityTypeList'] = $data['parentEntityTypeList'];
        }

        if (array_key_exists('foreignLinkEntityTypeList', $data)) {
            $params['foreignLinkEntityTypeList'] = $data['foreignLinkEntityTypeList'];
        }

        if (array_key_exists('layout', $data)) {
            $params['layout'] = $data['layout'];
        }

        if (array_key_exists('auditedForeign', $data)) {
            $params['layoutForeign'] = $data['layoutForeign'];
        }

        

        $this->linkManager->update($params);

        return true;
    }

    
    public function postActionRemoveLink(Request $request): bool
    {
        $data = $request->getParsedBody();

        $data = get_object_vars($data);

        $paramList = [
            'entity',
            'link',
        ];

        $params = [];

        foreach ($paramList as $item) {
            $params[$item] = filter_var($data[$item], FILTER_SANITIZE_STRING);
        }

        

        $this->linkManager->delete($params);

        return true;
    }

    
    public function postActionFormula(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->scope)) {
            throw new BadRequest();
        }

        if (!property_exists($data, 'data')) {
            throw new BadRequest();
        }

        $formulaData = get_object_vars($data->data);

        $this->entityManagerTool->setFormulaData($data->scope, $formulaData);

        return true;
    }

    
    public function postActionResetFormulaToDefault(Request $request): bool
    {
        $data = $request->getParsedBody();

        $scope = $data->scope ?? null;
        $type = $data->type ?? null;

        if (!$scope || !$type) {
            throw new BadRequest();
        }

        $this->entityManagerTool->resetFormulaToDefault($scope, $type);

        return true;
    }

    
    public function postActionResetToDefault(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->scope)) {
            throw new BadRequest();
        }

        $this->entityManagerTool->resetToDefaults($data->scope);

        return true;
    }

    
    public function postActionExportCustom(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $name = $data->name ?? null;
        $version = $data->version ?? null;
        $author = $data->author ?? null;
        $module = $data->module ?? null;
        $description = $data->description ?? null;

        if (
            !is_string($name) ||
            !is_string($version) ||
            !is_string($author) ||
            !is_string($module) ||
            !is_string($description) && !is_null($description)
        ) {
            throw new BadRequest();
        }

        $params = new ExportCustomParams(
            name: $name,
            module: $module,
            version: $version,
            author: $author,
            description: $description
        );

        $export = $this->injectableFactory->create(ExportCustom::class);
        $service = $this->injectableFactory->create(ExportCustomService::class);

        $service->storeToConfig($params);

        $result = $export->process($params);

        return (object) ['id' => $result->getAttachmentId()];
    }
}
