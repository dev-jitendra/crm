<?php


namespace Espo\Core\Utils\Metadata;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Database\Orm\Converter;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\Util;

class OrmMetadataData
{
    
    private $data = null;
    private string $cacheKey = 'ormMetadata';
    private bool $useCache;
    private ?Converter $converter = null;

    public function __construct(
        private DataCache $dataCache,
        private Config $config,
        private InjectableFactory $injectableFactory
    ) {

        $this->useCache = (bool) $this->config->get('useCache', false);
    }

    private function getConverter(): Converter
    {
        if (!isset($this->converter)) {
            $this->converter = $this->injectableFactory->create(Converter::class);
        }

        return $this->converter;
    }

    
    public function reload(): void
    {
        $this->getDataInternal(true);
    }

    
    public function getData(): array
    {
        return $this->getDataInternal();
    }

    
    private function getDataInternal(bool $reload = false): array
    {
        if (isset($this->data) && !$reload) {
            return $this->data;
        }

        if ($this->useCache && $this->dataCache->has($this->cacheKey) && !$reload) {
            
            $data = $this->dataCache->get($this->cacheKey);

            $this->data = $data;

            return $this->data;
        }

        $this->data = $this->getConverter()->process();

        if ($this->useCache) {
            $this->dataCache->store($this->cacheKey, $this->data);
        }

        return $this->data;
    }

    
    public function get($key = null, $default = null)
    {
        return Util::getValueByKey($this->getData(), $key, $default);
    }
}
