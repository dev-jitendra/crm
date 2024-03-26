<?php


namespace Espo\Core\Authentication\Result;

class FailReason
{
    public const DENIED = 'Denied';
    public const CODE_NOT_VERIFIED = 'Code not verified';
    public const NO_USERNAME = 'No username';
    public const NO_PASSWORD = 'No password';
    public const TOKEN_NOT_FOUND = 'Token not found';
    public const USER_NOT_FOUND = 'User not found';
    public const WRONG_CREDENTIALS = 'Wrong credentials';
    public const USER_TOKEN_MISMATCH = 'User and token mismatch';
    public const HASH_NOT_MATCHED = 'Hash not matched';
    public const METHOD_NOT_ALLOWED = 'Not allowed authentication method';
    public const DISCREPANT_DATA = 'Discrepant authentication data';
    public const ANOTHER_USER_NOT_FOUND = 'Another user not found';
    public const ANOTHER_USER_NOT_ALLOWED = 'Another user not allowed';
    public const ERROR = 'Error';
}
