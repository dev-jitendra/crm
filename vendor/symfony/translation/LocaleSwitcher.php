<?php



namespace Symfony\Component\Translation;

use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\Translation\LocaleAwareInterface;


class LocaleSwitcher implements LocaleAwareInterface
{
    private string $defaultLocale;

    
    public function __construct(
        private string $locale,
        private iterable $localeAwareServices,
        private ?RequestContext $requestContext = null,
    ) {
        $this->defaultLocale = $locale;
    }

    public function setLocale(string $locale): void
    {
        if (class_exists(\Locale::class)) {
            \Locale::setDefault($locale);
        }
        $this->locale = $locale;
        $this->requestContext?->setParameter('_locale', $locale);

        foreach ($this->localeAwareServices as $service) {
            $service->setLocale($locale);
        }
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    
    public function runWithLocale(string $locale, callable $callback): mixed
    {
        $original = $this->getLocale();
        $this->setLocale($locale);

        try {
            return $callback();
        } finally {
            $this->setLocale($original);
        }
    }

    public function reset(): void
    {
        $this->setLocale($this->defaultLocale);
    }
}
