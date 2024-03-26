<?php


namespace Espo\Core\Binding;

interface BindingLoader
{
    public function load(): BindingData;
}
