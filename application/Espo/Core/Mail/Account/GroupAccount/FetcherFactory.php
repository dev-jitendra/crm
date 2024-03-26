<?php


namespace Espo\Core\Mail\Account\GroupAccount;

use Espo\Core\Binding\Factory;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;

use Espo\Core\Mail\Account\Hook\BeforeFetch;
use Espo\Core\Mail\Account\Hook\AfterFetch;
use Espo\Core\Mail\Account\GroupAccount\Hooks\BeforeFetch as GroupAccountBeforeFetch;
use Espo\Core\Mail\Account\GroupAccount\Hooks\AfterFetch as GroupAccountAfterFetch;

use Espo\Core\Mail\Account\StorageFactory;
use Espo\Core\Mail\Account\GroupAccount\StorageFactory as GroupAccountStorageFactory;
use Espo\Core\Mail\Account\Fetcher;


class FetcherFactory implements Factory
{
    private InjectableFactory $injectableFactory;

    public function __construct(InjectableFactory $injectableFactory)
    {
        $this->injectableFactory = $injectableFactory;
    }

    public function create(): Fetcher
    {
        $binding = BindingContainerBuilder::create()
            ->bindImplementation(BeforeFetch::class, GroupAccountBeforeFetch::class)
            ->bindImplementation(AfterFetch::class, GroupAccountAfterFetch::class)
            ->bindImplementation(StorageFactory::class, GroupAccountStorageFactory::class)
            ->build();

        return $this->injectableFactory->createWithBinding(Fetcher::class, $binding);
    }
}
