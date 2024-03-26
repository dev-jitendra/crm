<?php


namespace Espo\Core\Select\Applier;

use Espo\Core\Select\Text\Applier as TextFilterApplier;
use Espo\Core\Select\AccessControl\Applier as AccessControlFilterApplier;
use Espo\Core\Select\Where\Applier as WhereApplier;
use Espo\Core\Select\Select\Applier as SelectApplier;
use Espo\Core\Select\Primary\Applier as PrimaryFilterApplier;
use Espo\Core\Select\Order\Applier as OrderApplier;
use Espo\Core\Select\Bool\Applier as BoolFilterListApplier;
use Espo\Core\Select\Applier\Appliers\Additional as AdditionalApplier;
use Espo\Core\Select\Applier\Appliers\Limit as LimitApplier;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Select\SelectManager;
use Espo\Core\Select\SelectManagerFactory;

use Espo\Entities\User;
use RuntimeException;

class Factory
{
    public const SELECT = 'select';
    public const WHERE = 'where';
    public const ORDER = 'order';
    public const LIMIT = 'limit';
    public const ACCESS_CONTROL_FILTER = 'accessControlFilter';
    public const TEXT_FILTER = 'textFilter';
    public const PRIMARY_FILTER = 'primaryFilter';
    public const BOOL_FILTER_LIST = 'boolFilterList';
    public const ADDITIONAL = 'additional';

    
    private array $defaultClassNameMap = [
        self::TEXT_FILTER => TextFilterApplier::class,
        self::ACCESS_CONTROL_FILTER => AccessControlFilterApplier::class,
        self::WHERE => WhereApplier::class,
        self::SELECT => SelectApplier::class,
        self::PRIMARY_FILTER => PrimaryFilterApplier::class,
        self::ORDER => OrderApplier::class,
        self::BOOL_FILTER_LIST => BoolFilterListApplier::class,
        self::ADDITIONAL => AdditionalApplier::class,
        self::LIMIT => LimitApplier::class,
    ];

    public function __construct(
        private InjectableFactory $injectableFactory,
        private SelectManagerFactory $selectManagerFactory
    ) {}

    private function create(string $entityType, User $user, string $type): object
    {
        $className = $this->getDefaultClassName($type);

        
        $selectManager = $this->selectManagerFactory->create($entityType, $user);

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(SelectManager::class, $selectManager)
            ->for($className)
            ->bindValue('$entityType', $entityType)
            ->bindValue('$selectManager', $selectManager);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    public function createWhere(string $entityType, User $user): WhereApplier
    {
        
        return $this->create($entityType, $user, self::WHERE);
    }

    public function createSelect(string $entityType, User $user): SelectApplier
    {
        
        return $this->create($entityType, $user, self::SELECT);
    }

    public function createOrder(string $entityType, User $user): OrderApplier
    {
        
        return $this->create($entityType, $user, self::ORDER);
    }

    public function createLimit(string $entityType, User $user): LimitApplier
    {
        
        return $this->create($entityType, $user, self::LIMIT);
    }

    public function createAccessControlFilter(string $entityType, User $user): AccessControlFilterApplier
    {
        
        return $this->create($entityType, $user, self::ACCESS_CONTROL_FILTER);
    }

    public function createTextFilter(string $entityType, User $user): TextFilterApplier
    {
        
        return $this->create($entityType, $user, self::TEXT_FILTER);
    }

    public function createPrimaryFilter(string $entityType, User $user): PrimaryFilterApplier
    {
        
        return $this->create($entityType, $user, self::PRIMARY_FILTER);
    }

    public function createBoolFilterList(string $entityType, User $user): BoolFilterListApplier
    {
        
        return $this->create($entityType, $user, self::BOOL_FILTER_LIST);
    }

    public function createAdditional(string $entityType, User $user): AdditionalApplier
    {
        
        return $this->create($entityType, $user, self::ADDITIONAL);
    }

    
    private function getDefaultClassName(string $type): string
    {
        if (array_key_exists($type, $this->defaultClassNameMap)) {
            return $this->defaultClassNameMap[$type];
        }

        throw new RuntimeException("Applier `$type` does not exist.");
    }
}
