<?php


namespace Espo\Core\Binding;

interface BindingProcessor
{
    public function process(Binder $binder): void;
}
