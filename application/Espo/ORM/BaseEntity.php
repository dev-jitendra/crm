<?php


namespace Espo\ORM;

use Espo\ORM\Value\ValueAccessorFactory;
use Espo\ORM\Value\ValueAccessor;

use stdClass;
use InvalidArgumentException;
use RuntimeException;

use const E_USER_DEPRECATED;
use const JSON_THROW_ON_ERROR;

class BaseEntity implements Entity
{
    
    protected $entityType;

    private bool $isNotNew = false;
    private bool $isSaved = false;
    private bool $isFetched = false;
    private bool $isBeingSaved = false;

    protected ?EntityManager $entityManager;
    private ?ValueAccessor $valueAccessor = null;

    
    private array $writtenMap = [];
    
    private array $attributes = [];
    
    private array $relations = [];
    
    private array $fetchedValuesContainer = [];
    
    private array $valuesContainer = [];

    
    public $id = null;

    
    public function __construct(
        string $entityType,
        array $defs,
        ?EntityManager $entityManager = null,
        ?ValueAccessorFactory $valueAccessorFactory = null
    ) {
        $this->entityType = $entityType;
        $this->entityManager = $entityManager;

        $this->attributes = $defs['attributes']  ?? $this->attributes;
        $this->relations = $defs['relations'] ?? $this->relations;

        if ($valueAccessorFactory) {
            $this->valueAccessor = $valueAccessorFactory->create($this);
        }
    }

    
    public function getId(): string
    {
        
        $id = $this->get('id');

        if ($id === null) {
            throw new RuntimeException("Entity ID is not set.");
        }

        if ($id === '') {
            throw new RuntimeException("Entity ID is empty.");
        }

        return $id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    
    public function clear(string $attribute): void
    {
        unset($this->valuesContainer[$attribute]);
    }

    
    public function reset(): void
    {
        $this->valuesContainer = [];
    }

    
    public function set($attribute, $value = null): void
    {
        $p1 = $attribute;
        $p2 = $value;

        

        if (is_array($p1) || is_object($p1)) {
            if (is_object($p1)) {
                $p1 = get_object_vars($p1);
            }

            if ($p2 === null) {
                $p2 = false;
            }

            if ($p2) {
                
                trigger_error(
                    'Second parameter is deprecated in Entity::set(array, onlyAccessible).',
                    E_USER_DEPRECATED
                );
            }

            $this->populateFromArray($p1, $p2);

            return;
        }

        if (is_string($p1)) {
            $name = $p1;
            $value = $p2;

            if ($name == 'id') {
                $this->id = $value;
            }

            if (!$this->hasAttribute($name)) {
                return;
            }

            $method = '_set' . ucfirst($name);

            if (method_exists($this, $method)) {
                $this->$method($value);

                return;
            }

            $this->populateFromArray([
                $name => $value,
            ]);

            return;
        }

        throw new InvalidArgumentException();
    }

    
    public function setMultiple(array|stdClass $valueMap): void
    {
        $this->set($valueMap);
    }

    
    public function get(string $attribute, $params = [])
    {
        if ($attribute === 'id') {
            return $this->id;
        }

        $method = '_get' . ucfirst($attribute);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if ($this->hasAttribute($attribute) && $this->hasInContainer($attribute)) {
            return $this->getFromContainer($attribute);
        }

        
        if (!empty($params)) {
            trigger_error(
                'Second parameter will be removed from the method Entity::get.',
                E_USER_DEPRECATED
            );
        }

        
        if ($this->hasRelation($attribute) && $this->id && $this->entityManager) {
            trigger_error(
                "Accessing related records with Entity::get is deprecated. " .
                "Use \$repository->getRelation(...)->find()",
                E_USER_DEPRECATED
            );

            
            return $this->entityManager
                ->getRepository($this->getEntityType())
                ->findRelated($this, $attribute, $params);
        }

        return null;
    }

    
    protected function setInContainer(string $attribute, $value): void
    {
        $this->valuesContainer[$attribute] = $value;
    }

    
    protected function hasInContainer(string $attribute): bool
    {
        return array_key_exists($attribute, $this->valuesContainer);
    }

    
    protected function getFromContainer(string $attribute)
    {
        if (!$this->hasInContainer($attribute)) {
            return null;
        }

        $value = $this->valuesContainer[$attribute] ?? null;

        if ($value === null) {
            return null;
        }

        $type = $this->getAttributeType($attribute);

        if ($type === self::JSON_ARRAY) {
            return $this->cloneArray($value);
        }

        if ($type === self::JSON_OBJECT) {
            return $this->cloneObject($value);
        }

        return $value;
    }

    
    protected function hasInFetchedContainer(string $attribute): bool
    {
        return array_key_exists($attribute, $this->fetchedValuesContainer);
    }

    
    protected function getFromFetchedContainer(string $attribute)
    {
        if (!$this->hasInFetchedContainer($attribute)) {
            return null;
        }

        $value = $this->fetchedValuesContainer[$attribute] ?? null;

        if ($value === null) {
            return $value;
        }

        $type = $this->getAttributeType($attribute);

        if ($type === self::JSON_ARRAY) {
            return $this->cloneArray($value);
        }

        if ($type === self::JSON_OBJECT) {
            return $this->cloneObject($value);
        }

        return $value;
    }

    
    public function has(string $attribute): bool
    {
        if ($attribute == 'id') {
            return (bool) $this->id;
        }

        $method = '_has' . ucfirst($attribute);

        if (method_exists($this, $method)) {
            return (bool) $this->$method();
        }

        if (array_key_exists($attribute, $this->valuesContainer)) {
            return true;
        }

        return false;
    }

    
    public function isValueObjectGettable(string $field): bool
    {
        if (!$this->valueAccessor) {
            throw new RuntimeException("No ValueAccessor.");
        }

        return $this->valueAccessor->isGettable($field);
    }

    
    public function getValueObject(string $field): ?object
    {
        if (!$this->valueAccessor) {
            throw new RuntimeException("No ValueAccessor.");
        }

        return $this->valueAccessor->get($field);
    }

    
    public function setValueObject(string $field, ?object $value): void
    {
        if (!$this->valueAccessor) {
            throw new RuntimeException("No ValueAccessor.");
        }

        $this->valueAccessor->set($field, $value);
    }

    protected function populateFromArrayItem(string $attribute, mixed $value): void
    {
        $preparedValue = $this->prepareAttributeValue($attribute, $value);

        $method = '_set' . ucfirst($attribute);

        if (method_exists($this, $method)) {
            $this->$method($preparedValue);

            return;
        }

        $this->setInContainer($attribute, $preparedValue);

        $this->writtenMap[$attribute] = true;
    }

    protected function prepareAttributeValue(string $attribute, mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $attributeType = $this->getAttributeType($attribute);

        if ($attributeType === self::FOREIGN) {
            $attributeType = $this->getForeignAttributeType($attribute) ?? $attributeType;
        }

        switch ($attributeType) {
            case self::VARCHAR:
                return $value;

            case self::BOOL:
                return ($value === 1 || $value === '1' || $value === true || $value === 'true');

            case self::INT:
                return intval($value);

            case self::FLOAT:
                return floatval($value);

            case self::JSON_ARRAY:
                return $this->prepareArrayAttributeValue($value);

            case self::JSON_OBJECT:
                return $this->prepareObjectAttributeValue($value);

            default:
                break;
        }

        return $value;
    }

    
    private function prepareArrayAttributeValue($value): ?array
    {
        if (is_string($value)) {
            $preparedValue = json_decode($value);

            if (!is_array($preparedValue)) {
                return null;
            }

            return $preparedValue;
        }

        if (!is_array($value)) {
            return null;
        }

        return $this->cloneArray($value);
    }

    
    private function prepareObjectAttributeValue($value): ?stdClass
    {
        if (is_string($value)) {
            $preparedValue = json_decode($value);

            if (!$preparedValue instanceof stdClass) {
                return null;
            }

            return $preparedValue;
        }

        $preparedValue = $value;

        if (is_array($value)) {
            $preparedValue = json_decode(json_encode($value, JSON_THROW_ON_ERROR));

            if ($preparedValue instanceof stdClass) {
                return $preparedValue;
            }
        }

        if (!$preparedValue instanceof stdClass) {
            return null;
        }

        return $this->cloneObject($preparedValue);
    }

    private function getForeignAttributeType(string $attribute): ?string
    {
        if (!$this->entityManager) {
            return null;
        }

        $defs = $this->entityManager->getDefs();

        $entityDefs = $defs->getEntity($this->entityType);

        
        if (!$entityDefs->hasAttribute($attribute)) {
            return null;
        }

        $relation = $entityDefs->getAttribute($attribute)->getParam('relation');
        $foreign = $entityDefs->getAttribute($attribute)->getParam('foreign');

        if (!$relation) {
            return null;
        }

        if (!$foreign) {
            return null;
        }

        if (!is_string($foreign)) {
            return self::VARCHAR;
        }

        if (!$entityDefs->getRelation($relation)->hasForeignEntityType()) {
            return null;
        }

        $entityType = $entityDefs->getRelation($relation)->getForeignEntityType();

        if (!$defs->hasEntity($entityType)) {
            return null;
        }

        $foreignEntityDefs = $defs->getEntity($entityType);

        if (!$foreignEntityDefs->hasAttribute($foreign)) {
            return null;
        }

        return $foreignEntityDefs->getAttribute($foreign)->getType();
    }

    
    public function isNew(): bool
    {
        return !$this->isNotNew;
    }

    
    public function setAsNotNew(): void
    {
        $this->isNotNew = true;
    }

    
    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    
    public function setAsSaved(): void
    {
        $this->isSaved = true;
    }

    
    public final function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public function hasField($name)
    {
        return $this->hasAttribute($name);
    }

    
    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    
    public function hasRelation(string $relation): bool
    {
        return isset($this->relations[$relation]);
    }

    
    public function getAttributeList(): array
    {
        return array_keys($this->attributes);
    }

    
    public function getRelationList(): array
    {
        return array_keys($this->relations);
    }

    
    public function toArray()
    {
        $arr = [];

        if (isset($this->id)) {
            $arr['id'] = $this->id;
        }

        foreach ($this->getAttributeList() as $attribute) {
            if ($attribute === 'id') {
                continue;
            }

            if ($this->has($attribute)) {
                $arr[$attribute] = $this->get($attribute);
            }
        }

        return $arr;
    }

    
    public function getValueMap(): stdClass
    {
        $array = $this->toArray();

        return (object) $array;
    }

    
    public function getAttributeType(string $attribute): ?string
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }

