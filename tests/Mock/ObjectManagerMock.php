<?php

namespace Tests\Mock;

use Doctrine\Persistence\ObjectManager;
use function PHPUnit\Framework\throwException;

class ObjectManagerMock implements ObjectManager
{
    private $objectCollection = [];
    private $removedObjectCollection = [];
    private $isFlushed = false;

    public function find($className, $id)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function persist($object)
    {
        $this->objectCollection[] = $object;
        $this->isFlushed = false;
    }

    public function remove($object)
    {
        $this->removedObjectCollection[] = $object;
        $this->isFlushed = false;
    }

    public function merge($object)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function clear($objectName = null)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function detach($object)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function refresh($object)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function flush()
    {
        $this->isFlushed = true;
    }

    public function getRepository($className)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function getClassMetadata($className)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function getMetadataFactory()
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function initializeObject($obj)
    {
        throwException(new \RuntimeException('Not implemented'));
    }

    public function contains($object)
    {
        return in_array($object, $this->objectCollection, true);
    }

    public function isRemoved($object)
    {
        return in_array($object, $this->removedObjectCollection, true);
    }

    public function getLastPersistedObject()
    {
        return count($this->objectCollection) ? $this->objectCollection[count($this->objectCollection)-1] : null;
    }

    public function isFlushed()
    {
        return $this->isFlushed;
    }
}
