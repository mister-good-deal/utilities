<?php

namespace utilities\abstracts\designPatterns;

use \utilities\classes\ini\IniManager as Ini;
use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\abstracts\designPatterns\Entity as Entity;

class Collection implements Iterator, ArrayAccess, Countable, SeekableIterator
{
    private $collection = array();
    private $indexId    = array();
    private $current    = 0;

    public function __construct()
    {
    }

    /**
     * Add an entity at the end of the collection
     *
     * @param  Entity $entity The entity object
     * @throws Exception      If the entity id is already in the collection
     */
    public function add($entity)
    {
        $id = $entity->getId();

        if (!array_key_exists($id, $this->indexId)) {
            throw new Exception('This entity id(' . $id .') is already in the collection', Exception::$WARNING);
        } else {
            $this->collection[] = $entity;
            $this->indexId[$id] = count($this->collection);
        }
    }

    public function getEntityById($entityId)
    {

        return $this->collection[$this->indexId[$entityId]];
    }

    /*==========  Iterator interface  ==========*/

    public function current()
    {
        return $this->collection[$this->current];
    }

    public function key()
    {
        return $this->current;
    }

    public function next()
    {
        $this->current++;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function valid()
    {
        return isset($this->collection[$this->current]);
    }

    /*==========  ArrayAccess interface  ==========*/

    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    /*==========  Countable interface  ==========*/

    public function count()
    {
        return count($this->collection);
    }

    /*==========  SeekableIterator interface  ==========*/

    public function seek($position)
    {
        if (!isset($this->collection[$position])) {
            throw new Exception('There is no data in this iterator at index ' . $position, Exception::$ERROR);
        } else {
            $this->current = $position;
        }
    }
}
