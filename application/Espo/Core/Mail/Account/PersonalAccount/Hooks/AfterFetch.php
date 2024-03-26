<?php


namespace Espo\Core\Mail\Account\PersonalAccount\Hooks;

use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Account\Hook\BeforeFetchResult;
use Espo\Core\Mail\Account\Hook\AfterFetch as AfterFetchInterface;
use Espo\Tools\Stream\Service as StreamService;
use Espo\Entities\Email;
use Espo\ORM\EntityManager;

class AfterFetch implements AfterFetchInterface
{
    public function __construct(
        private EntityManager $entityManager,
        private StreamService $streamService
    ) {}

    public function process(Account $account, Email $email, BeforeFetchResult $beforeFetchResult): void
    {
        if (!$email->isFetched()) {
            $this->noteAboutEmail($email);
        }
    }

    private function noteAboutEmail(Email $email): void
    {
        $parentLink = $email->getParent();

        if (!$parentLink) {
            return;
        }

        $parent = $this->entityManager->getEntity($parentLink->getEntityType(), $parentLink->getId());

        if (!$parent) {
            return;
        }

        $this->streamService->noteEmailReceived($parent, $email);
    }
}
