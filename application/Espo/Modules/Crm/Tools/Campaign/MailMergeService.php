<?php


namespace Espo\Modules\Crm\Tools\Campaign;

use Espo\Core\Acl;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Language;
use Espo\Entities\Template;
use Espo\Modules\Crm\Entities\Campaign as CampaignEntity;
use Espo\Modules\Crm\Entities\TargetList;
use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityCollection;
use Espo\ORM\EntityManager;

class MailMergeService
{
    
    protected $entityTypeAddressFieldListMap = [
        'Account' => ['billingAddress', 'shippingAddress'],
        'Contact' => ['address'],
        'Lead' => ['address'],
        'User' => [],
    ];

    
    protected $targetLinkList = [
        'accounts',
        'contacts',
        'leads',
        'users',
    ];

    private EntityManager $entityManager;
    private Acl $acl;
    private Language $defaultLanguage;
    private MailMergeGenerator $generator;

    public function __construct(
        EntityManager $entityManager,
        Acl $acl,
        Language $defaultLanguage,
        MailMergeGenerator $generator
    ) {
        $this->entityManager = $entityManager;
        $this->acl = $acl;
        $this->defaultLanguage = $defaultLanguage;
        $this->generator = $generator;
    }

    
    public function generate(string $campaignId, string $link, bool $checkAcl = true): string
    {
        
        $campaign = $this->entityManager->getEntityById(CampaignEntity::ENTITY_TYPE, $campaignId);

        if ($checkAcl && !$this->acl->checkEntityRead($campaign)) {
            throw new Forbidden();
        }

        
        $targetEntityType = $campaign->getRelationParam($link, 'entity');

        if ($checkAcl && !$this->acl->check($targetEntityType, Acl\Table::ACTION_READ)) {
            throw new Forbidden("Could not mail merge campaign because access to target entity type is forbidden.");
        }

        if (!in_array($link, $this->targetLinkList)) {
            throw new BadRequest();
        }

        if ($campaign->getType() !== CampaignEntity::TYPE_MAIL) {
            throw new Error("Could not mail merge campaign not of Mail type.");
        }

        $templateId = $campaign->get($link . 'TemplateId');

        if (!$templateId) {
            throw new Error("Could not mail merge campaign w/o specified template.");
        }

        
        $template = $this->entityManager->getEntityById(Template::ENTITY_TYPE, $templateId);

        if (!$template) {
            throw new Error("Template not found.");
        }

        if ($template->getTargetEntityType() !== $targetEntityType) {
            throw new Error("Template is not of proper entity type.");
        }

        $campaign->loadLinkMultipleField('targetLists');
        $campaign->loadLinkMultipleField('excludingTargetLists');

        if (count($campaign->getLinkMultipleIdList('targetLists')) === 0) {
            throw new Error("Could not mail merge campaign w/o any specified target list.");
        }

        $metTargetHash = [];
        $targetEntityList = [];

        
        $excludingTargetListList = $this->entityManager
            ->getRDBRepository(CampaignEntity::ENTITY_TYPE)
            ->getRelation($campaign, 'excludingTargetLists')
            ->find();

        foreach ($excludingTargetListList as $excludingTargetList) {
            $recordList = $this->entityManager
                ->getRDBRepository(TargetList::ENTITY_TYPE)
                ->getRelation($excludingTargetList, $link)
                ->find();

            foreach ($recordList as $excludingTarget) {
                $hashId = $excludingTarget->getEntityType() . '-' . $excludingTarget->getId();
                $metTargetHash[$hashId] = true;
            }
        }

        $addressFieldList = $this->entityTypeAddressFieldListMap[$targetEntityType];

        
        $targetListCollection = $this->entityManager
            ->getRDBRepository(CampaignEntity::ENTITY_TYPE)
            ->getRelation($campaign, 'targetLists')
            ->find();

        foreach ($targetListCollection as $targetList) {
            if (!$campaign->get($link . 'TemplateId')) {
                continue;
            }

            $entityList = $this->entityManager
                ->getRDBRepository(TargetList::ENTITY_TYPE)
                ->getRelation($targetList, $link)
                ->where([
                    '@relation.optedOut' => false,
                ])
                ->find();

            foreach ($entityList as $e) {
                $hashId = $e->getEntityType() . '-'. $e->getId();

                if (!empty($metTargetHash[$hashId])) {
                    continue;
                }

                $metTargetHash[$hashId] = true;

                if ($campaign->get('mailMergeOnlyWithAddress')) {
                    if (empty($addressFieldList)) {
                        continue;
                    }

                    $hasAddress = false;

                    foreach ($addressFieldList as $addressField) {
                        if (
                            $e->get($addressField . 'Street') ||
                            $e->get($addressField . 'PostalCode')
                        ) {
                            $hasAddress = true;
                            break;
                        }
                    }

                    if (!$hasAddress) {
                        continue;
                    }
                }

                $targetEntityList[] = $e;
            }
        }

        if (empty($targetEntityList)) {
            throw new Error("No targets available for mail merge.");
        }

        $filename = $campaign->getName() . ' - ' .
            $this->defaultLanguage->translateLabel($targetEntityType, 'scopeNamesPlural');

        
        $collection = $this->entityManager
            ->getCollectionFactory()
            ->create($targetEntityType, $targetEntityList);

        return $this->generator->generate(
            $collection,
            $template,
            $campaign->getId(),
            $filename
        );
    }
}
