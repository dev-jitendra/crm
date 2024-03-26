<?php


namespace Espo\Modules\Crm;

use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingProcessor;

class Binding implements BindingProcessor
{
    public function process(Binder $binder): void
    {
        $binder->bindImplementation(
            'Espo\\Modules\\Crm\\Tools\\MassEmail\\MessageHeadersPreparator',
            'Espo\\Modules\\Crm\\Tools\\MassEmail\\DefaultMessageHeadersPreparator'
        );
    }
}
