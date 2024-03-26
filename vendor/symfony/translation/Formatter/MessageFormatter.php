<?php



namespace Symfony\Component\Translation\Formatter;

use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;


class_exists(IntlFormatter::class);


class MessageFormatter implements MessageFormatterInterface, IntlFormatterInterface
{
    private TranslatorInterface $translator;
    private IntlFormatterInterface $intlFormatter;

    
    public function __construct(TranslatorInterface $translator = null, IntlFormatterInterface $intlFormatter = null)
    {
        $this->translator = $translator ?? new IdentityTranslator();
        $this->intlFormatter = $intlFormatter ?? new IntlFormatter();
    }

    public function format(string $message, string $locale, array $parameters = []): string
    {
        if ($this->translator instanceof TranslatorInterface) {
            return $this->translator->trans($message, $parameters, null, $locale);
        }

        return strtr($message, $parameters);
    }

    public function formatIntl(string $message, string $locale, array $parameters = []): string
    {
        return $this->intlFormatter->formatIntl($message, $locale, $parameters);
    }
}
