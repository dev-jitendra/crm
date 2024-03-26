<?php


namespace Espo\Tools\EntityManager\Hook\Hooks;

use Espo\Core\Templates\Entities\Event;
use Espo\Core\Utils\Language;
use Espo\Tools\EntityManager\Hook\CreateHook;
use Espo\Tools\EntityManager\Params;

class EventCreateHook implements CreateHook
{
    public function __construct(
        private Language $baseLanguage,
        private Language $language
    ) {}

    public function process(Params $params): void
    {
        if ($params->getType() !== Event::TEMPLATE_TYPE) {
            return;
        }

        $this->translate($this->baseLanguage, $params);

        if ($this->baseLanguage->getLanguage() === $this->language->getLanguage()) {
            return;
        }

        $this->translate($this->language, $params);
    }

    private function translate(Language $language, Params $params): void
    {
        $name = $params->getName();

        $label1 = 'Schedule ' . $name;
        $label2 = 'Log ' . $name;

        $translatedName = $params->get('labelSingular') ?? $name;

        $translation1 = $language->translateLabel('Schedule') . ' ' . $translatedName;
        $translation2 = $language->translateLabel('Log') . ' ' .  $translatedName;

        $language->set('Global', 'labels', $label1, $translation1);
        $language->set('Global', 'labels', $label2, $translation2);

        $language->save();
    }
}
