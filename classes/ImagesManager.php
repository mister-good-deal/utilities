<?php
/**
 * Images manipulation utilities class
 *
 * @category Utility
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace classes;

use \classes\ExceptionManager as Exception;

/**
 * Images manipulation class with usefull utilities methods
 *
 * @link https://en.wikipedia.org/wiki/Display_resolution
 * @class ImagesManager
 */
class ImagesManager
{
    /**
     * @var integer[] $WIDTHS_16_9 Commons 16/9 ratios width
     */
    public static $WIDTHS_16_9 = array(2560, 2048, 1920, 1600, 1536, 1366, 1360, 1280, 1093);
    /**
     * @var integer[] $HEIGHTS_16_9 Commons 16/9 ratios height
     */
    public static $HEIGHTS_16_9 = array(1440, 1152, 1080, 900, 864, 768, 720, 614);
    /**
     * @var integer[] $MOST_USE_WIDTHS Most use width
     */
    public static $MOST_USE_WIDTHS = array(1920, 1600, 1440, 1366, 1280, 1024, 768, 480);
    /**
     * @var integer[] $MOST_USE_HEIGHTS  Most use height
     */
    public static $MOST_USE_HEIGHTS = array(1080, 1050, 1024, 900, 800, 768);

    /**
     * @var \Imagick $image \Imagick instance DEFAULT null
     */
    private $image = null;
    /**
     * @var string $imageName The image name
     */
    private $imageName;
    /**
     * @var string $imageExtension The image extension
     */
    private $imageExtension;

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    /**
     * Constructor which can instanciate a new \Imagick object if a path is specified
     *
     * @param string $imagePath OPTIONAL the image path
     */
    public function __construct($imagePath = '')
    {
        if ($imagePath !== '') {
            $this->setImage($imagePath);
        }
    }

    /**
     * destructor, free the image ressource memory
     */
    public function __destruct()
    {
        if ($this->image !== null) {
            $this->image->destroy();
        }
    }
    
    /*-----  End of Magic methods  ------*/

    /*======================================
    =            Public methods            =
    ======================================*/
    
    /**
     * Instantiate a new \Imagick object and destroyed the last if exists
     *
     * @param  string    $imagePath The image path
     * @throws Exception            If there is no image at the specified path
     * @throws Exception            If there is an error on image creation
     */
    public function setImage($imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new Exception('There is no image at this path : "' . $imagePath . '"', Exception::$PARAMETER);
        }

        $this->__destruct();

        try {
            $this->image          = new \Imagick($imagePath);
            $this->imageName      = pathinfo($imagePath, PATHINFO_FILENAME);
            $this->imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), Exception::$ERROR);
        }
    }

    /**
     * Generate and save scales images with specified widths
     *
     * @param integer[] $widths The widths to resize the image with
     *                          DEFAULT [1920, 1600, 1440, 1366, 1280, 1024, 768, 480]
     * @param string    $path   OPTIONAL the absolute path where images has to be saved DEFAULT ""
     */
    public function generateResizedImagesByWidth(
        $widths = array(1920, 1600, 1440, 1366, 1280, 1024, 768, 480),
        $path = ''
    ) {
        foreach ($widths as $width) {
            $this->generateResizedImages($width, 0, $path);
        }
    }

    /**
     * Generate and save scales images with specified heights
     *
     * @param integer[] $heights The heights to resize the image with
     *                           DEFAULT [1080, 1050, 1024, 900, 800, 768]
     * @param string    $path    OPTIONAL the absolute path where images has to be saved DEFAULT ""
     */
    public function generateResizedImagesByHeight($heights = array(1080, 1050, 1024, 900, 800, 768), $path = '')
    {
        foreach ($heights as $height) {
            $this->generateResizedImages(0, $height, $path);
        }
    }
    
    /*-----  End of Public methods  ------*/
    
    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Generate and save scales images with specified width and height
     *
     * @param integer $width  The width to resize the image with
     * @param integer $height The height to resize the image with
     * @param string  $path   OPTIONAL the absolute path where images has to be saved DEFAULT ""
     */
    private function generateResizedImages($width, $height, $path = '')
    {
        if ($path !== '' && !is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $this->image->scaleImage($width, $height);

        if ($width === 0) {
            $width = $this->image->getImageWidth();
        }

        if ($height === 0) {
            $height = $this->image->getImageHeight();
        }

        $this->image->writeImage($path . $this->imageName . '_' . $width . 'x' . $height . '.' . $this->imageExtension);
    }
    
    /*-----  End of Private methods  ------*/
}
