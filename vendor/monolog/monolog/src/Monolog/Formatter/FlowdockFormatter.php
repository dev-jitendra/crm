<?php declare(strict_types=1);



namespace Monolog\Formatter;

use Monolog\LogRecord;


class FlowdockFormatter implements FormatterInterface
{
    private string $source;

    private string $sourceEmail;

    public function __construct(string $source, string $sourceEmail)
    {
        $this->source = $source;
        $this->sourceEmail = $sourceEmail;
    }

    
    public function format(LogRecord $record): array
    {
        $tags = [
            '#logs',
            '#' . $record->level->toPsrLogLevel(),
            '#' . $record->channel,
        ];

        foreach ($record->extra as $value) {
            $tags[] = '#' . $value;
        }

        $subject = sprintf(
            'in %s: %s - %s',
            $this->source,
            $record->level->getName(),
            $this->getShortMessage($record->message)
        );

        return [
            'source' => $this->source,
            'from_address' => $this->sourceEmail,
            'subject' => $subject,
            'content' => $record->message,
            'tags' => $tags,
            'project' => $this->source,
        ];
    }

    
    public function formatBatch(array $records): array
    {
        $formatted = [];

        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }

        return $formatted;
    }

    public function getShortMessage(string $message): string
    {
        static $hasMbString;

        if (null === $hasMbString) {
            $hasMbString = function_exists('mb_strlen');
        }

        $maxLength = 45;

        if ($hasMbString) {
            if (mb_strlen($message, 'UTF-8') > $maxLength) {
                $message = mb_substr($message, 0, $maxLength - 4, 'UTF-8') . ' ...';
            }
        } else {
            if (strlen($message) > $maxLength) {
                $message = substr($message, 0, $maxLength - 4) . ' ...';
            }
        }

        return $message;
    }
}
