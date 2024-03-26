<?php


namespace Espo\Tools\EmailTemplate;

use Espo\Core\Templates\Entities\Person as PersonTemplate;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Contact;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\Core\AclManager;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\Config;
use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Core\Entities\Person;
use Espo\Core\Htmlizer\HtmlizerFactory as HtmlizerFactory;
use Espo\Core\Htmlizer\Htmlizer;
use Espo\Core\Acl\GlobalRestriction;
use Espo\Entities\EmailTemplate;
use Espo\Entities\User;
use Espo\Entities\Attachment;
use Espo\Entities\EmailAddress;
use Espo\Repositories\EmailAddress as EmailAddressRepository;

use Exception;

class Processor
{
    private const KEY_PARENT = 'Parent';

    public function __construct(
        private Formatter $formatter,
        private EntityManager $entityManager,
        private AclManager $aclManager,
        private ServiceContainer $recordServiceContainer,
        private Config $config,
        private FileStorageManager $fileStorageManager,
        private User $user,
        private HtmlizerFactory $htmlizerFactory,
        private PlaceholdersProvider $placeholdersProvider
    ) {}

    public function process(EmailTemplate $template, Params $params, Data $data): Result
    {
        $entityHash = $data->getEntityHash();

        $user = $data->getUser() ?? $this->user;

        if (!isset($entityHash[User::ENTITY_TYPE])) {
            $entityHash[User::ENTITY_TYPE] = $user;
        }

        $foundByAddressEntity = null;

        if ($data->getEmailAddress()) {
            $foundByAddressEntity = $this->getEmailAddressRepository()
                ->getEntityByAddress(
                    $data->getEmailAddress(),
                    null,
                    [
                        Contact::ENTITY_TYPE,
                        Lead::ENTITY_TYPE,
                        Account::ENTITY_TYPE,
                        User::ENTITY_TYPE,
                    ]
                );
        }

        if ($foundByAddressEntity) {
            if ($foundByAddressEntity instanceof Person) {
                $entityHash[PersonTemplate::TEMPLATE_TYPE] = $foundByAddressEntity;
            }

            if (!isset($entityHash[$foundByAddressEntity->getEntityType()])) {
                $entityHash[$foundByAddressEntity->getEntityType()] = $foundByAddressEntity;
            }
        }

        if (
            !$data->getParent() &&
            $data->getParentId() &&
            $data->getParentType()
        ) {
            $parent = $this->entityManager->getEntity($data->getParentType(), $data->getParentId());

            if ($parent) {
                $service = $this->recordServiceContainer->get($data->getParentType());

                $service->loadAdditionalFields($parent);

                if (
                    $params->applyAcl() &&
                    !$this->aclManager->checkEntityRead($this->user, $parent)
                ) {
                    $parent = null;
                }

                $data = $data->withParent($parent);
            }
        }

        if ($data->getParent()) {
            $parent = $data->getParent();

            $entityHash[$parent->getEntityType()] = $parent;
            $entityHash[self::KEY_PARENT] = $parent;

            if (
                !isset($entityHash[PersonTemplate::TEMPLATE_TYPE]) &&
                $parent instanceof Person
            ) {
                $entityHash[PersonTemplate::TEMPLATE_TYPE] = $parent;
            }
        }

        if ($data->getRelatedId() && $data->getRelatedType()) {
            $related = $this->entityManager->getEntity($data->getRelatedType(), $data->getRelatedId());

            if (
                $related &&
                $params->applyAcl() &&
                !$this->aclManager->checkEntityRead($this->user, $related)
            ) {
                $related = null;
            }

            if ($related) {
                $entityHash[$related->getEntityType()] = $related;
            }
        }

        $subject = $template->getSubject() ?? '';
        $body = $template->getBody() ?? '';

        $parent = $entityHash[self::KEY_PARENT] ?? null;

        if ($parent && !$this->config->get('emailTemplateHtmlizerDisabled')) {
            $handlebarsInSubject = str_contains($subject, '{{') && str_contains($subject, '}}');
            $handlebarsInBody = str_contains($body, '{{') && str_contains($body, '}}');

            if ($handlebarsInSubject || $handlebarsInBody) {
                $htmlizer = $this->createHtmlizer($params, $user);

                if ($handlebarsInSubject) {
                    $subject = $htmlizer->render($parent, $subject);
                }

                if ($handlebarsInBody) {
                    $body = $htmlizer->render($parent, $body, null, null, false, true);
                }
            }
        }

        foreach ($entityHash as $type => $entity) {
            $subject = $this->processText(
                $type,
                $entity,
                $subject,
                $user,
                false,
                null,
                !$params->applyAcl(),
                $template->isHtml()
            );
        }

        foreach ($entityHash as $type => $entity) {
            $body = $this->processText(
                $type,
                $entity,
                $body,
                $user,
                false,
                null,
                !$params->applyAcl(),
                $template->isHtml()
            );
        }

        $subject = $this->processPlaceholders($subject, $data);
        $body = $this->processPlaceholders($body, $data);

        $attachmentList = $params->copyAttachments() ?
            $this->copyAttachments($template) :
            [];

        return new Result(
            $subject,
            $body,
            $template->isHtml(),
            $attachmentList
        );
    }

