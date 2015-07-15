<?php
/**
 * Trait to set beautifull indent on multiple array values
 *
 * @category Trait
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\traits;

use \utilities\classes\exception\ExceptionManager as Exception;

/**
 * Utility methods to smart align values
 *
 * @trait BeautifullIndentTrait
 */
trait BeautifullIndentTrait
{
    /**
     * @var array $beautifullIndentMaxSize Array containing the max size of each category values
     */
    private static $beautifullIndentMaxSize = array();
    /**
     * @var string[] $md5Categories Array containing the md5 hash of the categories array
     */
    private static $md5Categories           = array();

    /**
     * Process the max value size of a category
     *
     * If the category is processed and the array didn't change, the category is not reprocessed
     *
     * @param string  $category The category name
     * @param array   $strings  The category values
     * @param integer $minSize  The minium size DEFAULT 0
     */
    public function setMaxSize($category, $strings = array(), $minSize = 0)
    {
        if (!isset(static::$beautifullIndentMaxSize[$category]) ||
            $this->md5Array($strings) !== static::$md5Categories[$category]
        ) {
            $max = 0;

            foreach ($strings as $string) {
                $stringSize = strlen((string) $string);

                if ($stringSize > $max) {
                    $max = $stringSize;
                }
            }

            static::$beautifullIndentMaxSize[$category] = max($max, $minSize);
            static::$md5Categories[$category]           = $this->md5Array($strings);
        }
    }

    /**
     * Return the value with the exact number of right extra spaces to keep all the values align
     *
     * @param  string           $value     The value to print
     * @param  string|string[]  $category  The category (can be multiple if needed)
     * @param  integer          $extraSize An extra size to add to the max value size of the category
     * @param  integer          $position  The position to align as str_pad constant DEFAULT STR_PAD_RIGHT
     * @return string                      The formatted value with extra spaces
     */
    public function smartAlign($value, $category, $extraSize = 0, $position = STR_PAD_RIGHT)
    {
        if (is_array($category)) {
            $max = 0;

            foreach ($category as $categoryName) {
                $max += static::$beautifullIndentMaxSize[$categoryName];
            }
        } else {
            $max = static::$beautifullIndentMaxSize[$category];
        }

        return str_pad($value, $max + $extraSize, ' ', $position);
    }

    /**
     * Get the max size of a category
     *
     * @param  string $category The category
     * @return integer          The max size
     */
    public function getMaxSize($category)
    {
        if (!isset(static::$beautifullIndentMaxSize[$category])) {
            throw new Exception('The category ' . $category . ' does not exist', Exception::$PARAMETER);
        }

        return static::$beautifullIndentMaxSize[$category];
    }

    /**
     * Get the md5 hash of an array
     *
     * @param  array  $array The array to hash
     * @return string        The md5 hash
     */
    private function md5Array($array)
    {
        array_multisort($array);
        
        return md5(json_encode($array));
    }
}