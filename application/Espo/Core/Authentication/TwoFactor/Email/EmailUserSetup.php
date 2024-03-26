<?php


namespace Espo\Core\Authentication\TwoFactor\Email;

use Espo\Core\Exceptions\BadRequest;
use Espo\Entities\User;
use Espo\Core\Authentication\TwoFactor\UserSetup;

use stdClass;


class EmailUserSetup implements UserSetup
{

    public function __construct(private Util $util)
    {}

    public function getData(User $user): stdClass
    {
        return (object) [
            'emailAddressList' => $user->getEmailAddressGroup()->getAddressList(),
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
