<?php

namespace AsyncAws\Core\Signer;

use AsyncAws\Core\Credentials\Credentials;
use AsyncAws\Core\Request;
use AsyncAws\Core\RequestContext;


interface Signer
{
    public function sign(Request $request, Credentials $credentials, RequestContext $context): void;

    public function presign(Request $request, Credentials $credentials, RequestContext $context): void;
}
