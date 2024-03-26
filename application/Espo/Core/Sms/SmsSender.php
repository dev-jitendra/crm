<?php


namespace Espo\Core\Sms;

use Espo\Core\InjectableFactory;
use Espo\Entities\Sms as SmsEntity;
use Espo\Core\Utils\Config;

class SmsSender
{
    private ?Sender $sender = null;

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Config $config
    ) {}

    private function getSender(): Sender
    {
        if ($this->sender === null) {
            
            
            
            $this->sender = $this->injectableFactory->createResolved(Sender::class);
        }

        return $this->sender;
    }

    public function send(SmsEntity $sms): void
    {
        $systemFromNumber = $this->config->get('outboundSmsFromNumber');

        if ($sms->getFromNumber() === null && $systemFromNumber) {
            $sms->setFromNumber($systemFromNumber);
        }

        $this->getSender()->send($sms);

        $sms->setAsSent();
    }
}
