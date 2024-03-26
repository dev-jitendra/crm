<?php


namespace Espo\Tools\EmailTemplate;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

class PlaceholdersProvider
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function get(): array
    {
        $defs = $this->metadata->get("app.emailTemplate.placeholders") ?? [];

        
        $list = array_keys($defs);

        usort($list, function ($a, $b) use ($defs) {
            $o1 = $defs[$a]['order'] ?? 0;
            $o2 = $defs[$b]['order'] ?? 0;

            return $o1 - $o2;
        });

        return array_map(function ($name) use ($defs) {
            
            $className = $defs[$name]['className'];

            $placeholder = $this->injectableFactory->create($className);

            return [$name, $placeholder];
        }, $list);
    }
}
