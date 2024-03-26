<?php


namespace Espo\Modules\Crm\EntryPoints;

use Espo\Modules\Crm\Entities\Campaign;
use Espo\Modules\Crm\Entities\EmailQueueItem;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\Modules\Crm\Tools\Campaign\LogService;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\EntryPoint\Traits\NoAuth;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\ORM\EntityManager;

class CampaignTrackOpened implements EntryPoint
{
    use NoAuth;

    public function __construct(
        private EntityManager $entityManager,
        private LogService $service
    ) {}

    
    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id');

        if (!$id || !is_string($id)) {
            throw new BadRequest();
        }

        $queueItemId = $id;

        
        $queueItem = $this->entityManager->getEntity(EmailQueueItem::ENTITY_TYPE, $queueItemId);

        if (!$queueItem) {
            throw new NotFound();
        }

        $targetType = $queueItem->getTargetType();
        $targetId = $queueItem->getTargetId();

        $target = $this->entityManager->getEntityById($targetType, $targetId);

        if (!$target) {
            return;
        }

        $massEmailId = $queueItem->getMassEmailId();

        if (!$massEmailId) {
            return;
        }

        
        $massEmail = $this->entityManager->getEntityById(MassEmail::ENTITY_TYPE, $massEmailId);

        if (!$massEmail) {
            return;
        }

        $campaignId = $massEmail->getCampaignId();

        if (!$campaignId) {
            return;
        }

        $campaign = $this->entityManager->getEntityById(Campaign::ENTITY_TYPE, $campaignId);

        if (!$campaign) {
            return;
        }

        $this->service->logOpened($campaignId, $queueItem);

        header('Content-Type: image/png');

        $img  = imagecreatetruecolor(1, 1);

        if (!$img) {
            return;
        }

        imagesavealpha($img, true);

        $color = imagecolorallocatealpha($img, 127, 127, 127, 127);

        if ($color === false) {
            return;
        }

        imagefill($img, 0, 0, $color);

        imagepng($img);
        imagecolordeallocate($img, $color);
        imagedestroy($img);
    }
}
