<?php

namespace utilities\abstracts\designPatterns;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;
use \utilities\abstracts\designPatterns\Entity as Entity;
use \utilities\abstracts\designPatterns\Collection as Collection;
use \utilities\classes\DataBase as DB;

/**
 * Abstract EntityManager pattern taht implements basic entity performed action
 *
 * @abstract
 */
abstract class EntityManager
{
    /**
     * @var Entity     $entity           An entity object
     * @var Collection $entityCollection An entityCollection object
     */
    private $entity;
    private $entityCollection;

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    public function __construct($entity = null, $entityCollection = null)
    {
        if ($entity !== null) {
            $this->setEntity($entity);
        }

        if ($entityCollection !== null) {
            $this->setEntityCollection($entityCollection);
        }
    }
    
    /*-----  End of Magic methods  ------*/
    
    /*==========================================
    =            Getters and setter            =
    ==========================================*/
    
    /**
     * Get the entity object
     *
     * @return Entity The entity object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the entity object
     *
     * @param  Entity    $entity The new entity oject
     * @throws Exception         If the entity is not a subclass of Entity
     */
    public function setEntity($entity)
    {
        if ($entity instanceof Entity) {
            $this->entity = $entity;
        } else {
            throw new Exception('The entity object must be a children of the class "Entity"', Exception::$PARAMETER);
        }
    }

    /**
     * Get the entity collection object
     *
     * @return Collection The entity colection object
     */
    public function getEntityCollection()
    {
        return $this->entityCollection;
    }
    
    /**
     * Set the entity collection object
     *
     * @param  Collection $entityCollection The new entity collection object
     * @throws Exception                    If the entityCollection is not a subclass of Collection
     */
    public function setEntityCollection($entityCollection)
    {
        if ($entityCollection instanceof Collection) {
            $this->entityCollection = $entityCollection;
        } else {
            throw new Exception(
                'The entityCollection object must be a children of the class "Collection"',
                Exception::$PARAMETER
            );
        }
    }
    
    /*-----  End of Getters and setter  ------*/

    /**
     * Save the entity in the database
     *
     * @todo Exception and return true / false
     */
    public function saveEntity($entity = null)
    {
        if ($this->entityAlreadyExists($entity)) {
            $this->updateInDatabase($entity);
        } else {
            $this->saveInDatabase($entity);
        }
    }

    public function saveCollection()
    {
        foreach ($this->entityCollection as $entity) {
            $this->saveEntity($entity);
        }
    }

    /**
     * Check if the entity already exists in the database
     *
     * @return bool True if the entity exists else false
     */
    private function entityAlreadyExists($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }

        $sql = 'SELECT COUNT(*)
                FROM %s
                WHERE %s';

        $columnsValue = array();

        // Handle multiple primary keys
        foreach ($entity->getIdKeyValue() as $columnName => $columnValue) {
            $columnsValue[] = $columnName . ' = ' . DB::quote($columnValue);
        }

        $sql = sprintf(
            $sql,
            $entity->getTableName(),
            implode($columnsValue, 'AND ')
        );

        return ((int) DB::query($sql)->fetchColumn() >= 1);
    }

    /**
     * Save the entity in the database
     *
     * @todo check SQL syntax to handle multiple SGBD
     */
    private function saveInDatabase($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }

        $query = 'INSERT INTO ' . $entity->getTableName() . ' VALUES ' . $this->getEntityAttributesMarks();

        DB::prepare($query)
           ->execute(array_values($entity->getColumnsValue()));

           // todo return the number of row affected (1 if ok else 0 (bool success ?))
    }

    /**
     * Delete the entity from the database
     *
     * @return boolean True if the entity has beed deleted else false
     */
    private function deleteInDatabse($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }

        $sql = 'DELETE FROM %s
                WHERE %s';

        $columnsValue = array();

        // Handle multiple primary keys
        foreach ($entity->getIdKeyValue() as $columnName => $columnValue) {
            $columnsValue[] = $columnName . ' = ' . DB::quote($columnValue);
        }

        $sql = sprintf(
            $sql,
            $entity->getTableName(),
            implode($columnsValue, 'AND ')
        );

        return ((int) DB::exec($sql) === 1);
    }

    /**
     * Update an entity from the database
     *
     * @throws Exception if the deletion failed
     */
    private function updateInDatabase($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }

        if (!$this->deleteInDatabse($entity)) {
            throw new Exception('Entity was not deleted', Exception::$ERROR);
        }

        $this->saveInDatabase($entity);
    }

    /**
     * Get the "?" markers of the Entity
     *
     * @return string The string markers (?, ?, ?)
     */
    private function getEntityAttributesMarks($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }

        return '(' . implode(array_fill(0, count($entity->getcolumnsAttributes()), '?'), ', ') . ')';
    }
}
