<?php


namespace Espo\Core\Formula\Functions\RecordServiceGroup;

use Espo\Core\Exceptions\Error\Body;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Utils\Json;

class ThrowForbiddenType extends BaseFunction
{
    
    public function process(ArgumentList $args)
    {
        if (empty($this->getVariables()->__isRecordService)) {
            $this->throwError("Can be called only from API script.");
        }

        $message = isset($args[0]) ? $this->evaluate($args[0]) : '';
        $body = isset($args[1]) ? $this->evaluate($args[1]) : null;

        if ($body !== null) {
            throw ForbiddenSilent::createWithBody($message, Json::encode($body));
        }

        throw ForbiddenSilent::createWithBody($message, Body::create()->withMessage($message));
    }
}
