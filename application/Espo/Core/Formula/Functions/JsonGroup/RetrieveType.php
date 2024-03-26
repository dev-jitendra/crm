<?php


namespace Espo\Core\Formula\Functions\JsonGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Functions\BaseFunction;

class RetrieveType extends BaseFunction
{
    
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $jsonString = $this->evaluate($args[0]);

        $path = count($args) > 1 ?
            $this->evaluate($args[1]) :
            '';

        if (!is_string($jsonString)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_string($path)) {
            $this->throwBadArgumentType(2, 'string');
        }

        $item = json_decode($jsonString);

        $pathArray = $this->splitPath($path);

        return $this->retrieveAttribute($item, $pathArray);
    }

    
    private function splitPath(string $path): array
    {
        if ($path === '') {
            return [];
        }

        
        $pathArray = preg_split('/(?<!\\\)\./', $path);

        foreach ($pathArray as $i => $item) {
            $pathArray[$i] = str_replace('\.', '.', $item);
        }

        return $pathArray;
    }

    
    private function retrieveAttribute($item, array $path)
    {
        if (!count($path)) {
            return $item;
        }

        $key = array_shift($path);

        if (is_array($item)) {
            $key = intval($key);

            $subItem = $item[$key] ?? null;

            if (is_null($subItem)) {
                return null;
            }

            return $this->retrieveAttribute($subItem, $path);
        }

        if (is_object($item)) {
            $subItem = $item->$key ?? null;

            if (is_null($subItem)) {
                return null;
            }

            return $this->retrieveAttribute($subItem, $path);
        }

        return null;
    }
}
