<?php


namespace Espo\Core\Formula\Functions\ExtGroup\EmailGroup;

use Espo\Core\Utils\SystemUser;
use Espo\Entities\Email;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Di;
use Espo\Entities\EmailTemplate;
use Espo\Tools\EmailTemplate\Data;
use Espo\Tools\EmailTemplate\Params;
use Espo\Tools\EmailTemplate\Processor;

class ApplyTemplateType extends BaseFunction implements

    Di\EntityManagerAware,
    Di\InjectableFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\InjectableFactorySetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        $args = $this->evaluate($args);

        $id = $args[0];
        $templateId = $args[1];
        $parentType = $args[2] ?? null;
        $parentId = $args[3] ?? null;

        if (!$id || !is_string($id)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!$templateId || !is_string($templateId)) {
            $this->throwBadArgumentType(2, 'string');
        }

        if ($parentType && !is_string($parentType)) {
            $this->throwBadArgumentType(3, 'string');
        }

        if ($parentId && !is_string($parentId)) {
            $this->throwBadArgumentType(4, 'string');
        }

        $em = $this->entityManager;

        
        $email = $em->getEntityById(Email::ENTITY_TYPE, $id);
        
        $emailTemplate = $em->getEntityById(EmailTemplate::ENTITY_TYPE, $templateId);

        if (!$email) {
            $this->log("Email {$id} does not exist.");

            return false;
        }

        if (!$emailTemplate) {
            $this->log("EmailTemplate {$templateId} does not exist.");

            return false;
        }

        $status = $email->getStatus();

        if ($status && $status === Email::STATUS_SENT) {
            $this->log("Can't apply template to email with 'Sent' status.");

            return false;
        }

        $processor = $this->injectableFactory->create(Processor::class);

        $params = Params::create()
            ->withCopyAttachments(true)
            ->withApplyAcl(false);

        $data = Data::create();

        if (!$parentType || !$parentId) {
            $parentType = $email->getParentType();
            $parentId = $email->getParentId();
        }

        if ($parentType && $parentId) {
            $data = $data
                ->withParentId($parentId)
                ->withParentType($parentType);
        }

        $data = $data->withEmailAddress(
            $email->getToAddressList()[0] ?? null
        );

        $emailData = $processor->process($emailTemplate, $params, $data);

        $attachmentsIdList = $email->getLinkMultipleIdList('attachments');

        $attachmentsIdList = array_merge(
            $attachmentsIdList,
            $emailData->getAttachmentIdList()
        );

        $email
            ->setSubject($emailData->getSubject())
            ->setBody($emailData->getBody())
            ->setIsHtml($emailData->isHtml())
            ->setAttachmentIdList($attachmentsIdList);

        $systemUserId = $this->injectableFactory->create(SystemUser::class)->getId();

        $em->saveEntity($email, [
            'modifiedById' => $systemUserId,
        ]);

        return true;
    }
}
