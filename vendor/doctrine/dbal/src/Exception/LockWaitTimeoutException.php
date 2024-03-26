<?php

namespace Doctrine\DBAL\Exception;


class LockWaitTimeoutException extends ServerException implements RetryableException
{
}
