<?php


namespace Espo\Core\FieldProcessing\Stream;

use Espo\ORM\Entity;
use Espo\Core\Acl;
use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\Tools\Stream\Service as StreamService;


class FollowersLoader implements LoaderInterface
{
    private const FOLLOWERS_LIMIT = 6;

    public function __construct(
        private StreamService $streamService,
        private Metadata $metadata,
        private User $user,
        private Acl $acl,
        private Config $config
    ) {}

    public function process(Entity $entity, Params $params): void
    {
        $this->processIsFollowed($entity);
        $this->processFollowers($entity);
    }

    public function processIsFollowed(Entity $entity): void
    {
        if (!$entity->hasAttribute('isFollowed')) {
            return;
        }

        $isFollowed = $this->streamService->checkIsFollowed($entity);

        $entity->set('isFollowed', $isFollowed);
    }

    public function processFollowers(Entity $entity): void
    {
        if ($this->user->isPortal()) {
            return;
        }

        if (!$this->metadata->get(['scopes', $entity->getEntityType(), 'stream'])) {
            return;
        }

        if (!$this->acl->checkEntityStream($entity)) {
            return;
        }

        $limit = $this->config->get('recordFollowersLoadLimit') ?? self::FOLLOWERS_LIMIT;

        $data = $this->streamService->getEntityFollowers($entity, 0, $limit);

        $entity->set('followersIds', $data['idList']);
        $entity->set('followersNames', $data['nameMap']);
    }
}
