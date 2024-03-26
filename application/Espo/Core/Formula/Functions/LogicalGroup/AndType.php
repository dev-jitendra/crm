<?php


namespace Espo\Core\Formula\Functions\LogicalGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class AndType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $result = true;

        foreach ($args as $item) {
            
            $result = $result && $this->evaluate($item);

            if (!$result) {
                break;
            }
        }

        return $result;
    }
}
