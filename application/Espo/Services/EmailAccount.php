<?php


namespace Espo\Services;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\PersonalAccount\AccountFactory;
use Espo\Core\Mail\Exceptions\NoSmtp;

use Espo\ORM\Entity;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Record\CreateParams;

use Espo\Entities\EmailAccount as EmailAccountEntity;

use Espo\Core\Di;

use stdClass;


class EmailAccount extends Record implements

    Di\CryptAware
{
    use Di\CryptSetter;

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

    public function create(stdClass $data, CreateParams $params): Entity
    {
        if (!$this->user->isAdmin()) {
            $count = $this->entityManager
                ->getRDBRepository(EmailAccountEntity::ENTITY_TYPE)
                ->where([
                    'assignedUserId' => $this->user->getId()
                ])
                ->count();

            if ($count >= $this->config->get('maxEmailAccountCount', \PHP_INT_MAX)) {
                throw new Forbidden();
            }
        }

        $entity = parent::create($data, $params);

        if (!$this->user->isAdmin()) {
            $entity->set('assignedUserId', $this->user->getId());
        }

        $this->entityManager->saveEntity($entity);

        return $entity;
    }

    
    public function getSmtpParamsFromAccount(EmailAccountEntity $emailAccount): ?array
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
