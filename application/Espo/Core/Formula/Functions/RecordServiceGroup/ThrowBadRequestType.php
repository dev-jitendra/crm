<?php


namespace Espo\Core\Formula\Functions\RecordServiceGroup;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error\Body;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Utils\Json;

class ThrowBadRequestType extends BaseFunction
{
    
    public function process(ArgumentList $args)
    {
        if (empty($this->getVariables()->__isRecordService)) {
            $this->throwError("Can be called only from API script.");
        }

        $message = isset($args[0]) ? $this->evaluate($args[0]) : '';
        $body = isset($args[1]) ? $this->evaluate($args[1]) : null;

        if ($body !== null) {
            throw BadRequest::createWithBody($message, Json::encode($body));
        }

        throw BadRequest::createWithBody($message, Body::create()->withMessage($message));
    }
}
