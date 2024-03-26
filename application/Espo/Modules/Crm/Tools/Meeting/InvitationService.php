<?php


namespace Espo\Modules\Crm\Tools\Meeting;

use Espo\Core\Acl;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\Record\ServiceContainer as RecordServiceContainer;
use Espo\Core\Utils\Config;
use Espo\Entities\User;
use Espo\Modules\Crm\Business\Event\Invitations;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Tools\Email\SendService;

class InvitationService
{
    private const TYPE_INVITATION = 'invitation';
    private const TYPE_CANCELLATION = 'cancellation';

    private RecordServiceContainer $recordServiceContainer;
    private SendService $sendService;
    private User $user;
    private InjectableFactory $injectableFactory;
    private Acl $acl;
    private EntityManager $entityManager;
    private Config $config;

    public function __construct(
        RecordServiceContainer $recordServiceContainer,
        SendService $sendService,
        User $user,
        InjectableFactory $injectableFactory,
        Acl $acl,
        EntityManager $entityManager,
        Config $config
    ) {
        $this->recordServiceContainer = $recordServiceContainer;
        $this->sendService = $sendService;
        $this->user = $user;
        $this->injectableFactory = $injectableFactory;
        $this->acl = $acl;
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    
    public function send(string $entityType, string $id, ?array $targets = null): array
    {
        return $this->sendInternal($entityType, $id, $targets);
    }

    
    public function sendCancellation(string $entityType, string $id, ?array $targets = null): array
    {
        return $this->sendInternal($entityType, $id, $targets, self::TYPE_CANCELLATION);
    }

    
    private function sendInternal(
        string $entityType,
        string $id,
        ?array $targets = null,
        string $type = self::TYPE_INVITATION
    ): array {

        $entity = $this->recordServiceContainer
            ->get($entityType)
            ->getEntity($id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden("No edit access.");
        }

        $linkList = [
            'users',
            'contacts',
            'leads',
        ];

        $sender = $this->getSender();

        $sentAddressList = [];
        $resultEntityList = [];

        foreach ($linkList as $link) {
            $builder = $this->entityManager
                ->getRDBRepository($entityType)
                ->getRelation($entity, $link);

            if ($targets === null && $type === self::TYPE_INVITATION) {
                $builder->where([
                    '@relation.status=' => Meeting::ATTENDEE_STATUS_NONE,
                ]);
            }

            $collection = $builder->find();

            foreach ($collection as $attendee) {
                if ($targets && !self::isInTargets($attendee, $targets)) {
                    continue;
                }

                $emailAddress = $attendee->get('emailAddress');

                if (!$emailAddress || in_array($emailAddress, $sentAddressList)) {
                    continue;
                }

                if ($type === self::TYPE_INVITATION) {
                    $sender->sendInvitation($entity, $attendee, $link);
                }

                if ($type === self::TYPE_CANCELLATION) {
                    $sender->sendCancellation($entity, $attendee, $link);
                }

                $sentAddressList[] = $emailAddress;
                $resultEntityList[] = $attendee;

                $this->entityManager
                    ->getRDBRepository($entityType)
                    ->getRelation($entity, $link)
                    ->updateColumns($attendee, ['status' => Meeting::ATTENDEE_STATUS_NONE]);
            }
        }

        return $resultEntityList;
    }

    
    private static function isInTargets(Entity $entity, array $targets): bool
    {
        foreach ($targets as $target) {
            if (
                $entity->getEntityType() === $target->getEntityType() &&
                $entity->getId() === $target->getId()
            ) {
                return true;
            }
        }

        return false;
    }

    private function getSender(): Invitations
    {
        $smtpParams = !$this->config->get('eventInvitationForceSystemSmtp') ?
            $this->sendService->getUserSmtpParams($this->user->getId()) :
            null;

        $builder = BindingContainerBuilder::create();

        if ($smtpParams) {
            $builder->bindInstance(SmtpParams::class, $smtpParams);
        }

        return $this->injectableFactory->createWithBinding(Invitations::class, $builder->build());
    }
}
