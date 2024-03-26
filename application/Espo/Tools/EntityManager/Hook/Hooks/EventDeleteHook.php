<?php


namespace Espo\Tools\EntityManager\Hook\Hooks;

use Espo\Core\Templates\Entities\Event;
use Espo\Core\Utils\Language;
use Espo\Tools\EntityManager\Hook\DeleteHook;
use Espo\Tools\EntityManager\Params;

class EventDeleteHook implements DeleteHook
{
    public function __construct(
        private Language $baseLanguage,
        private Language $language
    ) {}

    public function process(Params $params): void
    {
        if (
            $params->getType() !== Event::TEMPLATE_TYPE
        ) {
            return;
        }

        $name = $params->getName();

        $label1 = 'Schedule ' . $name;
        $label2 = 'Log ' . $name;

        $this->baseLanguage->delete('Global', 'labels', $label1);
        $this->baseLanguage->delete('Global', 'labels', $label2);

        $this->language->delete('Global', 'labels', $label1);
        $this->language->delete('Global', 'labels', $label2);

        $this->baseLanguage->save();
        $this->language->save();
    }
}
