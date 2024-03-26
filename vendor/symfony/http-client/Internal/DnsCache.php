<?php



namespace Symfony\Component\HttpClient\Internal;


final class DnsCache
{
    
    public $hostnames = [];

    
    public $removals = [];

    
    public $evictions = [];
}
