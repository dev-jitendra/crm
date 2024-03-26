<?php


namespace Espo\Core\Formula\Functions\ExtGroup\SmsGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Sms\SmsSender;
use Espo\Entities\Sms;

use Espo\Core\Di;

use Exception;

class SendType extends BaseFunction implements

    Di\EntityManagerAware,
    Di\InjectableFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\InjectableFactorySetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $evaluatedArgs = $this->evaluate($args);

        $id = $evaluatedArgs[0];

        if (!$id || !is_string($id)) {
            $this->throwBadArgumentType(1, 'string');
        }

        
        $sms = $this->entityManager->getEntity(Sms::ENTITY_TYPE, $id);

        if (!$sms) {
            $this->log("Sms '{$id}' does not exist.");

            return false;
        }

        if ($sms->getStatus() === Sms::STATUS_SENT) {
            $this->log("Can't send SMS that has 'Sent' status.");

            return false;
        }

        try {
            $this->createSender()->send($sms);

            $this->entityManager->saveEntity($sms);
        }
        catch (Exception $e) {
            $message = $e->getMessage();

            $this->log("Error while sending SMS. Message: {$message}." , 'error');

            $sms->setStatus(Sms::STATUS_FAILED);

            $this->entityManager->saveEntity($sms);

            return false;
        }

        return true;
    }

    private function createSender(): SmsSender
    {
        return $this->injectableFactory->create(SmsSender::class);
    }
}
