<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;

use Espo\Tools\Formula\Service;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\ForbiddenSilent;

use Espo\Entities\User;

use Espo\Core\Field\LinkParent;

use stdClass;

class Formula
{
    private Service $service;

    public function __construct(Service $service, User $user)
    {
        $this->service = $service;

        if (!$user->isAdmin()) {
            throw new ForbiddenSilent();
        }
    }

    public function postActionCheckSyntax(Request $request): stdClass
    {
        $expression = $request->getParsedBody()->expression ?? null;

        if (!$expression || !is_string($expression)) {
            throw new BadRequest("No or non-string expression.");
        }

        return $this->service->checkSyntax($expression)->toStdClass();
    }

    public function postActionRun(Request $request): stdClass
    {
        $expression = $request->getParsedBody()->expression ?? null;
        $targetType = $request->getParsedBody()->targetType ?? null;
        $targetId = $request->getParsedBody()->targetId ?? null;

        if (!$expression || !is_string($expression)) {
            throw new BadRequest("No or non-string expression.");
        }

        $targetLink = null;

        if ($targetType && $targetId) {
            $targetLink = LinkParent::create($targetType, $targetId);
        }

        return $this->service->run($expression, $targetLink)->toStdClass();
    }
}
