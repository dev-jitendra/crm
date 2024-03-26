<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Attachment;
use Espo\Entities\Settings;
use Espo\Entities\User;

class AccessChecker
{
    
    private $adminOnlyHavingInlineAttachmentsEntityTypeList = ['TemplateManager'];

    
    private $attachmentFieldTypeList = [
        FieldType::FILE,
        FieldType::IMAGE,
        FieldType::ATTACHMENT_MULTIPLE,
    ];

    
    private $inlineAttachmentFieldTypeList = [
        FieldType::WYSIWYG,
    ];

    
    private $allowedRoleList = [
        Attachment::ROLE_ATTACHMENT,
        Attachment::ROLE_INLINE_ATTACHMENT,
    ];

    private User $user;
    private Acl $acl;
    private Metadata $metadata;

    public function __construct(
        User $user,
        Acl $acl,
        Metadata $metadata
    ) {
        $this->user = $user;
        $this->acl = $acl;
        $this->metadata = $metadata;
    }

    
    public function check(FieldData $fieldData, string $role = Attachment::ROLE_ATTACHMENT): void
    {
        if (!in_array($role, $this->allowedRoleList)) {
            throw new Forbidden("Role not allowed.");
        }

        $relatedEntityType = $fieldData->getParentType() ?? $fieldData->getRelatedType();
        $field = $fieldData->getField();

        if (!$relatedEntityType) {
            throw new Forbidden();
        }

        if (
            $this->user->isAdmin() &&
            $role === Attachment::ROLE_INLINE_ATTACHMENT &&
            in_array($relatedEntityType, $this->adminOnlyHavingInlineAttachmentsEntityTypeList)
        ) {
            return;
        }

        $fieldType = $this->metadata->get(['entityDefs', $relatedEntityType, 'fields', $field, 'type']);

        if (!$fieldType) {
            throw new Forbidden("Field '{$field}' does not exist.");
        }

        $fieldTypeList = $role === Attachment::ROLE_INLINE_ATTACHMENT ?
            $this->inlineAttachmentFieldTypeList :
            $this->attachmentFieldTypeList;

        if (!in_array($fieldType, $fieldTypeList)) {
            throw new Forbidden("Field type '{$fieldType}' is not allowed for {$role}.");
        }

        if ($this->user->isAdmin() && $relatedEntityType === Settings::ENTITY_TYPE) {
            return;
        }

        if (
            !$this->acl->checkScope($relatedEntityType, Table::ACTION_CREATE) &&
            !$this->acl->checkScope($relatedEntityType, Table::ACTION_EDIT)
        ) {
            throw new Forbidden("No access to " . $relatedEntityType . ".");
        }

        if (in_array($field, $this->acl->getScopeForbiddenFieldList($relatedEntityType, Table::ACTION_EDIT))) {
            throw new Forbidden("No access to field '" . $field . "'.");
        }
    }
}
