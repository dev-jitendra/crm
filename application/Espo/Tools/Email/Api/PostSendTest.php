<?php


namespace Espo\Tools\Email\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Core\Mail\SmtpParams;
use Espo\Entities\Email;
use Espo\Tools\Email\SendService;
use Espo\Tools\Email\TestSendData;


class PostSendTest implements Action
{
    public function __construct(
        private SendService $sendService,
        private Acl $acl
    ) {}

    
    public function process(Request $request): Response
    {
        if (!$this->acl->checkScope(Email::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        $data = $request->getParsedBody();

        $type = $data->type ?? null;
        $id = $data->id ?? null;
        $server = $data->server ?? null;
        $port = $data->port ?? null;
        $username = $data->username ?? null;
        $password = $data->password ?? null;
        $auth = $data->auth ?? null;
        $authMechanism = $data->authMechanism ?? null;
        $security = $data->security ?? null;
        $userId = $data->userId ?? null;
        $fromAddress = $data->fromAddress ?? null;
        $fromName = $data->fromName ?? null;
        $emailAddress = $data->emailAddress ?? null;

        if (!is_string($server)) {
            throw new BadRequest("No `server`");
        }

        if (!is_int($port)) {
            throw new BadRequest("No or bad `port`.");
        }

        if (!is_string($emailAddress)) {
            throw new BadRequest("No `emailAddress`.");
        }

        $smtpParams = SmtpParams
            ::create($server, $port)
            ->withSecurity($security)
            ->withFromName($fromName)
            ->withFromAddress($fromAddress)
            ->withAuth($auth);

        if ($auth) {
            $smtpParams = $smtpParams
                ->withUsername($username)
                ->withPassword($password)
                ->withAuthMechanism($authMechanism);
        }

        $data = new TestSendData($emailAddress, $type, $id, $userId);

        $this->sendService->sendTestEmail($smtpParams, $data);

        return ResponseComposer::json(true);
    }
}
