<?php


namespace Espo\Modules\Crm\Business\Event;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Entities\Attachment;
use Espo\Entities\Email;
use Espo\Entities\UniqueId;
use Laminas\Mail\Message;

use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Core\Utils\Util;
use Espo\Core\Htmlizer\HtmlizerFactory as HtmlizerFactory;
use Espo\Core\Mail\EmailSender;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Language;
use Espo\Core\Utils\NumberUtil;
use Espo\Core\Utils\TemplateFileManager;

use DateTime;

class Invitations
{
    private const TYPE_INVITATION = 'invitation';
    private const TYPE_CANCELLATION = 'cancellation';

    private $smtpParams;
    private $entityManager;
    private $emailSender;
    private $config;
    private $dateTime; 
    private $language;
    private $number; 
    private $templateFileManager;
    private $fileManager; 
    private $htmlizerFactory;

    
    public function __construct(
        EntityManager $entityManager,
        ?SmtpParams $smtpParams,
        EmailSender $emailSender,
        Config $config,
        FileManager $fileManager,
        DateTimeUtil $dateTime,
        NumberUtil $number,
        Language $language,
        TemplateFileManager $templateFileManager,
        HtmlizerFactory $htmlizerFactory
    ) {
        $this->entityManager = $entityManager;
        $this->smtpParams = $smtpParams;
        $this->emailSender = $emailSender;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->language = $language;
        $this->number = $number;
        $this->fileManager = $fileManager;
        $this->templateFileManager = $templateFileManager;
        $this->htmlizerFactory = $htmlizerFactory;
    }

    
    public function sendInvitation(Entity $entity, Entity $invitee, string $link): void
    {
        $this->sendInternal($entity, $invitee, $link);
    }

    
    public function sendCancellation(Entity $entity, Entity $invitee, string $link): void
    {
        $this->sendInternal($entity, $invitee, $link, self::TYPE_CANCELLATION);
    }

    
    private function sendInternal(
        Entity $entity,
        Entity $invitee,
        string $link,
        string $type = self::TYPE_INVITATION
    ): void {

        $uid = $type === self::TYPE_INVITATION ?
            $this->createUniqueId($entity, $invitee, $link) : null;

        $emailAddress = $invitee->get('emailAddress');

        if (empty($emailAddress)) {
            return;
        }

        
        $email = $this->entityManager->getNewEntity(Email::ENTITY_TYPE);

        $email->set('to', $emailAddress);

        $subjectTpl = $this->templateFileManager->getTemplate($type, 'subject', $entity->getEntityType(), 'Crm');
        $bodyTpl = $this->templateFileManager->getTemplate($type, 'body', $entity->getEntityType(), 'Crm');

        $subjectTpl = str_replace(["\n", "\r"], '', $subjectTpl);

        $data = [];

        $siteUrl = rtrim($this->config->get('siteUrl'), '/');
        $recordUrl = $siteUrl . '/#' . $entity->getEntityType() . '/view/' . $entity->getId();

        $data['recordUrl'] = $recordUrl;

        if ($uid) {
            $part = $siteUrl . '?entryPoint=eventConfirmation&action=';

            $data['acceptLink'] = $part . 'accept&uid=' . $uid->getIdValue();
            $data['declineLink'] = $part . 'decline&uid=' . $uid->getIdValue();
            $data['tentativeLink'] = $part . 'tentative&uid=' . $uid->getIdValue();
        }

        if ($invitee instanceof User) {
            $data['isUser'] = true;

            $htmlizer = $this->htmlizerFactory->createForUser($invitee);
        }
        else {
            $htmlizer = $this->htmlizerFactory->createNoAcl();
        }

        $data['inviteeName'] = $invitee->get('name');
        $data['entityType'] = $this->language->translateLabel($entity->getEntityType(), 'scopeNames');
        $data['entityTypeLowerFirst'] = Util::mbLowerCaseFirst($data['entityType']);

        $subject = $htmlizer->render(
            $entity,
            $subjectTpl,
            $type . '-email-subject-' . $entity->getEntityType(),
            $data,
            true,
            true
        );

        $body = $htmlizer->render(
            $entity,
            $bodyTpl,
            $type . '-email-body-' . $entity->getEntityType(),
            $data,
            false,
            true
        );

        $email->set('subject', $subject);
        $email->set('body', $body);
        $email->set('isHtml', true);

        $attachmentName = ucwords($this->language->translateLabel($entity->getEntityType(), 'scopeNames')) . '.ics';

        
        $attachment = $this->entityManager->getNewEntity(Attachment::ENTITY_TYPE);

        $attachment->set([
            'name' => $attachmentName,
            'type' => 'text/calendar',
            'contents' => $this->getIcsContents($entity, $type),
        ]);

        $message = new Message();

        $sender = $this->emailSender->create();

        if ($this->smtpParams) {
            $sender->withSmtpParams($this->smtpParams);
        }

        $sender
            ->withMessage($message)
            ->withAttachments([$attachment])
            ->send($email);
    }

    private function createUniqueId(Entity $entity, Entity $invitee, string $link): UniqueId
    {
        
        $uid = $this->entityManager->getNewEntity(UniqueId::ENTITY_TYPE);

        $uid->set('data', [
            'eventType' => $entity->getEntityType(),
            'eventId' => $entity->getId(),
            'inviteeId' => $invitee->getId(),
            'inviteeType' => $invitee->getEntityType(),
            'link' => $link,
            'dateStart' => $entity->get('dateStart'),
        ]);

        if ($entity->get('dateEnd')) {
            $terminateAt = $entity->get('dateEnd');
        }
        else {
            $dt = new DateTime();
            $dt->modify('+1 month');

            $terminateAt = $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
        }

        $uid->set([
            'targetId' => $entity->getId(),
            'targetType' => $entity->getEntityType(),
            'terminateAt' => $terminateAt,
        ]);

        $this->entityManager->saveEntity($uid);

        return $uid;
    }

    protected function getIcsContents(Entity $entity, string $type): string
    {
        
        $user = $this->entityManager
            ->getRDBRepository($entity->getEntityType())
            ->getRelation($entity, 'assignedUser')
            ->findOne();

        $who = '';
        $email = '';

        if ($user) {
            $who = $user->getName();
            $email = $user->getEmailAddress();
        }

        $status = $type === self::TYPE_CANCELLATION ?
            Ics::STATUS_CANCELLED :
            Ics::STATUS_CONFIRMED;

        $method = $type === self::TYPE_CANCELLATION ?
            Ics::METHOD_CANCEL :
            Ics::METHOD_REQUEST;

        $ics = new Ics('
            'method' => $method,
            'startDate' => strtotime($entity->get('dateStart')),
            'endDate' => strtotime($entity->get('dateEnd')),
            'uid' => $entity->getId(),
            'summary' => $entity->get('name'),
            'who' => $who,
            'email' => $email,
            'description' => $entity->get('description'),
            'status' => $status,
        ]);

        return $ics->get();
    }
}