    private function processPlaceholders(string $text, Data $data): string
    {
        foreach ($this->placeholdersProvider->get() as [$key, $placeholder]) {
            $value = $placeholder->get($data);

            $text = str_replace('{' . $key . '}', $value, $text);
        }

        return $text;
    }

    private function processText(
        string $type,
        Entity $entity,
        string $text,
        User $user,
        bool $skipLinks = false,
        ?string $prefixLink = null,
        bool $skipAcl = false,
        bool $isHtml = true
    ): string {

        $attributeList = $entity->getAttributeList();

        $forbiddenAttributeList = [];

        if (!$skipAcl) {
            $forbiddenAttributeList = array_merge(
                $this->aclManager->getScopeForbiddenAttributeList($user, $entity->getEntityType()),
                $this->aclManager->getScopeRestrictedAttributeList(
                    $entity->getEntityType(),
                    [
                        GlobalRestriction::TYPE_FORBIDDEN,
                        GlobalRestriction::TYPE_INTERNAL,
                        GlobalRestriction::TYPE_ONLY_ADMIN,
                    ]
                )
            );
        }

        foreach ($attributeList as $attribute) {
            if (in_array($attribute, $forbiddenAttributeList)) {
                continue;
            }

            if (is_object($entity->get($attribute))) {
                continue;
            }

            if (!$entity->getAttributeType($attribute)) {
                continue;
            }

            $value = $this->formatter->formatAttributeValue($entity, $attribute, !$isHtml);

            if (is_null($value)) {
                continue;
            }

            $variableName = $attribute;

            if (!is_null($prefixLink)) {
                $variableName = $prefixLink . '.' . $attribute;
            }

            $text = str_replace('{' . $type . '.' . $variableName . '}', $value, $text);
        }

        if (!$skipLinks && $entity->hasId()) {
            $text = $this->processLinks(
                $type,
                $entity,
                $text,
                $user,
                $skipAcl,
                $isHtml
            );
        }

        return $text;
    }

    private function processLinks(
        string $type,
        Entity $entity,
        string $text,
        User $user,
        bool $skipAcl,
        bool $isHtml
    ): string {

        $forbiddenLinkList = $skipAcl ?
            $this->aclManager->getScopeRestrictedLinkList(
                $entity->getEntityType(),
                [
                    GlobalRestriction::TYPE_FORBIDDEN,
                    GlobalRestriction::TYPE_INTERNAL,
                    GlobalRestriction::TYPE_ONLY_ADMIN,
                ]
            ) :
            [];

        foreach ($entity->getRelationList() as $relation) {
            if (in_array($relation, $forbiddenLinkList)) {
                continue;
            }

            $relationType = $entity->getRelationType($relation);

            $relationTypeIsOk =
                $relationType === Entity::BELONGS_TO ||
                $relationType === Entity::BELONGS_TO_PARENT;

            if (!$relationTypeIsOk) {
                continue;
            }

            $relatedEntity = $this->entityManager
                ->getRDBRepository($entity->getEntityType())
                ->getRelation($entity, $relation)
                ->findOne();

            if (!$relatedEntity) {
                continue;
            }

            try {
                $hasAccess = $this->aclManager->checkEntityRead($user, $relatedEntity);
            }
            catch (Exception) {
                continue;
            }

            if (!$hasAccess) {
                continue;
            }

            $text = $this->processText(
                $type,
                $relatedEntity,
                $text,
                $user,
                true,
                $relation,
                $skipAcl,
                $isHtml
            );
        }

        return $text;
    }

    
    private function copyAttachments(EmailTemplate $template): array
    {
        $copiedAttachmentList = [];

        
        $attachmentList = $this->entityManager
            ->getRDBRepositoryByClass(EmailTemplate::class)
            ->getRelation($template, 'attachments')
            ->find();

        foreach ($attachmentList as $attachment) {
            
            $clone = $this->entityManager->getNewEntity(Attachment::ENTITY_TYPE);

            $data = $attachment->getValueMap();

            unset($data->parentType);
            unset($data->parentId);
            unset($data->id);

            $clone->set($data);
            $clone->set('sourceId', $attachment->getSourceId());
            $clone->set('storage', $attachment->getStorage());

            if (!$this->fileStorageManager->exists($attachment)) {
                continue;
            }

            $this->entityManager->saveEntity($clone);

            $copiedAttachmentList[] = $clone;
        }

        return $copiedAttachmentList;
    }

    private function createHtmlizer(Params $params, User $user): Htmlizer
    {
        if (!$params->applyAcl()) {
            return $this->htmlizerFactory->createNoAcl();
        }

        return $this->htmlizerFactory->createForUser($user);
    }

    private function getEmailAddressRepository(): EmailAddressRepository
    {
        
        return $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);
    }
}
