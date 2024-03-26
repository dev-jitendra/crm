<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Core\Utils\Json;
use Espo\Modules\Crm\Tools\KnowledgeBase\Service as KBService;

use Espo\Tools\Attachment\FieldData;
use stdClass;

class KnowledgeBaseArticle extends Record
{
    
    public function postActionGetCopiedAttachments(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $id = $data->id ?? null;
        $field = $data->field ?? null;
        $parentType = $data->parentType ?? null;
        $relatedType = $data->relatedType ?? null;

        if (!$id || !$field) {
            throw new BadRequest("No `id` or `field`.");
        }

        try {
            $fieldData = new FieldData(
                $field,
                $parentType,
                $relatedType
            );
        }
        catch (Error $e) {
            throw new BadRequest($e->getMessage());
        }

        $list = $this->getArticleService()->copyAttachments($id, $fieldData);

        $ids = array_map(
            fn ($item) => $item->getId(),
            $list
        );

        $names = (object) [];

        foreach ($list as $item) {
            $names->{$item->getId()} = $item->getName();
        }

        return (object) [
            'ids' => $ids,
            'names' => $names,
        ];
    }

    
    public function postActionMoveToTop(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $where = null;

        if (!empty($data->where)) {
            $where = WhereItem::fromRawAndGroup(
                Json::decode(Json::encode($data->where), true)
            );
        }

        $this->getArticleService()->moveToTop($data->id, $where);

        return true;
    }

    
    public function postActionMoveUp(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $where = null;

        if (!empty($data->where)) {
            $where = WhereItem::fromRawAndGroup(
                Json::decode(Json::encode($data->where), true)
            );
        }

        $this->getArticleService()->moveUp($data->id, $where);

        return true;
    }

    
    public function postActionMoveDown(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $where = null;

        if (!empty($data->where)) {
            $where = WhereItem::fromRawAndGroup(
                Json::decode(Json::encode($data->where), true)
            );
        }

        $this->getArticleService()->moveDown($data->id, $where);

        return true;
    }

    
    public function postActionMoveToBottom(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $where = null;

        if (!empty($data->where)) {
            $where = WhereItem::fromRawAndGroup(
                Json::decode(Json::encode($data->where), true)
            );
        }

        $this->getArticleService()->moveToBottom($data->id, $where);

        return true;
    }

    private function getArticleService(): KBService
    {
        return $this->injectableFactory->create(KBService::class);
    }
}
