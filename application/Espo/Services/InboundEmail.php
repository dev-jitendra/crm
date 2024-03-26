<?php


namespace Espo\Services;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\GroupAccount\AccountFactory;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\ORM\Entity;
use Espo\Core\Exceptions\BadRequest;
use Espo\Services\Record as RecordService;
use Espo\Entities\InboundEmail as InboundEmailEntity;

use Espo\Core\Di;


class InboundEmail extends RecordService implements

    Di\CryptAware,
    Di\EmailSenderAware
{
    use Di\CryptSetter;
    use Di\EmailSenderSetter;

    protected function filterInput($data)
    {
        parent::filterInput($data);

        if (property_exists($data, 'password')) {
            $data->password = $this->crypt->encrypt($data->password);
        }

        if (property_exists($data, 'smtpPassword')) {
            $data->smtpPassword = $this->crypt->encrypt($data->smtpPassword);
        }
    }

    public function processValidation(Entity $entity, $data)
    {
        parent::processValidation($entity, $data);

        if ($entity->get('useImap')) {
            if (!$entity->get('fetchSince')) {
                throw new BadRequest("EmailAccount validation: fetchSince is required.");
            }
        }
    }

    
    public function getSmtpParamsFromAccount(InboundEmailEntity $emailAccount): ?array
    {
        $params = $this->injectableFactory
            ->create(AccountFactory::class)
            ->create($emailAccount->getId())
            ->getSmtpParams();

        if (!$params) {
            return null;
        }

        return $params->toArray();
    }
}
