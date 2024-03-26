<?php


namespace Espo\Classes\AppInfo;

use Espo\Core\Binding\Binding as BindingItem;
use Espo\Core\Binding\EspoBindingLoader;
use Espo\Core\Console\Command\Params;
use Espo\Core\Utils\Module;

class Binding
{
    private Module $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function process(Params $params): string
    {
        $result = '';

        $bindingLoader = new EspoBindingLoader($this->module);

        $data = $bindingLoader->load();

        $keyList = $data->getGlobalKeyList();

        $result .= "Global:\n\n";

        foreach ($keyList as $key) {
            $result .= $this->printItem($key, $data->getGlobal($key));
        }

        $contextList = $data->getContextList();

        foreach ($contextList as $context) {
            $result .= "Context: {$context}\n\n";

            $keyList = $data->getContextKeyList($context);

            foreach ($keyList as $key) {
                $result .= $this->printItem($key, $data->getContext($context, $key));
            }
        }

        return $result;
    }

    private function printItem(string $key, BindingItem $binding): string
    {
        $result = '';

        $tab = '  ';

        $result .= $tab . "Key:   {$key}\n";

        $type = $binding->getType();
        $value = $binding->getValue();

        $typeString = [
            BindingItem::IMPLEMENTATION_CLASS_NAME => 'Implementation',
            BindingItem::CONTAINER_SERVICE => 'Service',
            BindingItem::VALUE => 'Value',
            BindingItem::CALLBACK => 'Callback',
            BindingItem::FACTORY_CLASS_NAME => 'Factory',
        ][$type];

        $result .= $tab . "Type:  {$typeString}\n";

        if ($type == BindingItem::IMPLEMENTATION_CLASS_NAME || $type == BindingItem::CONTAINER_SERVICE) {
            $result .= $tab . "Value: {$value}\n";
        }

        if ($type == BindingItem::VALUE) {
            if (is_string($value) || is_int($value) || is_float($value)) {
                $result .= $tab . "Value: {$value}\n";
            }

            if (is_bool($value)) {
                $valueString = $value ? 'true' : 'false';

                $result .= $tab . "Value: {$valueString}\n";
            }
        }

        $result .= "\n";

        return $result;
    }
}
