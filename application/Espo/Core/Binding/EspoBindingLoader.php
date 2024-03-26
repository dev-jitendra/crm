<?php


namespace Espo\Core\Binding;

use Espo\Core\Utils\Module;
use Espo\Binding;

class EspoBindingLoader implements BindingLoader
{
    
    private array $moduleNameList;

    public function __construct(Module $module)
    {
        $this->moduleNameList = $module->getOrderedList();
    }

    public function load(): BindingData
    {
        $data = new BindingData();
        $binder = new Binder($data);

        (new Binding())->process($binder);

        foreach ($this->moduleNameList as $moduleName) {
            $this->loadModule($binder, $moduleName);
        }

        $this->loadCustom($binder);

        return $data;
    }

    private function loadModule(Binder $binder, string $moduleName): void
    {
        $className = 'Espo\\Modules\\' . $moduleName . '\\Binding';

        if (!class_exists($className)) {
            return;
        }

        

        (new $className())->process($binder);
    }

    private function loadCustom(Binder $binder): void
    {
        $className = 'Espo\\Custom\\Binding';

        if (!class_exists($className)) {
            return;
        }

        

        (new $className())->process($binder);
    }
}
