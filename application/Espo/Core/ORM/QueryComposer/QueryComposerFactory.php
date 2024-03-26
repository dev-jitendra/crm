<?php


namespace Espo\Core\ORM\QueryComposer;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\ORM\QueryComposer\Part\FunctionConverterFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\PDO\PDOProvider;
use Espo\ORM\QueryComposer\QueryComposer;
use Espo\ORM\Metadata as OrmMetadata;
use Espo\ORM\EntityFactory;

use PDO;
use RuntimeException;

class QueryComposerFactory implements \Espo\ORM\QueryComposer\QueryComposerFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private PDOProvider $pdoProvider,
        private OrmMetadata $ormMetadata,
        private EntityFactory $entityFactory,
        private FunctionConverterFactory $functionConverterFactory
    ) {}

    public function create(string $platform): QueryComposer
    {
        
        $className =
            $this->metadata->get(['app', 'orm', 'platforms', $platform, 'queryComposerClassName']) ??
            $this->metadata->get(['app', 'orm', 'queryComposerClassNameMap', $platform]);

        if (!$className) {
            
            $className = "Espo\\ORM\\QueryComposer\\{$platform}QueryComposer";
        }

        if (!class_exists($className)) {
            throw new RuntimeException("Query composer for '{$platform}' platform does not exits.");
        }

        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(PDO::class, $this->pdoProvider->get())
            ->bindInstance(OrmMetadata::class, $this->ormMetadata)
            ->bindInstance(EntityFactory::class, $this->entityFactory)
            ->bindInstance(FunctionConverterFactory::class, $this->functionConverterFactory)
            ->build();

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }
}
