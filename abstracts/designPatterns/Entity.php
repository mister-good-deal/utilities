<?php

namespace utilities\abstracts\designPatterns;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;

abstract class Entity
{
    const ENTITIES_CONF_PATH = 'database/entities/';
    const HASH_ALGO          = 'MD5';

    private $conf;
    private $tableName;
    private $entityName;
    private $id;
    private $maxColumnNameSize = 0;
    private $maxColumnTypeSize = 0;

    protected $columnsValue      = array();
    protected $columnsAttributes = array();

    public function __construct($entityName)
    {
        Ini::setIniFileName(self::ENTITIES_CONF_PATH . $entityName . '.ini');

        $this->conf       = Ini::getAllParams();
        $this->entityName = $entityName;
        $this->parseConf();
    }

    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->columnsValue);
    }

    public function __get($columnName)
    {
        if (!$this->__isset($columnName)) {
            throw new Exception('The attribute ' . $columnName . ' is undefined', Exception::$PARAMETER);
        }

        return $this->columnsValue[$columnName];
    }

    public function __set($columnName, $value)
    {
        if (!$this->__isset($columnName)) {
            throw new Exception('The attribute ' . $columnName . ' is undefined', Exception::$PARAMETER);
        }
        
        $this->columnsValue[$columnName] = $value;
    }

    public function __toString()
    {
        $this->setBeautifullIndent();

        $string = '['  . $this->entityName . ']' . PHP_EOL;

        foreach ($this->columnsValue as $columnName => $columnValue) {
            $string .=
                '  ' . $this->smartIndent($columnName, $this->maxColumnNameSize)
                . '  ' . $this->smartIndent(
                    $this->columnsAttributes[$columnName]['type'] . '(' .
                    $this->columnsAttributes[$columnName]['size'] . ')',
                    $this->maxColumnTypeSize
                )
                . '  = ' . $this->formatValue($columnValue) . PHP_EOL;
        }

        return $string;
    }

    public function __debugInfo()
    {
        return $this->columnsValue;
    }

    /**
     * Return the entity in an array format
     *
     * @return array Array with columns name on keys and columns value on values
     */
    public function toArray()
    {
        return $this->columnsValue;
    }

    /**
     * Get the id of an entitys
     *
     * @return int|string The entity id (if multiple keys, a hash is generated)
     */
    public function getId()
    {
        return $this->id;
    }

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

                if (is_array($columnAttributes['primaryKey'])) {
                    $this->id = hash(self::HASH_ALGO, implode($columnAttributes['primaryKey']));
                } else {
                    $this->id = $columnAttributes['primaryKey'];
                }
            }
        }

        $this->columnsValue      = $columnsValue;
        $this->columnsAttributes = $columnsAttributes;
    }

    /**
     * Utility method to process and set the max size of name and type
     */
    private function setBeautifullIndent()
    {
        $maxColumnNameSize = 0;
        $maxColumnTypeSize = 0;

        foreach ($this->columnsAttributes as $columnName => $columnAttributes) {
            $currentColumnNameSize = strlen($columnName);
            $currentColumnTypeSize = strlen($columnAttributes['type']) + strlen($columnAttributes['size']);

            if ($currentColumnNameSize > $maxColumnNameSize) {
                $maxColumnNameSize = $currentColumnNameSize;
            }

            if ($currentColumnTypeSize > $maxColumnTypeSize + 2) {
                $maxColumnTypeSize = $currentColumnTypeSize + 2;
            }
        }

        $this->maxColumnNameSize = $maxColumnNameSize;
        $this->maxColumnTypeSize = $maxColumnTypeSize;
    }

    /**
     * Indent a string nicely to align "=" sign
     *
     * @param  string $value   The string to indent
     * @param  int    $maxSize The max size of all the string to indent with
     * @return string          The indented string
     */
    private function smartIndent($value, $maxSize)
    {
        return str_pad($value, $maxSize, ' ', STR_PAD_RIGHT);
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
}
