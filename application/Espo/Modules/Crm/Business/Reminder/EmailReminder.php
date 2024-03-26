<?php


namespace Espo\Modules\Crm\Business\Reminder;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Util;
use Espo\Core\Htmlizer\HtmlizerFactory as HtmlizerFactory;
use Espo\Core\Mail\EmailSender;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Language;
use Espo\Core\Utils\TemplateFileManager;

class EmailReminder
{
    
    protected $entityManager;

    
    protected $emailSender;

    
    protected $config;

    
    protected $templateFileManager;

    
    protected $language;

    
    protected $htmlizerFactory;

    public function __construct(
        EntityManager $entityManager,
        TemplateFileManager $templateFileManager,
        EmailSender $emailSender,
        Config $config,
        HtmlizerFactory $htmlizerFactory,
        Language $language
    ) {
        $this->entityManager = $entityManager;
        $this->templateFileManager = $templateFileManager;
        $this->emailSender = $emailSender;
        $this->config = $config;
        $this->language = $language;
        $this->htmlizerFactory = $htmlizerFactory;
    }

    public function send(Entity $reminder): void
    {
        $user = $this->entityManager->getEntity('User', $reminder->get('userId'));
        $entity = $this->entityManager->getEntity($reminder->get('entityType'), $reminder->get('entityId'));

        if (!$user || !$entity) {
            return;
        }

        $emailAddress = $user->get('emailAddress');

        if (!$emailAddress) {
            return;
        }

        if (!$entity instanceof CoreEntity) {
            return;
        }

        if ($entity->hasLinkMultipleField('users')) {
            $entity->loadLinkMultipleField('users', ['status' => 'acceptanceStatus']);
            $status = $entity->getLinkMultipleColumn('users', 'status', $user->getId());

            if ($status === 'Declined') {
                return;
            }
        }

        $email = $this->entityManager->getNewEntity('Email');

        $email->set('to', $emailAddress);

        $subjectTpl = $this->templateFileManager
            ->getTemplate('reminder', 'subject', $entity->getEntityType(), 'Crm');

        $bodyTpl = $this->templateFileManager
            ->getTemplate('reminder', 'body', $entity->getEntityType(), 'Crm');

        $subjectTpl = str_replace(["\n", "\r"], '', $subjectTpl);

        $data = [];

        $siteUrl = rtrim($this->config->get('siteUrl'), '/');
        $recordUrl = $siteUrl . '/#' . $entity->getEntityType() . '/view/' . $entity->getId();

        $data['recordUrl'] = $recordUrl;
        $data['entityType'] = $this->language->translateLabel($entity->getEntityType(), 'scopeNames');
        $data['entityTypeLowerFirst'] = Util::mbLowerCaseFirst($data['entityType']);
        $data['userName'] = $user->get('name');

        $htmlizer = $this->htmlizerFactory->createForUser($user);

        $subject = $htmlizer->render(
            $entity,
            $subjectTpl,
            'reminder-email-subject-' . $entity->getEntityType(),
            $data,
            true
        );

        $body = $htmlizer->render(
            $entity,
            $bodyTpl,
            'reminder-email-body-' . $entity->getEntityType(),
            $data,
            false
        );

        $email->set('subject', $subject);
        $email->set('body', $body);
        $email->set('isHtml', true);

        $this->emailSender->send($email);
    }
}
