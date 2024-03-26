<?php

namespace libphonenumber;

interface MetadataSourceInterface
{
    
    public function getMetadataForRegion($regionCode);

    
    public function getMetadataForNonGeographicalRegion($countryCallingCode);
}
