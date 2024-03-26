<?php


namespace Espo\Core\Authentication\TwoFactor\Sms;

use Espo\Core\Authentication\TwoFactor\Exceptions\NotConfigured;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Portal\Utils\Config;
use Espo\Entities\User;
use Espo\Core\Authentication\TwoFactor\UserSetup;

use stdClass;


class SmsUserSetup implements UserSetup
{
    public function __construct(
        private Util $util,
        private Config $config
    ) {}

    public function getData(User $user): stdClass
    {
        if (!$this->config->get('smsProvider')) {
            throw new NotConfigured("No SMS provider.");
        }

        return (object) [
            'phoneNumberList' => $user->getPhoneNumberGroup()->getNumberList(),
        ];
    }

    public function verifyData(User $user, stdClass $payloadData): bool
    {
        $code = $payloadData->code ?? null;

        if ($code === null) {
            throw new BadRequest("No code.");
        }

        $codeModified = str_replace(' ', '', trim($code));

        if (!$codeModified) {
            return false;
        }

        return $this->util->verifyCode($user, $codeModified);
    }
}
