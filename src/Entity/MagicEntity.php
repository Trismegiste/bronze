<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze\Entity;

use Trismegiste\Strangelove\MongoDb\Root;
use Trismegiste\Strangelove\MongoDb\RootImpl;

/**
 * Generic Entity
 */
class MagicEntity implements Root
{

    use RootImpl {
        bsonSerialize as protected defaultSerialize;
        bsonUnserialize as protected defaultUnserialize;
    }

    protected array $container = [];

    public function __get($name)
    {
        return key_exists($name, $this->container) ? $this->container[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }

    public function __isset($name)
    {
        return key_exists($name, $this->container);
    }

    public function bsonSerialize(): object|array
    {
        $ret = $this->defaultSerialize();
        if (key_exists('_id', $ret)) {
            $ret['container']['_id'] = $ret['_id'];
        }

        return $ret['container'];
    }

    public function bsonUnserialize(array $data): void
    {
        $pk = $data['_id'];
        unset($data['_id']);
        unset($data['__pclass']);
        $this->defaultUnserialize(['_id' => $pk, 'container' => $data]);
    }

    public function getAttributes(): array
    {
        return array_keys($this->container);
    }

}
