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

    private static $iniSections;
    private static $iniAll;
    private static $initialized = false;
    
    private function __construct()
    {

    }

    private static function initialize()
    {
        if (!self::$initialized) {
            self::$iniAll      = parse_ini_file(self::INI_FILE_NAME, false);
            self::$iniSections = parse_ini_file(self::INI_FILE_NAME, true);
            self::$initialized = true;
        }
    }

    public static function getParamsFromSection($section = null)
    {
        self::initialize();

        if (is_array($section)) {
            $return = array();

            foreach ($section as $sectionLevel) {
                if (is_string($sectionLevel)) {
                    if (array_key_exists($sectionLevel, self::$iniSections)) {
                        $return[$sectionLevel] = self::$iniSections[$sectionLevel];
                    } else {
                        throw new Exception(
                            'ERROR::The section ' . $sectionLevel . ' doesn\'t exist in the ini conf file',
                            LogLevel::WARNING
                        );
                    }
                } else {
                    throw new Exception(
                        'ERROR::Parameter must be a String or an array of String',
                        LogLevel::PARAMETER
                    );
                }
            }
        } elseif (is_string($section)) {
            if (array_key_exists($section, self::$iniSections)) {
                $return[$section] = self::$iniSections[$section];
            } else {
                throw new Exception(
                    'ERROR::The section ' . $section . ' doesn\'t exist in the ini conf file',
                    LogLevel::WARNING
                );
            }

            $return = self::$iniSections[$section];
        } else {
            throw new Exception('ERROR::Parameter must be a String or an array of String', LogLevel::PARAMETER);
        }

        return $return;
    }

    public static function getParamFromSection($section = null, $param = null)
    {
        self::initialize();

        if (is_string($section)) {
            $params = self::getParamsFromSection($section);

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
        } else {
            throw new Exception('ERROR::First parameter must be a String (the section name)', LogLevel::PARAMETER);
        }
    }

    public static function getAllParams()
    {
        self::initialize();

        return self::$iniAll;
    }

    public static function getParam($param)
    {
        self::initialize();

        if (is_string($param)) {
            if (array_key_exists($param, self::$iniAll)) {
                return self::$iniAll[$param];
            } else {
                throw new Exception('ERROR::' . $param . ' is not defined in INI file', LogLevel::WARNING);
            }
        } else {
            throw new Exception('ERROR::Parameter must be a String or an array of String', LogLevel::PARAMETER);
        }
    }
}
