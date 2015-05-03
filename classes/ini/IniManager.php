<?php
/**
 * @author Romain Laneuville <romain.laneuville@hotmail.fr>
 * @link https://github.com/ZiperRom1/utilities/blob/master/classes/ini/IniManager.php GitHub repo
 */

namespace utilities\classes\ini;

use \utilities\classes\exception\ExceptionManager as Exception;

/**
* IniManager
*
* Helper class to provide access read and write to an ini conf file.
* Can set and get any parameter in the given ini file.
*
* @example IniManager::setParam('FileLogger', 'filePath', 'C:\prog\utilities\log.txt');
* @example IniManager::getParam('FileLogger', 'filePath');
*/
class IniManager
{
    /**
     * ini file path (can be only the file name if the path is under the include_path of php ini conf)
     */
    const INI_FILE_NAME = 'confTest.ini';

    private static $iniValues;
    private static $initialized = false;
    
    /*==========  Private constructor (singleton pattern)  ==========*/
    
    private function __construct()
    {

    }

    /**
     * One time call constructor to get ini values
     */
    private static function initialize()
    {
        if (!self::$initialized) {
            self::$iniValues   = parse_ini_file(self::INI_FILE_NAME, true);
            self::$initialized = true;
        }
    }

    /*==========  Public methods  ==========*/

    /**
     * Get all the parameters of the specified section
     *
     * @param  string       $section The section name
     * @throws Exception             If each final section type is not a string
     * @throws Exception             If any section doesn't exist in the ini file
     * @return array                 An array containing all the params values
     */
    public static function getParams($section = null)
    {
        self::initialize();
        $return = array();

        if (is_array($section)) {
            foreach ($section as $sectionLevel) {
                if (is_string($sectionLevel)) {
                    if (self::sectionExists($section)) {
                        $return[$sectionLevel] = self::$iniValues[$sectionLevel];
                    } else {
                        throw new Exception(
                            'ERROR::The section ' . $sectionLevel . ' doesn\'t exist in the ini conf file',
                            Exception::$WARNING
                        );
                    }
                } else {
                    throw new Exception(
                        'ERROR::Parameter section must be a String or an array of String',
                        Exception::$PARAMETER
                    );
                }
            }
        } elseif (is_string($section)) {
            if (self::sectionExists($section)) {
                $return[$section] = self::$iniValues[$section];
            } else {
                throw new Exception(
                    'ERROR::The section ' . $section . ' doesn\'t exist in the ini conf file',
                    Exception::$WARNING
                );
            }

            $return = self::$iniValues[$section];
        } else {
            throw new Exception(
                'ERROR::Parameter section must be a String or an array of String',
                Exception::$PARAMETER
            );
        }

        return $return;
    }

    /**
     * Get the param value of the specified section
     *
     * @param  string    $section The section name
     * @param  string    $param   The param name
     * @throws Exception          If the param name is not a string
     * @throws Exception          If the param doesn't exist in the specified section
     * @return mixed              The param value (can be any type)
     */
    public static function getParam($section = null, $param = null)
    {
        self::initialize();

        $params = self::getParams($section);

        if (is_string($param)) {
            if (self::paramExists($section, $param)) {
                return $params[$param];
            } else {
                throw new Exception(
                    'ERROR::The section ' . $section . ' doesn\'t contain the parameter ' . $param,
                    Exception::$WARNING
                );
            }
        } else {
            throw new Exception('ERROR::Second parameter must be a String (the param name)', Exception::$PARAMETER);
        }
    }

    /**
     * Get all parmameters of the ini file
     *
     * @return array Multi dimensional array
     */
    public static function getAllParams()
    {
        self::initialize();

        return self::$iniValues;
    }

    /**
     * Get all the sections first level name
     *
     * @return array One dimensional array
     */
    public static function getSections()
    {
        self::initialize();

        return array_keys(self::$iniValues);
    }

    /**
     * Set a parameter in the ini file
     *
     * @param string $section The section name
     * @param string $param   The param name
     * @param mixed  $value   The param value (can be any type except multi dimensional array)
     */
    public static function setParam($section = null, $param = null, $value = null)
    {
        self::initialize();
        self::addParam($section, $param, $value);
        self::$initialized = false;
    }

    /*==========  Private methods  ==========*/
    
    /**
     * Check if the section exists in teh ini file
     *
     * @param  string $section The section name
     * @return bool            Section exists
     */
    private static function sectionExists($section)
    {
        return array_key_exists($section, self::$iniValues);
    }

    /**
     * Check if the parameter exists in the specified section
     *
     * @param  string $section The section name
     * @param  string $param   The parameter name
     * @return bool            Parameter exists
     */
    private static function paramExists($section, $param)
    {
        return array_key_exists($param, self::$iniValues[$section]);
    }

    /**
     * Helper to add or set a param in the ini file
     *
     * @param string $section The section name
     * @param string $param   The param name
     * @param mixed $value    The param value (can be any type except mutli dimensional array)
     */
    private static function addParam($section, $param, $value)
    {
        self::initialize();
        self::$iniValues[$section][$param] = $value;

        file_put_contents(
            self::INI_FILE_NAME,
            self::arrayToIni(),
            FILE_USE_INCLUDE_PATH | LOCK_EX
        );
    }

    /**
     * Helper to parse an ini array conf to an ini string (ini file content)
     *
     * @return string The ini file content
     */
    private static function arrayToIni()
    {
        $iniString = '';

        foreach (self::$iniValues as $section => $sectionValue) {
            $iniString .= "[" . $section . "]\n";

            foreach ($sectionValue as $param => $value) {
                if (is_array($value)) {
                    foreach ($value as $subSectionLevel => $subSectionValue) {
                        $iniString .= $param
                        . "["
                        . $subSectionLevel
                        ."] = "
                        . (is_numeric($subSectionValue) ? $subSectionValue : '"' . $subSectionValue . '"')
                        ."\n";
                    }
                } else {
                    $iniString .= $param
                        ." = "
                        . (is_numeric($value) ? $value : '"' . $value . '"')
                        ."\n";
                }
            }

            $iniString .= "\n";
        }

        return trim($iniString);
    }
}
