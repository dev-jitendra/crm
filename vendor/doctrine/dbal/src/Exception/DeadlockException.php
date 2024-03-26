<?php

namespace Doctrine\DBAL\Exception;


class DeadlockException extends ServerException implements RetryableException
{
}
