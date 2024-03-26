<?php


namespace Espo\EntryPoints;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\EntryPoint\Traits\NoAuth;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Utils\Client\ActionRenderer;

class LoginAs implements EntryPoint
{
    use NoAuth;

    public function __construct(private ActionRenderer $actionRenderer) {}

    
    public function run(Request $request, Response $response): void
    {
        $anotherUser = $request->getQueryParam('anotherUser');

        if (!$anotherUser) {
            throw new BadRequest("No anotherUser.");
        }

        $this->actionRenderer->write(
            $response,
            ActionRenderer\Params::create('controllers/login-as', 'login')
                ->withData([
                    'anotherUser' => $anotherUser,
                    'username' => $request->getQueryParam('username'),
                ])
                ->withInitAuth()
        );
    }
}
