<?php


namespace Espo\Core\Exceptions\Error;

use Espo\Core\Utils\Json;


class Body
{
    private ?string $messageTranslationLabel = null;
    private ?string $messageTranslationScope = null;
    
    private ?array $messageTranslationData = null;
    private ?string $message = null;

    public static function create(): self
    {
        return new self();
    }

    
    public function withMessageTranslation(string $label, ?string $scope = null, ?array $data = null): self
    {
        $obj = clone $this;

        $obj->messageTranslationLabel = $label;
        $obj->messageTranslationScope = $scope;
        $obj->messageTranslationData = $data;

        return $obj;
    }

    public function withMessage(string $message): self
    {
        $obj = clone $this;
        $obj->message = $message;

        return $obj;
    }

    public function encode(): string
    {
        $data = (object) [];

        if ($this->messageTranslationLabel) {
            $messageTranslationData = (object) ($this->messageTranslationData ?? []);

            $data->messageTranslation = (object) [
                'label' => $this->messageTranslationLabel,
                'scope' => $this->messageTranslationScope,
                'data' => $messageTranslationData,
            ];
        }

        if ($this->message) {
            $data->message = $this->message;
        }

        return Json::encode($data);
    }
}
