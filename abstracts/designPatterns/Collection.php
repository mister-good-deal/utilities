<?php

namespace utilities\abstracts\designPatterns;

use \utilities\classes\ini\IniManager as Ini;
use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\abstracts\designPatterns\Entity as Entity;

class Collection implements Iterator, ArrayAccess, Countable, SeekableIterator
{
    /**
     * @var Entity[]       $collection An array of entity object
     * @var int[]|string[] $indexId    An array of entity id key
     * @var int            $current    Current position of the pointer in the $collection
     */
    private $collection = array();
    private $indexId    = array();
    private $current    = 0;

    public function __construct()
    {
    }

    /**
     * Add an entity at the end of the collection
     *
     * @param  Entity    $entity The entity object
     * @throws Exception         If the entity id is already in the collection
     */
    public function add($entity)
    {
        $id = $entity->getId();

        if (array_key_exists($id, $this->indexId)) {
            throw new Exception('This entity id(' . $id .') is already in the collection', Exception::$WARNING);
        } else {
            $this->collection[] = $entity;
            $this->indexId[$id] = $this->count();
        }
    }

    /**
     * Get an entity by its id
     *
     * @param  int|string $entityId The entityId
     * @throws Exception            If the entity id is not in the collection
     * @return Entity               The entity
     */
    public function getEntityById($entityId)
    {
        if (!array_key_exists($entityId, $this->indexId)) {
            throw new Exception('This entity id(' . $entityId . ') is not in the collection', Exception::$WARNING);
        }

        return $this->collection[$this->indexId[$entityId]];
    }

    /*==========  Iterator interface  ==========*/

    /**
     * Returns the current element
     *
     * @return Entity The current entity
     */
    public function current()
    {
        return $this->collection[$this->current];
    }

    /**
     * Returns the key of the current entity
     *
     * @return int|null Returns the key on success, or NULL on failure
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Moves the current position to the next element
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * Rewinds back to the first element of the Iterator
     */
    public function rewind()
    {
        $this->current = 0;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean Returns true on success or false on failure
     */
    public function valid()
    {
        return isset($this->collection[$this->current]);
    }

    /*==========  ArrayAccess interface  ==========*/

    /**
     * Whether an offset exists
     *
     * @param int|string $offset An offset to check for
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * Returns the entity at specified offset
     *
     * @param  int|string $offset The offset to retrieve
     * @return Entity             Return the matching entity
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * Assigns an entity to the specified offset
     *
     * @param int|string  $offset The offset to assign the entity to
     * @param Entity      $entity The entity to set
     */
    public function offsetSet($offset, $entity)
    {
        $this->collection[$offset] = $entity;
    }

    /**
     * Unsets an offset
     *
     * @param int|string $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    /*==========  Countable interface  ==========*/

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer
     */
    public function count()
    {
        return count($this->collection);
    }

    /*==========  SeekableIterator interface  ==========*/

    /**
     * Seeks to a position
     *
     * @param  int $position The position to seek to
     * @throws Exception     If the position is not seekable
     */
    public function seek($position)
    {
        if (!isset($this->collection[$position])) {
            throw new Exception('There is no data in this iterator at index ' . $position, Exception::$ERROR);
        } else {
            $this->current = $position;
        }
    }
}
