<?php


namespace Espo\Core\Formula\Functions\ExtGroup\UserGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Job\JobSchedulerFactory;

use Espo\Tools\UserSecurity\Password\Jobs\SendAccessInfo as SendAccessInfoJob;
use Espo\Core\Job\Job\Data as JobData;

use Espo\Entities\User;

use Espo\Core\Di;

class SendAccessInfoType extends BaseFunction implements

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

        $userId = $evaluatedArgs[0];

        if (!$userId || !is_string($userId)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $user = $this->entityManager->getEntity(User::ENTITY_TYPE, $userId);

        if (!$user) {
            $this->log("User '{$userId}' does not exist.");

            return;
        }

        $this->createJobScheduledFactory()
            ->create()
            ->setClassName(SendAccessInfoJob::class)
            ->setData(
                JobData::create()->withTargetId($user->getId())
            )
            ->schedule();
    }

    private function createJobScheduledFactory(): JobSchedulerFactory
    {
        return $this->injectableFactory->create(JobSchedulerFactory::class);
    }
}
