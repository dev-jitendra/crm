<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

use stdClass;

class Integration extends Entity
{
    public const ENTITY_TYPE = 'Integration';

    public function get(string $name, $params = [])
    {
        if ($name == 'id') {
            return $this->id;
        }

        if ($this->hasAttribute($name)) {
            if ($this->hasInContainer($name)) {
                return $this->getFromContainer($name);
            }
        } else {
            if ($this->get('data')) {
                $data = $this->get('data');
            }
            else {
                $data = new stdClass();
            }

            if (isset($data->$name)) {
                return $data->$name;
            }
        }

        return null;
    }

    public function clear(string $name): void
    {
        parent::clear($name);

        $data = $this->get('data');

        if (empty($data)) {
            $data = new stdClass();
        }

        unset($data->$name);

        $this->set('data', $data);
    }

    public function set($p1, $p2 = null): void
    {
        if (is_object($p1)) {
            $p1 = get_object_vars($p1);
        }

        if (is_array($p1)) {
            if ($p2 === null) {
                $p2 = false;
            }

            $this->populateFromArray($p1, $p2);

            return;
        }

        $name = $p1;
        $value = $p2;

        if ($name == 'id') {
            $this->id = $value;

            return;
        }

        if ($this->hasAttribute($name)) {
            $this->setInContainer($name, $value);
        }
        else {
            $data = $this->get('data') ?? (object) [];

            $data->$name = $value;

            $this->set('data', $data);
        }
    }

    public function isAttributeChanged(string $name): bool
    {
        if ($name === 'data') {
            return true;
        }

        return parent::isAttributeChanged($name);
    }

    
    public function populateFromArray(array $array, bool $onlyAccessible = true, bool $reset = false): void
    {
        if ($reset) {
            $this->reset();
        }

        foreach ($array as $attribute => $value) {
            if (!is_string($attribute)) {
                continue;
            }

            if ($this->hasAttribute($attribute)) {
                $value = $this->prepareAttributeValue($attribute, $value);
            }

            $this->set($attribute, $value);
        }
    }

    public function toArray()
    {
        $array = [];

        if (isset($this->id)) {
            $array['id'] = $this->id;
        }

        foreach ($this->getAttributeList() as $attribute) {
            if ($attribute === 'id') {
                continue;
            }

            if ($attribute === 'data') {
                continue;
            }

            if ($this->has($attribute)) {
                $array[$attribute] = $this->get($attribute);
            }
        }

        $data = $this->get('data') ?? (object) [];

        return array_merge(
            $array,
            get_object_vars($data)
        );
    }

    public function getValueMap(): stdClass
    {
        $arr = $this->toArray();

        return (object) $arr;
    }
}
