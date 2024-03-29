<?php



namespace Symfony\Component\HttpClient\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpClient\TraceableHttpClient;

final class HttpClientPass implements CompilerPassInterface
{
    private $clientTag;

    public function __construct(string $clientTag = 'http_client.client')
    {
        $this->clientTag = $clientTag;
    }

    
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('data_collector.http_client')) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->clientTag) as $id => $tags) {
            $container->register('.debug.'.$id, TraceableHttpClient::class)
                ->setArguments([new Reference('.debug.'.$id.'.inner'), new Reference('debug.stopwatch', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)])
                ->setDecoratedService($id);
            $container->getDefinition('data_collector.http_client')
                ->addMethodCall('registerClient', [$id, new Reference('.debug.'.$id)]);
        }
    }
}
