<?php


namespace Espo\Modules\Crm\Tools\MassEmail\MessagePreparator;

use Espo\Core\Mail\SenderParams;

class Data
{
    private string $id;
    private SenderParams $senderParams;

    public function __construct(
        string $id,
        SenderParams $senderParams
    ) {
        $this->id = $id;
        $this->senderParams = $senderParams;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSenderParams(): SenderParams
    {
        return $this->senderParams;
    }
}