        return $this->attributes[$attribute]['type'] ?? null;
    }

    
    public function getRelationType(string $relation): ?string
    {
        if (!isset($this->relations[$relation])) {
            return null;
        }

        return $this->relations[$relation]['type'] ?? null;
    }

    
    public function getAttributeParam(string $attribute, string $name)
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }

        return $this->attributes[$attribute][$name] ?? null;
    }

    
    public function getRelationParam(string $relation, string $name)
    {
        if (!isset($this->relations[$relation])) {
            return null;
        }

        return $this->relations[$relation][$name] ?? null;
    }

    
    public function isFetched(): bool
    {
        return $this->isFetched;
    }

    
    public function isFieldChanged($name)
    {
        return $this->has($name) && ($this->get($name) != $this->getFetched($name));
    }

    
    public function isAttributeChanged(string $name): bool
    {
        if (!$this->has($name)) {
            return false;
        }

        if (!$this->hasFetched($name)) {
            return true;
        }

        
        $type = $this->getAttributeType($name);

        return !self::areValuesEqual(
            $type,
            $this->get($name),
            $this->getFetched($name),
            $this->getAttributeParam($name, 'isUnordered') ?? false
        );
    }

    
    public function isAttributeWritten(string $name): bool
    {
        return $this->writtenMap[$name] ?? false;
    }

    
    protected static function areValuesEqual(string $type, $v1, $v2, bool $isUnordered = false): bool
    {
        if ($type === self::JSON_ARRAY) {
            if (is_array($v1) && is_array($v2)) {
                if ($isUnordered) {
                    sort($v1);
                    sort($v2);
                }

                if ($v1 != $v2) {
                    return false;
                }

                foreach ($v1 as $i => $itemValue) {
                    if (is_object($itemValue) && is_object($v2[$i])) {
                        if (!self::areValuesEqual(self::JSON_OBJECT, $itemValue, $v2[$i])) {
                            return false;
                        }

                        continue;
                    }

                    if ($itemValue !== $v2[$i]) {
                        return false;
                    }
                }

                return true;
            }
        }
        else if ($type === self::JSON_OBJECT) {
            if (is_object($v1) && is_object($v2)) {
                if ($v1 != $v2) {
                    return false;
                }

                $a1 = get_object_vars($v1);
                $a2 = get_object_vars($v2);

                foreach (get_object_vars($v1) as $key => $itemValue) {
                    if (is_object($a1[$key]) && is_object($a2[$key])) {
                        if (!self::areValuesEqual(self::JSON_OBJECT, $a1[$key], $a2[$key])) {
                            return false;
                        }

                        continue;
                    }

                    if (is_array($a1[$key]) && is_array($a2[$key])) {
                        if (!self::areValuesEqual(self::JSON_ARRAY, $a1[$key], $a2[$key])) {
                            return false;
                        }

                        continue;
                    }

                    if ($a1[$key] !== $a2[$key]) {
                        return false;
                    }
                }

                return true;
            }
        }

        return $v1 === $v2;
    }

    
    public function setFetched(string $attribute, $value): void
    {
        $preparedValue = $this->prepareAttributeValue($attribute, $value);

        $this->fetchedValuesContainer[$attribute] = $preparedValue;
    }

    
    public function getFetched(string $attribute)
    {
        if ($attribute === 'id') {
            return $this->id;
        }

        if ($this->hasInFetchedContainer($attribute)) {
            return $this->getFromFetchedContainer($attribute);
        }

        return null;
    }

    
    public function hasFetched(string $attribute): bool
    {
        if ($attribute === 'id') {
            return !is_null($this->id);
        }

        return $this->hasInFetchedContainer($attribute);
    }

    
    public function resetFetchedValues(): void
    {
        $this->fetchedValuesContainer = [];
    }

    
    public function updateFetchedValues(): void
    {
        $this->fetchedValuesContainer = $this->valuesContainer;

        foreach ($this->fetchedValuesContainer as $attribute => $value) {
            $this->setFetched($attribute, $value);
        }

        $this->writtenMap = [];
    }

    
    public function setAsFetched(): void
    {
        $this->isFetched = true;

        $this->setAsNotNew();

        $this->updateFetchedValues();
    }

    
    public function isBeingSaved(): bool
    {
        return $this->isBeingSaved;
    }

    public function setAsBeingSaved(): void
    {
        $this->isBeingSaved = true;
    }

    public function setAsNotBeingSaved(): void
    {
        $this->isBeingSaved = false;
    }

    
    public function populateDefaults(): void
    {
        foreach ($this->attributes as $attribute => $defs) {
            if (!array_key_exists('default', $defs)) {
                continue;
            }

            $this->setInContainer($attribute, $defs['default']);
        }
    }

    
    protected function cloneArray(?array $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $toClone = false;

        foreach ($value as $item) {
            if (is_object($item) || is_array($item)) {
                $toClone = true;

                break;
            }
        }

        if (!$toClone) {
            return $value;
        }

        $copy = [];

        

        foreach ($value as $i => $item) {
            if (is_object($item)) {
                $copy[$i] = $this->cloneObject($item);

                continue;
            }

            if (is_array($item)) {
                $copy[$i] = $this->cloneArray($item);

                continue;
            }

            $copy[$i] = $item;
        }

        return $copy;
    }

    
    protected function cloneObject(?stdClass $value): ?stdClass
    {
        if ($value === null) {
            return null;
        }

        $copy = (object) [];

        foreach (get_object_vars($value) as $k => $item) {
            

            $key = $k;

            if (!is_string($key)) {
                $key = strval($key);
            }

            if (is_object($item)) {
                $copy->$key = $this->cloneObject($item);

                continue;
            }

            if (is_array($item)) {
                $copy->$key = $this->cloneArray($item);

                continue;
            }

            $copy->$key = $item;
        }

        return $copy;
    }

    
    public function populateFromArray(array $data, bool $onlyAccessible = true, bool $reset = false): void
    {
        if ($reset) {
            $this->reset();
        }

        foreach ($this->getAttributeList() as $attribute) {
            if (!array_key_exists($attribute, $data)) {
                continue;
            }

            if ($attribute == 'id') {
                $this->id = $data[$attribute];

                continue;
            }

            if ($onlyAccessible && $this->getAttributeParam($attribute, 'notAccessible')) {
                continue;
            }

            $value = $data[$attribute];

            $this->populateFromArrayItem($attribute, $value);
        }
    }

    
    protected function setValue($attribute, $value): void
    {
        $this->setInContainer($attribute, $value);
    }
}
