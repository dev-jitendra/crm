<?php


namespace Espo\Core\Mail;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Account\SendingAccountProvider;
use Espo\Core\Utils\Config;
use Espo\Entities\Attachment;
use Espo\Entities\Email;

use Laminas\Mail\Message;


class EmailSender
{
    public function __construct(
        private Config $config,
        private SendingAccountProvider $accountProvider,
        private InjectableFactory $injectableFactory
    ) {}

    private function createSender(): Sender
    {
        return $this->injectableFactory->createWithBinding(
            Sender::class,
            BindingContainerBuilder
                ::create()
                ->bindInstance(SendingAccountProvider::class, $this->accountProvider)
                ->build()
        );
    }

    
    public function create(): Sender
    {
        return $this->createSender();
    }

    
    public function withParams($params): Sender
    {
        return $this->createSender()->withParams($params);
    }

    
    public function withSmtpParams($params): Sender
    {
        return $this->createSender()->withSmtpParams($params);
    }

    
    public function withAttachments(iterable $attachmentList): Sender
    {
        return $this->createSender()->withAttachments($attachmentList);
    }

    
    public function withEnvelopeOptions(array $options): Sender
    {
        return $this->createSender()->withEnvelopeOptions($options);
    }

    
    public function withMessage(Message $message): Sender
    {
        return $this->createSender()->withMessage($message);
    }

    
    public function hasSystemSmtp(): bool
    {
        if ($this->config->get('smtpServer')) {
            return true;
        }

        if ($this->accountProvider->getSystem()) {
            return true;
        }

        return false;
    }

    
    public function send(Email $email): void
    {
        $this->createSender()->send($email);
    }

    
    static public function generateMessageId(Email $email): string
    {
        return Sender::generateMessageId($email);
    }
}
