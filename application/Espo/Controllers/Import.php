<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;

use Espo\Core\Exceptions\NotFound;
use Espo\Tools\Import\Params as ImportParams;
use Espo\Tools\Import\Service as Service;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\Record;

use Espo\Core\Di\InjectableFactoryAware;
use Espo\Core\Di\InjectableFactorySetter;

use stdClass;

class Import extends Record

    implements InjectableFactoryAware
{
    use InjectableFactorySetter;

    protected function checkAccess(): bool
    {
        return $this->acl->check('Import');
    }

    public function putActionUpdate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }

    public function postActionCreateLink(Request $request): bool
    {
        throw new Forbidden();
    }

    public function deleteActionRemoveLink(Request $request): bool
    {
        throw new Forbidden();
    }
}
