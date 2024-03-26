<?php


namespace Espo\Core\FieldProcessing\PhoneNumber;

use Espo\ORM\Entity;
use Espo\Repositories\PhoneNumber as Repository;
use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;

use Espo\ORM\Defs as OrmDefs;


class Loader implements LoaderInterface
{
    public function __construct(private OrmDefs $ormDefs, private EntityManager $entityManager)
    {}

    public function process(Entity $entity, Params $params): void
    {
        $entityDefs = $this->ormDefs->getEntity($entity->getEntityType());

        if (!$entityDefs->hasField('phoneNumber')) {
            return;
        }

        if ($entityDefs->getField('phoneNumber')->getType() !== 'phone') {
            return;
        }

        
        $repository = $this->entityManager->getRepository('PhoneNumber');

        $phoneNumberData = $repository->getPhoneNumberData($entity);

        $entity->set('phoneNumberData', $phoneNumberData);
        $entity->setFetched('phoneNumberData', $phoneNumberData);
    }
}
