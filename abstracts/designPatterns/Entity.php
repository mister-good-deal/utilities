<?php
/**
 * Entity pattern abstract class
 *
 * @category Abstract
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\abstracts\designPatterns;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;
use \utilities\classes\DataBase as DB;

/**
 * Abstract Entity pattern
 *
 * @abstract
 */
abstract class Entity
{
    use \utilities\traits\BeautifullIndentTrait;

    /**
     * @const ENTITIES_CONF_PATH The path where the entities ini conf file are stored
     */
    const ENTITIES_CONF_PATH = 'database/entities/';

    /**
     * @var array $conf All the ini params
     */
    private $conf;
    /**
     * @var string $tableName The table entity name
     */
    private $tableName;
    /**
     * @var The entity name
     */
    private $entityName;
    /**
     * @var string $idKey Id key name
     */
    private $idKey;

    /**
     * @var array $columnsValue An associative array with column name on key and its value on value
     */
    protected $columnsValue      = array();
    /**
     * @var array $columnsAttributes An associative array with column name on key and column value on value
     */
    protected $columnsAttributes = array();

    /*=====================================
    =            Magic methods            =
    =====================================*/

    /**
     * Constructor that takes the entity name as first parameter to call the parent constructor
     *
     * @param string $entityName The entity name
     */
    public function __construct($entityName)
    {
        Ini::setIniFileName(static::ENTITIES_CONF_PATH . $entityName . '.ini');

        $this->conf       = Ini::getAllParams();
        $this->entityName = $entityName;
        $this->parseConf();
    }

