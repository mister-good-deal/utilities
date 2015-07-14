<?php
/**
 * Entity manager pattern abstract class
 *
 * @category Abstract
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\abstracts\designPatterns;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\abstracts\designPatterns\Entity as Entity;
use \utilities\abstracts\designPatterns\Collection as Collection;
use \utilities\classes\DataBase as DB;

/**
 * Abstract EntityManager pattern
 *
 * @abstract
 */
abstract class EntityManager
{
    /**
     * @var Entity $entity An entity object
     */
    private $entity;
    /**
     * @var Collection $entityCollection An entityCollection object
     */
    private $entityCollection;

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    /**
     * Constructor that can take an Entity as first parameter and a Collection as second parameter
     *
     * @param Entity     $entity           An entity object
     * @param Collection $entityCollection A colection oject
     */
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

    /*======================================
    =            Public methods            =
    ======================================*/
    
    /**
     * Save the entity in the database
     *
     * @return boolean True if the entity has been saved or updated else false
     */
    public function saveEntity()
    {
        $sucess = true;

        if ($this->entityAlreadyExists()) {
            $this->updateInDatabase();
        } else {
            $sucess = $this->saveInDatabase();
        }

        return $sucess;
    }

    /**
     * Save the entity colection in the database
     *
     * @return boolean True if the entity collection has been saved else false
     */
    public function saveCollection()
    {
        $currentEntity = $this->entity;
        $success       = true;

        DB::beginTransaction();

        foreach ($this->entityCollection as $entity) {
            if (!$success) {
                break;
            }

            $this->setEntity($entity);
            $success = $this->saveEntity();
        }
        
        if ($success) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        // restore the initial entity
        $this->entity = $currentEntity;

        return $success;
    }

    /**
     * Delete an entity in the database
     *
     * @return boolean True if the entity has beed deleted else false
     */
    public function deleteEntity()
    {
        return $this->deleteInDatabse();
    }
    
    /*-----  End of Public methods  ------*/

    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Check if the entity already exists in the database
     *
     * @return boolean True if the entity exists else false
     */
    private function entityAlreadyExists()
    {
        $sqlMarks = 'SELECT COUNT(*)
                     FROM %s
                     WHERE %s';

        $sql = $this->sqlFormater(
            $sqlMarks,
            $this->entity->getTableName(),
            $this->getEntityPrimaryKeysWhereClause()
        );

        return ((int) DB::query($sql)->fetchColumn() >= 1);
    }

    /**
     * Save the entity in the database
     *
     * @return boolean True if the entity has beed saved else false
     */
    private function saveInDatabase()
    {
        $sqlMarks = 'INSERT INTO %s
                     VALUES %s';

        $sql = $this->sqlFormater(
            $sqlMarks,
            $this->entity->getTableName(),
            $this->getEntityAttributesMarks($this->entity)
        );

        return DB::prepare($sql)->execute(array_values($this->entity->getColumnsValue()));
    }

    /**
     * Uddape the entity in the database
     *
     * @return integer The number of rows updated
     */
    private function updateInDatabase()
    {
        $sqlMarks = 'UPDATE %s
                     SET %s
                     WHERE %s';

        $sql = $this->sqlFormater(
            $sqlMarks,
            $this->entity->getTableName(),
            $this->getEntityUpdateMarksValue(),
            $this->getEntityPrimaryKeysWhereClause()
        );

        return (int) DB::exec($sql);
    }

    /**
     * Delete the entity from the database
     *
     * @return boolean True if the entity has beed deleted else false
     */
    private function deleteInDatabse()
    {
        $sqlMarks = 'DELETE FROM %s
                     WHERE %s';

        $sql = $this->sqlFormater(
            $sqlMarks,
            $this->entity->getTableName(),
            $this->getEntityPrimaryKeysWhereClause()
        );

        return ((int) DB::exec($sql) === 1);
    }

    /*==========  Utilities methods  ==========*/
    
    /**
     * Get the "?" markers of the entity
     *
     * @return string The string markers (?, ?, ?)
     */
    private function getEntityAttributesMarks()
    {
        return '(' . implode(array_fill(0, count($this->entity->getColumnsAttributes()), '?'), ', ') . ')';
    }

    /**
     * Get the "columnName = 'columnValue'" markers of the entity for the update sql command
     *
     * @return string The string markers (columnName1 = 'value1', columnName2 = 'value2') primary keys EXCLUDED
     */
    private function getEntityUpdateMarksValue()
    {
        $marks = array();

        foreach ($this->entity->getColumnsKeyValueNoPrimary() as $columnName => $columnValue) {
            $marks[] = $columnName . ' = ' . DB::quote($columnValue);
        }

        return implode(', ', $marks);
    }

    /**
     * Get the "primaryKey1 = 'primaryKey1Value' AND primaryKey2 = 'primaryKey2Value'" of the entity
     *
     * @return string The SQL segment string "primaryKey1 = 'primaryKey1Value' AND primaryKey2 = 'primaryKey2Value'"
     */
    private function getEntityPrimaryKeysWhereClause()
    {
        $columnsValue = array();

        foreach ($this->entity->getIdKeyValue() as $columnName => $columnValue) {
            $columnsValue[] = $columnName . ' = ' . DB::quote($columnValue);
        }

        return implode($columnsValue, 'AND ');
    }

    /**
     * Format a sql query with sprintf function
     * First arg must be the sql string with markers (%s, %d, ...)
     * Others args should be the values for the markers
     *
     * @return string The SQL formated string
     */
    private function sqlFormater()
    {
        return call_user_func_array('sprintf', func_get_args());
    }
    
    /*-----  End of Private methods  ------*/
}
