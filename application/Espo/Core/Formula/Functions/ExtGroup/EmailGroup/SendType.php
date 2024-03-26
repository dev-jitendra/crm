<?php


namespace Espo\Core\Formula\Functions\ExtGroup\EmailGroup;

use Espo\Core\ApplicationUser;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Utils\SystemUser;
use Espo\Entities\Email;
use Espo\Tools\Email\SendService;

use Espo\Core\Di;

use Exception;

class SendType extends BaseFunction implements
    Di\EntityManagerAware,
    Di\ServiceFactoryAware,
    Di\ConfigAware,
    Di\InjectableFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\ServiceFactorySetter;
    use Di\ConfigSetter;
    use Di\InjectableFactorySetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $args = $this->evaluate($args);

        $id = $args[0];

        if (!$id || !is_string($id)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $em = $this->entityManager;

        
        $email = $em->getEntityById(Email::ENTITY_TYPE, $id);

        if (!$email) {
            $this->log("Email '{$id}' does not exist.");

            return false;
        }

        $status = $email->getStatus();

        if ($status === Email::STATUS_SENT) {
            $this->log("Can't send email that has 'Sent' status.");

            return false;
        }

        
        $service = $this->serviceFactory->create(Email::ENTITY_TYPE);

        $service->loadAdditionalFields($email);

        $toSave = false;

        if ($status !== Email::STATUS_SENDING) {
            $email->set('status', Email::STATUS_SENDING);

            $toSave = true;
        }

        if (!$email->get('from')) {
            $from = $this->config->get('outboundEmailFromAddress');

            if ($from) {
                $email->set('from', $from);

                $toSave = true;
            }
        }

        $systemUserId = $this->injectableFactory->create(SystemUser::class)->getId();

        if ($toSave) {
            $em->saveEntity($email, [
                SaveOption::SILENT => true,
                SaveOption::MODIFIED_BY_ID => $systemUserId,
            ]);
        }

        $sendService = $this->injectableFactory->create(SendService::class);

        try {
            $sendService->send($email);
        }
        catch (Exception $e) {
            $message = $e->getMessage();
            $this->log("Error while sending. Message: {$message}." , 'error');

            return false;
        }

        return true;
    }
}
