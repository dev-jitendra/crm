<?php


namespace Espo\Core\Select\Text;

use Espo\Core\Utils\Config;

class ConfigProvider
{
    private const MIN_LENGTH_FOR_CONTENT_SEARCH = 4;

    public function __construct(private Config $config)
    {}

    public function getMinLengthForContentSearch(): int
    {
        return $this->config->get('textFilterContainsMinLength') ??
            self::MIN_LENGTH_FOR_CONTENT_SEARCH;
    }

    public function useContainsForVarchar(): bool
    {
        return $this->config->get('textFilterUseContainsForVarchar') ?? false;
    }

    public function usePhoneNumberNumericSearch(): bool
    {
        return $this->config->get('phoneNumberNumericSearch') ?? false;
    }
}
