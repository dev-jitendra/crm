<?php



namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;


abstract class FileDumper implements DumperInterface
{
    
    protected $relativePathTemplate = '%domain%.%locale%.%extension%';

    
    public function setRelativePathTemplate(string $relativePathTemplate)
    {
        $this->relativePathTemplate = $relativePathTemplate;
    }

    public function dump(MessageCatalogue $messages, array $options = [])
    {
        if (!\array_key_exists('path', $options)) {
            throw new InvalidArgumentException('The file dumper needs a path option.');
        }

        
        foreach ($messages->getDomains() as $domain) {
            $fullpath = $options['path'].'/'.$this->getRelativePath($domain, $messages->getLocale());
            if (!file_exists($fullpath)) {
                $directory = \dirname($fullpath);
                if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
                    throw new RuntimeException(sprintf('Unable to create directory "%s".', $directory));
                }
            }

            $intlDomain = $domain.MessageCatalogue::INTL_DOMAIN_SUFFIX;
            $intlMessages = $messages->all($intlDomain);

            if ($intlMessages) {
                $intlPath = $options['path'].'/'.$this->getRelativePath($intlDomain, $messages->getLocale());
                file_put_contents($intlPath, $this->formatCatalogue($messages, $intlDomain, $options));

                $messages->replace([], $intlDomain);

                try {
                    if ($messages->all($domain)) {
                        file_put_contents($fullpath, $this->formatCatalogue($messages, $domain, $options));
                    }
                    continue;
                } finally {
                    $messages->replace($intlMessages, $intlDomain);
                }
            }

            file_put_contents($fullpath, $this->formatCatalogue($messages, $domain, $options));
        }
    }

    
    abstract public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string;

    
    abstract protected function getExtension(): string;

    
    private function getRelativePath(string $domain, string $locale): string
    {
        return strtr($this->relativePathTemplate, [
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->getExtension(),
        ]);
    }
}
