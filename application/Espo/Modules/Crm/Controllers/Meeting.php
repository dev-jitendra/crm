<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Api\Response;
use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;

use Espo\Core\Api\Request;

use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Utils\Json;
use Espo\Modules\Crm\Entities\Meeting as MeetingEntity;
use Espo\Modules\Crm\Tools\Meeting\InvitationService;
use Espo\Modules\Crm\Tools\Meeting\Invitee;
use Espo\Modules\Crm\Tools\Meeting\Service;
use stdClass;

class Meeting extends Record
{
    
    public function postActionSendInvitations(Request $request): bool
    {
        $id = $request->getParsedBody()->id ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        $invitees = $this->fetchInvitees($request);

        $resultList = $this->injectableFactory
            ->create(InvitationService::class)
            ->send(MeetingEntity::ENTITY_TYPE, $id, $invitees);

        return $resultList !== 0;
    }

    
    public function postActionSendCancellation(Request $request): bool
    {
        $id = $request->getParsedBody()->id ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        $invitees = $this->fetchInvitees($request);

        $resultList = $this->injectableFactory
            ->create(InvitationService::class)
            ->sendCancellation(MeetingEntity::ENTITY_TYPE, $id, $invitees);

        return $resultList !== 0;
    }

    
    private function fetchInvitees(Request $request): ?array
    {
        $targets = $request->getParsedBody()->targets ?? null;

        if ($targets === null) {
            return null;
        }

        if (!is_array($targets)) {
            throw new BadRequest();
        }

        $invitees = [];

        foreach ($targets as $target) {
            if (!$target instanceof stdClass) {
                throw new BadRequest();
            }

            $targetEntityType = $target->entityType ?? null;
            $targetId = $target->id ?? null;

            if (!is_string($targetEntityType) || !is_string($targetId)) {
                throw new BadRequest();
            }

            $invitees[] = new Invitee($targetEntityType, $targetId);
        }

        return $invitees;
    }

    
    public function postActionMassSetHeld(Request $request): bool
    {
        $ids = $request->getParsedBody()->ids ?? null;

        if (!is_array($ids)) {
            throw new BadRequest("No `ids`.");
        }

        $this->injectableFactory
            ->create(Service::class)
            ->massSetHeld(MeetingEntity::ENTITY_TYPE, $ids);

        return true;
    }

    
    public function postActionMassSetNotHeld(Request $request): bool
    {
        $ids = $request->getParsedBody()->ids ?? null;

        if (!is_array($ids)) {
            throw new BadRequest("No `ids`.");
        }

        $this->injectableFactory
            ->create(Service::class)
            ->massSetNotHeld(MeetingEntity::ENTITY_TYPE, $ids);

        return true;
    }

    
    public function postActionSetAcceptanceStatus(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id) || empty($data->status)) {
            throw new BadRequest();
        }

        $this->injectableFactory
            ->create(Service::class)
            ->setAcceptance(MeetingEntity::ENTITY_TYPE, $data->id, $data->status);

        return true;
    }

    
    public function getActionAttendees(Request $request, Response $response): void
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $collection = $this->injectableFactory
            ->create(Service::class)
            ->getAttendees(MeetingEntity::ENTITY_TYPE, $id);

        $response->writeBody(
            Json::encode(['list' => $collection->getValueMapList()])
        );
    }
}
