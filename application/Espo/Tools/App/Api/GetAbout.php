<?php


namespace Espo\Tools\App\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Resource\FileReader;


class GetAbout implements Action
{
    public function __construct(
        private FileReader $fileReader,
        private Config $config
    ) {}

    public function process(Request $request): Response
    {
        $text = $this->fileReader->read('texts/about.md', FileReader\Params::create());

        return ResponseComposer::json([
            'text' => $text,
            'version' => $this->config->get('version'),
        ]);
    }
}
