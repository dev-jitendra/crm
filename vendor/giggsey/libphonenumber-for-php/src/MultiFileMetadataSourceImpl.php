<?php


namespace libphonenumber;


class MultiFileMetadataSourceImpl implements MetadataSourceInterface
{
    
    protected $regionToMetadataMap = array();

    
    protected $countryCodeToNonGeographicalMetadataMap = array();

    
    protected $currentFilePrefix;


    
    protected $metadataLoader;

    
    public function __construct(MetadataLoaderInterface $metadataLoader, $currentFilePrefix = null)
    {
        if ($currentFilePrefix === null) {
            $currentFilePrefix = __DIR__ . '/data/PhoneNumberMetadata';
        }

        $this->currentFilePrefix = $currentFilePrefix;
        $this->metadataLoader = $metadataLoader;
    }

    
    public function getMetadataForRegion($regionCode)
    {
        $regionCode = strtoupper($regionCode);

        if (!array_key_exists($regionCode, $this->regionToMetadataMap)) {
            
            
            $this->loadMetadataFromFile($this->currentFilePrefix, $regionCode, 0, $this->metadataLoader);
        }

        return $this->regionToMetadataMap[$regionCode];
    }

    
    public function getMetadataForNonGeographicalRegion($countryCallingCode)
    {
        if (!array_key_exists($countryCallingCode, $this->countryCodeToNonGeographicalMetadataMap)) {
            $this->loadMetadataFromFile($this->currentFilePrefix, PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY, $countryCallingCode, $this->metadataLoader);
        }

        return $this->countryCodeToNonGeographicalMetadataMap[$countryCallingCode];
    }

    
    public function loadMetadataFromFile($filePrefix, $regionCode, $countryCallingCode, MetadataLoaderInterface $metadataLoader)
    {
        $regionCode = strtoupper($regionCode);

        $isNonGeoRegion = PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY === $regionCode;
        $fileName = $filePrefix . '_' . ($isNonGeoRegion ? $countryCallingCode : $regionCode) . '.php';
        if (!is_readable($fileName)) {
            throw new \RuntimeException('missing metadata: ' . $fileName);
        }

        $data = $metadataLoader->loadMetadata($fileName);
        $metadata = new PhoneMetadata();
        $metadata->fromArray($data);
        if ($isNonGeoRegion) {
            $this->countryCodeToNonGeographicalMetadataMap[$countryCallingCode] = $metadata;
        } else {
            $this->regionToMetadataMap[$regionCode] = $metadata;
        }
    }
}
