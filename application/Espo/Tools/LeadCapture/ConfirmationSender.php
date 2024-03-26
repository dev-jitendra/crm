<?php


namespace Espo\Tools\LeadCapture;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\GroupAccount\AccountFactory;
use Espo\Core\Mail\EmailSender;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Templates\Entities\Person;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime;
use Espo\Core\Utils\Language;
use Espo\Entities\Email;
use Espo\Entities\EmailTemplate;
use Espo\Entities\LeadCapture as LeadCaptureEntity;
use Espo\Entities\UniqueId;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\EntityManager;
use Espo\Tools\EmailTemplate\Data as EmailTemplateData;
use Espo\Tools\EmailTemplate\Params as EmailTemplateParams;
use Espo\Tools\EmailTemplate\Processor as EmailTemplateProcessor;

class ConfirmationSender
{
    private EntityManager $entityManager;
    private Config $config;
    private Language $defaultLanguage;
    private EmailSender $emailSender;
    private AccountFactory $accountFactory;
    private DateTime $dateTime;
    private EmailTemplateProcessor $emailTemplateProcessor;

    public function __construct(
        EntityManager $entityManager,
        Config $config,
        Language $defaultLanguage,
        EmailSender $emailSender,
        AccountFactory $accountFactory,
        DateTime $dateTime,
        EmailTemplateProcessor $emailTemplateProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->defaultLanguage = $defaultLanguage;
        $this->emailSender = $emailSender;
        $this->accountFactory = $accountFactory;
        $this->dateTime = $dateTime;
        $this->emailTemplateProcessor = $emailTemplateProcessor;
    }

    
    public function send(string $id): void
    {
        
        $uniqueId = $this->entityManager
            ->getRDBRepositoryByClass(UniqueId::class)
            ->where(['name' => $id])
            ->findOne();

        if (!$uniqueId) {
            throw new Error("LeadCapture: UniqueId not found.");
        }

        $uniqueIdData = $uniqueId->getData();

        if (empty($uniqueIdData->data)) {
            throw new Error("LeadCapture: data not found.");
        }

        if (empty($uniqueIdData->leadCaptureId)) {
            throw new Error("LeadCapture: leadCaptureId not found.");
        }

        $data = $uniqueIdData->data;
        $leadCaptureId = $uniqueIdData->leadCaptureId;
        $leadId = $uniqueIdData->leadId ?? null;

        $terminateAt = $uniqueId->getTerminateAt();

        if ($terminateAt && time() > strtotime($terminateAt->toString())) {
            throw new Error("LeadCapture: Opt-in confirmation expired.");
        }

        
        $leadCapture = $this->entityManager->getEntity(LeadCaptureEntity::ENTITY_TYPE, $leadCaptureId);

        if (!$leadCapture) {
            throw new Error("LeadCapture: LeadCapture not found.");
        }

        $optInConfirmationEmailTemplateId = $leadCapture->getOptInConfirmationEmailTemplateId();

        if (!$optInConfirmationEmailTemplateId) {
            throw new Error("LeadCapture: No optInConfirmationEmailTemplateId.");
        }

        
        $emailTemplate = $this->entityManager
            ->getEntityById(EmailTemplate::ENTITY_TYPE, $optInConfirmationEmailTemplateId);

        if (!$emailTemplate) {
            throw new Error("LeadCapture: EmailTemplate not found.");
        }

        if ($leadId) {
            
            $lead = $this->entityManager->getEntityById(Lead::ENTITY_TYPE, $leadId);
        }
        else {
            $lead = $this->entityManager->getNewEntity(Lead::ENTITY_TYPE);

            $lead->set($data);
        }

        if (!$lead) {
            throw new Error("Lead Capture: Could not find lead.");
        }

        $emailAddress = $lead->getEmailAddress();

        if (!$emailAddress) {
            throw new Error("Lead Capture: No lead email address.");
        }

        $emailData = $this->emailTemplateProcessor->process(
            $emailTemplate,
            EmailTemplateParams::create(),
            EmailTemplateData::create()
                ->withEntityHash([
                    Person::TEMPLATE_TYPE => $lead,
                    Lead::ENTITY_TYPE => $lead,
                ])
        );

        $subject = $emailData->getSubject();
        $body = $emailData->getBody();
        $isHtml = $emailData->isHtml();

        if (
            mb_strpos($body, '{optInUrl}') === false &&
            mb_strpos($body, '{optInLink}') === false
        ) {
            if ($isHtml) {
                $body .= "<p>{optInLink}</p>";
            } else {
                $body .= "\n\n{optInUrl}";
            }
        }

        $url = $this->config->getSiteUrl() . '/?entryPoint=confirmOptIn&id=' . $uniqueId->getIdValue();

        $linkHtml =
            '<a href='.$url.'>' .
            $this->defaultLanguage->translateLabel('Confirm Opt-In', 'labels', LeadCaptureEntity::ENTITY_TYPE) .
            '</a>';

        $body = str_replace('{optInUrl}', $url, $body);
        $body = str_replace('{optInLink}', $linkHtml, $body);

        $createdAt = $uniqueId->getCreatedAt()->toString();

        if ($createdAt) {
            $dateString = $this->dateTime->convertSystemDateTime($createdAt, null, $this->config->get('dateFormat'));
            $timeString = $this->dateTime->convertSystemDateTime($createdAt, null, $this->config->get('timeFormat'));
            $dateTimeString = $this->dateTime->convertSystemDateTime($createdAt);

            $body = str_replace('{optInDate}', $dateString, $body);
            $body = str_replace('{optInTime}', $timeString, $body);
            $body = str_replace('{optInDateTime}', $dateTimeString, $body);
        }

        
        $email = $this->entityManager->getNewEntity(Email::ENTITY_TYPE);

        $email
            ->setSubject($subject)
            ->setBody($body)
            ->setIsHtml($isHtml)
            ->addToAddress($emailAddress);

        $smtpParams = null;

        $inboundEmailId = $leadCapture->getInboundEmailId();

        if ($inboundEmailId) {
            $account = $this->accountFactory->create($inboundEmailId);

            if (!$account->isAvailableForSending()) {
                throw new Error("Lead Capture: Group email account {$inboundEmailId} can't be used for sending.");
            }

            $smtpParams = $account->getSmtpParams();
        }

        $sender = $this->emailSender->create();

        if ($smtpParams) {
            $sender->withSmtpParams($smtpParams);
        }

        $sender
            ->withAttachments($emailData->getAttachmentList())
            ->send($email);
    }
}
