<?php


namespace Espo\EntryPoints;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\Traits\NoAuth;
use Espo\Core\Exceptions\NotFound;

class LogoImage extends Image
{
    use NoAuth;

    protected $allowedRelatedTypeList = ['Settings', 'Portal'];
    protected $allowedFieldList = ['companyLogo'];

    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id');
        $size = $request->getQueryParam('size') ?? null;

        if (!$id) {
            $id = $this->config->get('companyLogoId');
        }

        if (!$id) {
            throw new NotFound();
        }

        $this->show($response, $id, $size);
    }
}
