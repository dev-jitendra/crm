<?php

namespace Laminas\Validator\Barcode;

interface AdapterInterface
{
    
    public function hasValidLength($value);

    
    public function hasValidCharacters($value);

    
    public function hasValidChecksum($value);

    
    public function getLength();

    
    public function getCharacters();

    
    public function getChecksum();

    
    public function useChecksum($check = null);
}
