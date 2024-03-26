<?php


namespace Espo\Core\Field\Address;

use Espo\Core\Utils\Metadata;

class AddressFormatterMetadataProvider
{
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getFormatterClassName(int $format): ?string
    {
        return $this->metadata->get([
           'app', 'addressFormats', strval($format), 'formatterClassName',
        ]);
    }
}
