<?php

namespace utilities\abstracts\designPatterns;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;
use \utilities\abstracts\designPatterns\Entity as Entity;
use \utilities\classes\DataBase as DB;

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
    
    public function __construct($entityName)
    {
        $this->entity = new Entity($entityName);
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
     * @param Entity $entity The new entity oject
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
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
     * @param Collection $entityCollection The new entity collection object
     */
    public function setEntityCollection($entityCollection)
    {
        $this->entityCollection = $entityCollection;
    }
    
    /*-----  End of Getters and setter  ------*/

    /**
     * Save the entity in the database
     *
     * @todo Exception and return true / false
     */
    public function save()
    {
        if ($this->entityAlreadyExists()) {
            $this->updateInDatabase();
        } else {
            $this->saveInDatabase();
        }
    }

    /**
     * Check if the entity already exists in the database
     *
     * @return bool True if the entity exists else false
     */
    private function entityAlreadyExists()
    {
        $sql = 'SELECT COUNT(*)
                FROM %s
                WHERE %s';

        $columnsValue = array();

        // Handle multiple primary keys

        foreach ($this->entity->getIdKeyValue() as $columnName => $columnValue) {
            $columnsValue[] = $columnName . ' = ' . DB::quote($columnValue);
        }

        $sql = sprintf(
            $sql,
            $this->entity->getTableName(),
            implode($columnsValue, 'AND ')
        );

        return (int) DB::exec($sql) === 1;
    }

    /**
     * Save the entity in the database
     *
     * @todo check SQL syntax to handle multiple SGBD
     */
    private function saveInDatabase()
    {
        DB::prepare('INSERT INTO ' . $this->entity->getTableName() . ' VALUES ' . $this->getEntityAttributesMarks())
           ->execute(array_values($this->entity->getColumnsValue()));

           // todo return the number of row affected (1 if ok else 0 (bool success ?))
    }

    /**
     * Delete the entity from the database
     *
     * @return boolean True if the entity has beed deleted else false
     */
    private function deleteInDatabse()
    {
        return (int) DB::exec(
            'DELETE FROM ' . $this->tableName . '
             WHERE ' .$this->getIdKey() . ' = ' . DB::quote($this->getId())
        ) === 1;
    }

    /**
     * Update an entity from the database
     *
     * @throws Exception if the deletion failed
     */
    private function updateInDatabase()
    {
        if (!$this->deleteInDatabse()) {
            throw new Exception('Entity was not deleted', Exception::$ERROR);
        }

        $this->saveInDatabase();
    }

    /**
     * Get the "?" markers of the Entity
     *
     * @return string The string markers (?, ?, ?)
     */
    private function getEntityAttributesMarks()
    {
        return '(' . implode(array_fill(0, count($this->entity->getcolumnsAttributes()), '?'), ', ') . ')';
    }
}
