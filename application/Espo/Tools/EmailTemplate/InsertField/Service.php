<?php


namespace Espo\Tools\EmailTemplate\InsertField;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\FieldUtil;
use Espo\Entities\Email;
use Espo\Entities\EmailAddress;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Contact;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Tools\EmailTemplate\Formatter;
use stdClass;

class Service
{
    private EntityManager $entityManager;
    private Acl $acl;
    private Formatter $formatter;
    private FieldUtil $fieldUtil;
    private ServiceContainer $recordServiceContainer;

    public function __construct(
        EntityManager $entityManager,
        Acl $acl,
        Formatter $formatter,
        FieldUtil $fieldUtil,
        ServiceContainer $recordServiceContainer
    ) {
        $this->entityManager = $entityManager;
        $this->acl = $acl;
        $this->formatter = $formatter;
        $this->fieldUtil = $fieldUtil;
        $this->recordServiceContainer = $recordServiceContainer;
    }

    
    public function getData(?string $parentType, ?string $parentId, ?string $to): stdClass
    {
        if (!$this->acl->checkScope(Email::ENTITY_TYPE, Table::ACTION_CREATE)) {
            throw new Forbidden();
        }

        $result = (object) [];

        $dataList = [];

        if ($parentId && $parentType) {
            $e = $this->entityManager->getEntityById($parentType, $parentId);

            if ($e && $this->acl->check($e)) {
                $dataList[] = [
                    'type' => 'parent',
                    'entity' => $e,
                ];
            }
        }

        if ($to) {
            $e = $this->getEmailAddressRepository()
                ->getEntityByAddress($to, null,
                    [Contact::ENTITY_TYPE, Lead::ENTITY_TYPE, Account::ENTITY_TYPE]);

            if ($e && $e->getEntityType() !== User::ENTITY_TYPE && $this->acl->check($e)) {
                $dataList[] = [
                    'type' => 'to',
                    'entity' => $e,
                ];
            }
        }

        $fm = $this->fieldUtil;

        $formatter = $this->formatter;

        foreach ($dataList as $item) {
            $type = $item['type'];
            $e = $item['entity'];

            $entityType = $e->getEntityType();

            $recordService = $this->recordServiceContainer->get($entityType);

            $recordService->prepareEntityForOutput($e);

            $ignoreTypeList = [
                'image',
                'file',
                'map',
                'wysiwyg',
                'linkMultiple',
                'attachmentMultiple',
                'bool',
            ];

            foreach ($fm->getEntityTypeFieldList($entityType) as $field) {
                $fieldType = $fm->getEntityTypeFieldParam($entityType, $field, 'type');
                $fieldAttributeList = $fm->getAttributeList($entityType, $field);

                if (
                    $fm->getEntityTypeFieldParam($entityType, $field, 'disabled') ||
                    $fm->getEntityTypeFieldParam($entityType, $field, 'directAccessDisabled') ||
                    $fm->getEntityTypeFieldParam($entityType, $field, 'templatePlaceholderDisabled') ||
                    in_array($fieldType, $ignoreTypeList)
                ) {
                    foreach ($fieldAttributeList as $a) {
                        $e->clear($a);
                    }
                }
            }

            $attributeList = $fm->getEntityTypeAttributeList($entityType);

            $values = (object) [];

            foreach ($attributeList as $a) {
                if (!$e->has($a)) {
                    continue;
                }

                $value = $formatter->formatAttributeValue($e, $a);

                if ($value !== null && $value !== '') {
                    $values->$a = $value;
                }
            }

            $result->$type = (object) [
                'entityType' => $e->getEntityType(),
                'id' => $e->getId(),
                'values' => $values,
                'name' => $e->get('name'),
            ];
        }

        return $result;
    }

    private function getEmailAddressRepository(): EmailAddressRepository
    {
        
        return $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);
    }
}
