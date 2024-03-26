<?php


namespace Espo\Tools\ActionHistory;

use Espo\Core\Record\ActionHistory\Action;
use Espo\Core\Record\Collection as RecordCollection;
use Espo\Entities\ActionHistoryRecord;
use Espo\Core\FieldProcessing\ListLoadProcessor;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;
use Espo\Entities\User;

class Service
{
    public function __construct(
        private Metadata $metadata,
        private EntityManager $entityManager,
        private User $user,
        private ListLoadProcessor $listLoadProcessor
    ) {}

    
    public function getLastViewed(?int $maxSize, ?int $offset): RecordCollection
    {
        $scopes = $this->metadata->get('scopes');

        $targetTypeList = array_filter(
            array_keys($scopes),
            function ($item) use ($scopes) {
                return !empty($scopes[$item]['object']) || !empty($scopes[$item]['lastViewed']);
            }
        );

        $maxSize = $maxSize ?? 0;
        $offset = $offset ?? 0;

        $collection = $this->entityManager
            ->getRDBRepositoryByClass(ActionHistoryRecord::class)
            ->where([
                'userId' => $this->user->getId(),
                'action' => Action::READ,
                'targetType' => $targetTypeList,
            ])
            ->order('MAX:createdAt', 'DESC')
            ->select([
                'targetId',
                'targetType',
                'MAX:number',
                ['MAX:createdAt', 'createdAt'],
            ])
            ->group(['targetId', 'targetType'])
            ->limit($offset, $maxSize + 1)
            ->find();

        foreach ($collection as $entity) {
            $this->listLoadProcessor->process($entity);

            $entity->set('id', Util::generateId());
        }

        return RecordCollection::createNoCount($collection,  $maxSize);
    }
}
