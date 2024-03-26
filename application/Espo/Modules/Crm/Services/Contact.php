<?php


namespace Espo\Modules\Crm\Services;

use Espo\ORM\Entity;

use Espo\Services\Record;


class Contact extends Record
{
    protected $readOnlyAttributeList = [
        'inboundEmailId',
        'portalUserId'
    ];

    protected $linkMandatorySelectAttributeList = [
        'targetLists' => ['isOptedOut'],
    ];

    protected $mandatorySelectAttributeList = [
        'accountId',
        'accountName',
    ];

    protected function afterCreateEntity(Entity $entity, $data)
    {
        if (!empty($data->emailId)) {
            $email = $this->getEntityManager()->getEntity('Email', $data->emailId);

            if ($email && !$email->get('parentId') && $this->getAcl()->check($email)) {
                if ($this->getConfig()->get('b2cMode') || !$entity->get('accountId')) {
                    $email->set([
                        'parentType' => 'Contact',
                        'parentId' => $entity->getId(),
                    ]);
                }
                else {
                    if ($entity->get('accountId')) { 
                        $email->set([
                            'parentType' => 'Account',
                            'parentId' => $entity->get('accountId')
                        ]);
                    }
                }

                $this->getEntityManager()->saveEntity($email);
            }
        }
    }
}
