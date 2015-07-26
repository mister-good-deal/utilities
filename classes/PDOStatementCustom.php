<?php
/**
 * PDOStatement custom class
 *
 * @category Custom class
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace classes;

/**
 * PDOStatement custom class to print sql query on demand
 *
 * @class PDOStatementCustom
 */
class PDOStatementCustom extends \PDOStatement
{
    use \traits\EchoTrait;

    /**
     * @var \PDO $pdo PDO object instance
     */
    protected $pdo;
    /**
     * @var boolean $printSQL If the SQL queries should be printed or not
     */
    protected $printSQL;

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
     /**
     * Constructor
     *
     * @param \PDO    $pdo      $pdo value
     * @param boolean $printSQL $printSQL value
     */
    protected function __construct($pdo, $printSQL)
    {
        $this->pdo      = $pdo;
        $this->printSQL = $printSQL;
    }
    
    /*-----  End of Magic methods  ------*/
    
    /*======================================
    =            Public methods            =
    ======================================*/
    
    /**
     * Like \PDOStatement->execute() but can print the SQL query before executes it
     *
     * {@inheritdoc}
     */
    public function execute($inputParameters = null)
    {
        if ($this->printSQL && is_array($inputParameters)) {
            $this->printQuery($inputParameters);
        }

        return parent::execute($inputParameters);
    }
    
    /*-----  End of Public methods  ------*/

    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Utility method to format and print the SQL query which will be executed
     *
     * @param mixed $inputParameters The input parameters
     */
    private function printQuery($inputParameters)
    {
        $query = preg_replace_callback(
            '/[?]/',
            function ($k) use ($inputParameters) {
                static $i = 0;
                return sprintf("'%s'", $inputParameters[$i++]);
            },
            $this->queryString
        );

        static::out(PHP_EOL . $query . PHP_EOL);
    }
    
    /*-----  End of Private methods  ------*/
}
