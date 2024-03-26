<?php


namespace Espo\Tools\EmailTemplate;

interface Placeholder
{
   public function get(Data $data): string;
}