    /**
     * Tell if the column name exists
     *
     * @param  string  $columnName The column name
     * @return boolean             True if the column name exists else fale
     */
    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->columnsValue);
    }

    /**
     * Get the column name value
     *
     * @param  string $columnName The column name
     * @throws Exception          If the column name does not a exist
     * @return mixed              The column value
     */
    public function __get($columnName)
    {
        if (!$this->__isset($columnName)) {
            throw new Exception('The attribute ' . $columnName . ' is undefined', Exception::$PARAMETER);
        }

        return $this->columnsValue[$columnName];
    }

    /**
     * Set the column name
     *
     * @param  string    $columnName The column name
     * @param  mixed     $value      The new column value
     * @throws Exception             If the column name does not a exist
     */
    public function __set($columnName, $value)
    {
        if (!$this->__isset($columnName)) {
            throw new Exception('The attribute ' . $columnName . ' is undefined', Exception::$PARAMETER);
        }

        $this->columnsValue[$columnName] = $value;
    }

    /**
     * Pretty output the entity
     *
     * @return string The pretty output entity
     */
    public function __toString()
    {
        $this->setMaxSize('columnName', array_keys($this->columnsValue));
        $this->setMaxSize('columnValue', array_values($this->columnsValue));
        $this->setMaxSize('columnType', array_column($this->columnsAttributes, 'type'));
        $this->setMaxSize('columnSize', array_column($this->columnsAttributes, 'size'));

        $string = '['  . $this->entityName . ']' . PHP_EOL;

        foreach ($this->columnsValue as $columnName => $columnValue) {
            $string .=
                '  ' . $this->smartAlign($columnName, 'columnName')
                . '  ' . $this->smartAlign(
                    $this->columnsAttributes[$columnName]['type'] . '(' .
                    $this->columnsAttributes[$columnName]['size'] . ')',
                    array('columnType', 'columnSize'),
                    2
                )
                . '  = ' . $this->formatValue($columnValue) . PHP_EOL;
        }

        return $string;
    }

    /**
     * Return the entity in an array format
     *
     * @return array Array with columns name on keys and columns value on values
     */
    public function __toArray()
    {
        return $this->columnsValue;
    }

    /**
     * Info to display when using a var_dump on the entity
     *
     * @return array The var_dump info
     */
    public function __debugInfo()
    {
        return $this->columnsValue;
    }

    /*-----  End of Magic methods  ------*/

    /*==========================================
    =            Getters and setter            =
    ==========================================*/

    /**
     * Get the key id of an entity
     *
     * @return string[] The entity key id
     */
    public function getIdKey()
    {
        $idKey = array();

        if (is_array($this->idKey)) {
            foreach ($this->idKey as $columnName) {
                $idKey[] = $columnName;
            }
        } else {
            $idKey[] = $this->idKey;
        }

        return $idKey;
    }

    /**
     * Get the id value of the entity
     *
     * @return int|int[] The id value(s)
     */
    public function getIdValue()
    {
        if (is_array($this->idKey)) {
            $idValue = array();

            foreach ($this->idKey as $columnName) {
                $idValue[] = $this->__get($columnName);
            }
        } else {
            $idValue = $this->__get($this->idKey);
        }

        return $idValue;
    }

    /**
     * Get the associative array idKey => idValue
     *
     * @return array The associative array idKey => idValue
     */
    public function getIdKeyValue()
    {
        $idKeyValue = array();

        if (is_array($this->idKey)) {
            foreach ($this->idKey as $columnName) {
                $idKeyValue[$columnName] = $this->__get($columnName);
            }
        } else {
            $idKeyValue[$this->idKey] = $this->getIdValue();
        }

        return $idKeyValue;
    }

    /**
     * Get the associative array columnName => columnValue primary keys EXCLUDED
     *
     * @return array The associative array columnName => columnValue primary keys EXCLUDED
     */
    public function getColumnsKeyValueNoPrimary()
    {
        $columnsKeyValue = array();

        if (!is_array($this->idKey)) {
            $idKeys = array($this->idKey);
        } else {
            $idKeys = $this->idKey;
        }

        foreach ($this->columnsValue as $columnName => $columnValue) {
            if (!in_array($columnName, $idKeys)) {
                $columnsKeyValue[$columnName] = $columnValue;
            }
        }

        return $columnsKeyValue;
    }

    /**
     * Set the id value of the entity (can be an array if several primary keys)
     *
     * @param  int|array The id value
     * @throws Exception If the id is on several columns and $value is not an array
     * @throws Exception If the id key is not found
     */
    public function setIdValue($value)
    {
        if (is_array($this->idKey)) {
            if (!is_array($value)) {
                throw new Exception(
                    'The id is on several columns you must passed an assosiative array with keys (' .
                    implode(', ', $this->idKey) . ')',
                    Exception::$PARAMETER
                );
            }

            foreach ($value as $key => $val) {
                if (!array_key_exists($key, $this->columnsValue)) {
                    throw new Exception(
                        'The keys of the assosiative array must be one of these : ' . implode(', ', $this->idKey),
                        Exception::$PARAMETER
                    );
                }

                $this->columnAttributes[$key] = $val;
            }
        } else {
            $this->columnAttributes[$this->idKey] = $value;
        }
    }

    /**
     * Get the entity table name
     *
     * @return string The entity table name
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the columns attributes
     *
     * @return array The columns attributes
     */
    public function getColumnsAttributes()
    {
        return $this->columnsAttributes;
    }

    /**
     * Get the columns value
     *
     * @return array The columns value
     */
    public function getColumnsValue()
    {
        return $this->columnsValue;
    }

    /**
     * Get the entity name
     *
     * @return string The entity name
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /*-----  End of Getters and setter  ------*/

    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Parse an entity conf to extract attributes
     */
    private function parseConf()
    {
        $columnsValue = array();

        foreach ($this->conf as $columnName => $columnAttributes) {
            if ($columnName !== 'table') {
                $columnsValue[$columnName]      = null;
                $columnsAttributes[$columnName] = $columnAttributes;
            } else {
                $this->tableName = $columnAttributes['name'];

                if (isset($columnAttributes['primaryKey'])) {
                    $this->idKey = $columnAttributes['primaryKey'];
                }
            }
        }

        $this->columnsValue      = $columnsValue;
        $this->columnsAttributes = $columnsAttributes;
    }

    /**
     * Cast a SQL return value to a string value
     *
     * @param  mixed $value The typed value
     * @return string       The string value
     */
    private function formatValue($value)
    {
        $formatedValue;

        switch (gettype($value)) {
            case 'boolean':
                $formatedValue = $value ? 'TRUE' : 'FALSE';
                break;

            case 'integer':
                $formatedValue = (int) $value;
                break;

            case 'string':
                $formatedValue = '"' . $value . '"';
                break;

            case 'NULL':
                $formatedValue = 'NULL';
                break;

            default:
                $formatedValue = $value;
                break;
        }

        return $formatedValue;
    }
    
    /*-----  End of Private methods  ------*/
}