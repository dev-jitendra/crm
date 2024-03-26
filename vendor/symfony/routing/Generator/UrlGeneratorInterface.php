<?php



namespace Symfony\Component\Routing\Generator;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContextAwareInterface;


interface UrlGeneratorInterface extends RequestContextAwareInterface
{
    
    public const ABSOLUTE_URL = 0;

    
    public const ABSOLUTE_PATH = 1;

    
    public const RELATIVE_PATH = 2;

    
    public const NETWORK_PATH = 3;

    
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string;
}
