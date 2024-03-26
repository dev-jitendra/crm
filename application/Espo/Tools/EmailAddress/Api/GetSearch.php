<?php


namespace Espo\Tools\EmailAddress\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Config;
use Espo\Entities\Email;
use Espo\Tools\Email\AddressService;


class GetSearch implements Action
{
    private const ADDRESS_MAX_SIZE = 50;

    public function __construct(
        private AddressService $service,
        private Acl $acl,
        private Config $config
    ) {}

    public function process(Request $request): Response
    {
        if (!$this->acl->checkScope(Email::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        if (!$this->acl->checkScope(Email::ENTITY_TYPE, Acl\Table::ACTION_CREATE)) {
            throw new Forbidden();
        }

        $q = $request->getQueryParam('q');

        if (is_string($q)) {
            $q = trim($q);
        }

        if (!$q) {
            throw new BadRequest("No `q` parameter.");
        }

        $maxSize = intval($request->getQueryParam('maxSize'));

        if (!$maxSize || $maxSize > self::ADDRESS_MAX_SIZE) {
            $maxSize = (int) $this->config->get('recordsPerPage');
        }

        $onlyActual = $request->getQueryParam('onlyActual') === 'true';

        $result = $this->service->searchInAddressBook($q, $maxSize, $onlyActual);

        return ResponseComposer::json($result);
    }
}
