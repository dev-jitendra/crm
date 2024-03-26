<?php


namespace Espo\Modules\Crm\Tools\MassEmail;

use Espo\Core\Mail\Mail\Header\XQueueItemId;
use Espo\Core\Utils\Config;
use Espo\Modules\Crm\Tools\MassEmail\MessagePreparator\Data;
use Laminas\Mail\Headers;

class DefaultMessageHeadersPreparator implements MessageHeadersPreparator
{
    public function __construct(private Config $config)
    {}

    public function prepare(Headers $headers, Data $data): void
    {
        $id = $data->getId();

        $header = new XQueueItemId();
        $header->setId($id);

        $headers->addHeader($header);
        $headers->addHeaderLine('Precedence', 'bulk');

        if (!$this->config->get('massEmailDisableMandatoryOptOutLink')) {
            $optOutUrl = $this->getSiteUrl() . '?entryPoint=unsubscribe&id=' . $id;

            $headers->addHeaderLine('List-Unsubscribe', '<' . $optOutUrl . '>');
        }
    }

    private function getSiteUrl(): string
    {
        return
            $this->config->get('massEmailSiteUrl') ??
            $this->config->get('siteUrl');
    }
}
