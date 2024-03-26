<?php


namespace Espo\Core\Mail\Mail\Storage;

class Imap extends \Laminas\Mail\Storage\Imap
{
    
    public function getIdsFromUniqueId(string $uid): array
    {
        $nextUid = strval(intval($uid) + 1);

        assert($this->protocol !== null);

        return $this->protocol->search(['UID ' . $nextUid . ':*']);
    }

    
    public function getIdsSinceDate(string $date): array
    {
        assert($this->protocol !== null);

        return $this->protocol->search(['SINCE ' . $date]);
    }

    
    public function getHeaderAndFlags(int $id): array
    {
        assert($this->protocol !== null);

        
        $data = $this->protocol->fetch(['FLAGS', 'RFC822.HEADER'], $id);

        $header = $data['RFC822.HEADER'];

        $flags = [];

        foreach ($data['FLAGS'] as $flag) {
            $flags[] = static::$knownFlags[$flag] ?? $flag;
        }

        return [
            'flags' => $flags,
            'header' => $header,
        ];
    }
}
