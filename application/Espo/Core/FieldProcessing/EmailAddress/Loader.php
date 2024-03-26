<?php


namespace Espo\Core\FieldProcessing\EmailAddress;

use Espo\Repositories\EmailAddress as Repository;
use Espo\ORM\Entity;
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

        if (!$entityDefs->hasField('emailAddress')) {
            return;
        }

        if ($entityDefs->getField('emailAddress')->getType() !== 'email') {
            return;
        }

        
        $repository = $this->entityManager->getRepository('EmailAddress');

        $emailAddressData = $repository->getEmailAddressData($entity);

        $entity->set('emailAddressData', $emailAddressData);
        $entity->setFetched('emailAddressData', $emailAddressData);
    }
}
