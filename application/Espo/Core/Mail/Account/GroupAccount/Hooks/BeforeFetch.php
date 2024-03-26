<?php


namespace Espo\Core\Mail\Account\GroupAccount\Hooks;

use Espo\Core\Mail\Account\Hook\BeforeFetch as BeforeFetchInterface;
use Espo\Core\Mail\Account\Hook\BeforeFetchResult;
use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Message;
use Espo\Core\Mail\Account\GroupAccount\BouncedRecognizer;
use Espo\Core\Utils\Log;
use Espo\Entities\EmailAddress;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\Modules\Crm\Entities\EmailQueueItem;
use Espo\Modules\Crm\Tools\Campaign\LogService as CampaignService;

use Throwable;

class BeforeFetch implements BeforeFetchInterface
{
    private Log $log;
    private EntityManager $entityManager;
    private BouncedRecognizer $bouncedRecognizer;
    private CampaignService $campaignService;

    public function __construct(
        Log $log,
        EntityManager $entityManager,
        BouncedRecognizer $bouncedRecognizer,
        CampaignService $campaignService
    ) {
        $this->log = $log;
        $this->entityManager = $entityManager;
        $this->bouncedRecognizer = $bouncedRecognizer;
        $this->campaignService = $campaignService;
    }

    public function process(Account $account, Message $message): BeforeFetchResult
    {
        if ($this->bouncedRecognizer->isBounced($message)) {
            try {
                $toSkip = $this->processBounced($message);
            }
            catch (Throwable $e) {
                $this->log->error(
                    'InboundEmail ' . $account->getId() . ' ' .
                    'Process Bounced Message; ' . $e->getCode() . ' ' . $e->getMessage()
                );

                return BeforeFetchResult::create()->withToSkip();
            }

            if ($toSkip) {
                return BeforeFetchResult::create()->withToSkip();
            }
        }

        return BeforeFetchResult::create()
            ->with('skipAutoReply', $this->checkMessageCannotBeAutoReplied($message))
            ->with('isAutoReply', $this->checkMessageIsAutoReply($message));
    }

    private function processBounced(Message $message): bool
    {
        $isHard = $this->bouncedRecognizer->isHard($message);
        $queueItemId = $this->bouncedRecognizer->extractQueueItemId($message);

        if (!$queueItemId) {
            return false;
        }

        
        $queueItem = $this->entityManager->getEntityById(EmailQueueItem::ENTITY_TYPE, $queueItemId);

        if (!$queueItem) {
            return false;
        }

        $massEmail = null;
        $campaignId = null;
        $massEmailId = $queueItem->getMassEmailId();

        if ($massEmailId) {
            
            $massEmail = $this->entityManager->getEntityById(MassEmail::ENTITY_TYPE, $massEmailId);
        }

        if ($massEmail) {
            $campaignId = $massEmail->getCampaignId();
        }

        $emailAddress = $queueItem->getEmailAddress();

        if (!$emailAddress) {
            return true;
        }

        
        $emailAddressRepository = $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);

        if ($isHard) {
            $emailAddressEntity = $emailAddressRepository->getByAddress($emailAddress);

            if ($emailAddressEntity) {
                $emailAddressEntity->set('invalid', true);

                $this->entityManager->saveEntity($emailAddressEntity);
            }
        }

        $targetType = $queueItem->getTargetType();
        $targetId = $queueItem->getTargetId();

        $target = $this->entityManager->getEntityById($targetType, $targetId);

        if ($campaignId && $target) {
            $this->campaignService->logBounced($campaignId, $queueItem, $isHard);
        }

        return true;
    }

    private function checkMessageIsAutoReply(Message $message): bool
    {
        if ($message->getHeader('X-Autoreply')) {
            return true;
        }

        if ($message->getHeader('X-Autorespond')) {
            return true;
        }

        if (
            $message->getHeader('Auto-submitted') &&
            strtolower($message->getHeader('Auto-submitted')) !== 'no'
        ) {
            return true;
        }

        return false;
    }

    private function checkMessageCannotBeAutoReplied(Message $message): bool
    {
        if ($message->getHeader('X-Auto-Response-Suppress') === 'AutoReply') {
            return true;
        }

        if ($message->getHeader('X-Auto-Response-Suppress') === 'All') {
            return true;
        }

        if ($this->checkMessageIsAutoReply($message)) {
            return true;
        }

        return false;
    }
}
