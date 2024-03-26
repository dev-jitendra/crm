<?php


namespace Espo\Core\Mail\Smtp;

use Espo\Core\InjectableFactory;
use Espo\Core\Mail\SmtpParams;

class HandlerProcessor
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    
    public function handle(string $className, SmtpParams $params, ?string $id): SmtpParams
    {
        $handler = $this->injectableFactory->create($className);

        if ($handler instanceof Handler) {
            return $handler->handle($params, $id);
        }

        if (method_exists($handler, 'applyParams')) {
            $raw = $params->toArray();

            $handler->applyParams($id, $raw);

            return SmtpParams::fromArray($raw);
        }

        return $params;
    }
}
