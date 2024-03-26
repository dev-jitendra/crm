<?php


namespace Espo\Modules\Crm\Business\Event;

use RuntimeException;

class Ics
{
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_TENTATIVE = 'TENTATIVE';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const METHOD_REQUEST = 'REQUEST';
    public const METHOD_CANCEL = 'CANCEL';

    
    private string $method;
    private ?string $output = null;
    private string $prodid;
    private ?int $startDate = null;
    private ?int $endDate = null;
    private ?string $summary = null;
    private ?string $address = null;
    private ?string $email = null;
    private ?string $who = null;
    private ?string $description = null;
    private ?string $uid = null;
    
    private string $status;

    
    public function __construct(string $prodid, array $attributes = [])
    {
        if ($prodid === '') {
            throw new RuntimeException('PRODID is required');
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->method = self::METHOD_REQUEST;
        $this->prodid = $prodid;

        foreach ($attributes as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new RuntimeException("Bad attribute '{$key}'.");
            }

            $this->$key = $value;
        }
    }

    public function get(): string
    {
        if ($this->output === null) {
            $this->generate();
        }

        
        return $this->output;
    }

    private function generate(): void
    {
        $this->output =
            "BEGIN:VCALENDAR\r\n".
            "VERSION:2.0\r\n".
            "PRODID:-" . $this->prodid . "\r\n".
            "METHOD:" . $this->method . "\r\n".
            "BEGIN:VEVENT\r\n".
            "DTSTART:" . $this->formatTimestamp($this->startDate) . "\r\n".
            "DTEND:" . $this->formatTimestamp($this->endDate) . "\r\n".
            "SUMMARY:" . $this->escapeString($this->summary) . "\r\n".
            "LOCATION:" . $this->escapeString($this->address) . "\r\n".
            "ORGANIZER;CN=" . $this->escapeString($this->who) . ":MAILTO:" . $this->escapeString($this->email) . "\r\n".
            "DESCRIPTION:" . $this->escapeString($this->formatMultiline($this->description)) . "\r\n".
            "UID:" . $this->uid . "\r\n".
            "SEQUENCE:0\r\n".
            "DTSTAMP:" . $this->formatTimestamp(time()) . "\r\n".
            "STATUS:" . $this->status . "\r\n" .
            "END:VEVENT\r\n".
            "END:VCALENDAR";
    }

    private function formatTimestamp(?int $timestamp): string
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        return date('Ymd\THis\Z', $timestamp);
    }

    private function escapeString(?string $string): string
    {
        if (!$string) {
            return '';
        }

        
        return preg_replace('/([\,;])/', '\\\$1', $string);
    }

    private function formatMultiline(?string $string): string
    {
        if (!$string) {
            return '';
        }

        return str_replace(["\r\n", "\n"], "\\r\\n", $string);
    }
}
