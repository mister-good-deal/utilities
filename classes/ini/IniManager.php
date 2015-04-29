<?php

namespace utilities\classes\ini;

use \utilities\classes\logger\LogLevel as LogLevel;
use \utilities\classes\exception\ExceptionManager as Exception;

/**
* IniManager
*/
class IniManager
{
    const INI_FILE_NAME = 'conf.ini';

    private static $iniValues;
    private static $initialized = false;
    
    private function __construct()
    {

    }

    private static function initialize()
    {
        if (!self::$initialized) {
            self::$iniValues   = parse_ini_file(self::INI_FILE_NAME, true);
            self::$initialized = true;
        }
    }

    public static function getParams($section = null)
    {
        self::initialize();

        if (is_array($section)) {
            $return = array();

            foreach ($section as $sectionLevel) {
                if (is_string($sectionLevel)) {
                    if (array_key_exists($sectionLevel, self::$iniValues)) {
                        $return[$sectionLevel] = self::$iniValues[$sectionLevel];
                    } else {
                        throw new Exception(
                            'ERROR::The section ' . $sectionLevel . ' doesn\'t exist in the ini conf file',
                            LogLevel::WARNING
                        );
                    }
                } else {
                    throw new Exception(
                        'ERROR::Parameter section must be a String or an array of String',
                        LogLevel::PARAMETER
                    );
                }
            }
        } elseif (is_string($section)) {
            if (array_key_exists($section, self::$iniValues)) {
                $return[$section] = self::$iniValues[$section];
            } else {
                throw new Exception(
                    'ERROR::The section ' . $section . ' doesn\'t exist in the ini conf file',
                    LogLevel::WARNING
                );
            }

            $return = self::$iniValues[$section];
        } else {
            throw new Exception('ERROR::Parameter section must be a String or an array of String', LogLevel::PARAMETER);
        }

        return $return;
    }

    public static function getParam($section = null, $param = null)
    {
        self::initialize();

        $params = self::getParams($section);

        if (is_string($param)) {
            if (array_key_exists($param, $params)) {
                return $params[$param];
            } else {
                throw new Exception(
                    'ERROR::The section ' . $section . ' doesn\'t contain the parameter ' . $param,
                    LogLevel::WARNING
                );
            }
        } else {
            throw new Exception('ERROR::Second parameter must be a String (the param name)', LogLevel::PARAMETER);
        }
    }

    public static function getAllParams()
    {
        self::initialize();

        return self::$iniValues;
    }

    public function getSections()
    {
        self::initialize();

        return array_keys(self::$iniValues);
    }

    public function setParam($section = null, $param = null, $value = null)
    {
        // if (in_array($section))
    }
}
