<?php


namespace Espo\EntryPoints;

use Espo\Core\Exceptions\BadRequest;
use Espo\Entities\PasswordChangeRequest;
use Espo\Core\Utils\Client\ActionRenderer;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\EntryPoint\Traits\NoAuth;
use Espo\Core\Utils\Config;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\ORM\EntityManager;

class ChangePassword implements EntryPoint
{
    use NoAuth;

    public function __construct(
        private Config $config,
        private EntityManager $entityManager,
        private ActionRenderer $actionRenderer
    ) {}

    public function run(Request $request, Response $response): void
    {
        $requestId = $request->getQueryParam('id');

        if (!$requestId) {
            throw new BadRequest();
        }

        $passwordChangeRequest = $this->entityManager
            ->getRDBRepository(PasswordChangeRequest::ENTITY_TYPE)
            ->where([
                'requestId' => $requestId,
            ])
            ->findOne();

        $strengthParams = [
            'passwordGenerateLength' => $this->config->get('passwordGenerateLength'),
            'passwordGenerateLetterCount' => $this->config->get('passwordGenerateLetterCount'),
            'generateNumberCount' => $this->config->get('generateNumberCount'),
            'passwordStrengthLength' => $this->config->get('passwordStrengthLength'),
            'passwordStrengthLetterCount' => $this->config->get('passwordStrengthLetterCount'),
            'passwordStrengthNumberCount' => $this->config->get('passwordStrengthNumberCount'),
            'passwordStrengthBothCases' => $this->config->get('passwordStrengthBothCases'),
        ];

        $options = [
            'id' => $requestId,
            'strengthParams' => $strengthParams,
            'notFound' => !$passwordChangeRequest,
        ];

        $params = new ActionRenderer\Params('controllers/password-change-request', 'passwordChange', $options);

        $this->actionRenderer->write($response, $params);
    }
}
