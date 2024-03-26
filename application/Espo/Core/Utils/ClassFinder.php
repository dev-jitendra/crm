<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\File\ClassMap;


class ClassFinder
{

    
    private $dataHashMap = [];

    public function __construct(private ClassMap $classMap)
    {}

    
    public function find(string $category, string $name, bool $subDirs = false): ?string
    {
        $map = $this->getMap($category, $subDirs);

        return $map[$name] ?? null;
    }

    
    public function getMap(string $category, bool $subDirs = false): array
    {
        if (!array_key_exists($category, $this->dataHashMap)) {
            $this->load($category, $subDirs);
        }

        return $this->dataHashMap[$category] ?? [];
    }

    private function load(string $category, bool $subDirs = false): void
    {
        $cacheFile = $this->buildCacheKey($category);

        $this->dataHashMap[$category] = $this->classMap->getData($category, $cacheFile, null, $subDirs);
    }

    private function buildCacheKey(string $category): string
    {
        return 'classmap' . str_replace('/', '', $category);
    }
}
