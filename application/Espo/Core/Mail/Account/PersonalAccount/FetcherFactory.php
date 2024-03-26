<?php


namespace Espo\Core\Mail\Account\PersonalAccount;

use Espo\Core\Binding\Factory;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Account\Hook\AfterFetch;
use Espo\Core\Mail\Account\PersonalAccount\Hooks\AfterFetch as PersonalAccountAfterFetch;
use Espo\Core\Mail\Account\Fetcher;
use Espo\Core\Mail\Account\StorageFactory;
use Espo\Core\Mail\Account\PersonalAccount\StorageFactory as PersonalAccountStorageFactory;


class FetcherFactory implements Factory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): Fetcher
    {
        $binding = BindingContainerBuilder::create()
            ->bindImplementation(StorageFactory::class, PersonalAccountStorageFactory::class)
            ->bindImplementation(AfterFetch::class, PersonalAccountAfterFetch::class)
            ->build();

        return $this->injectableFactory->createWithBinding(Fetcher::class, $binding);
    }
}
