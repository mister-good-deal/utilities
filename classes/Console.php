<?php
/**
 * ORM console mode
 *
 * @category ORM
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\classes;

use \utilities\classes\DataBase as DB;

/**
 * ORM in a console mode with simple command syntax to manage the database
 *
 * @class Console
 */
class Console
{
    use \utilities\traits\BeautifullIndentTrait;
    use \utilities\traits\FiltersTrait;

    /**
     * @var string[] $COMMANDS List of all commands with their description
     */
    private static $COMMANDS = array(
        'exit'                                          => 'Exit the ORM console',
        'last cmd'                                      => 'Get the last command written',
        'all cmd'                                       => 'Get all the commands written',
        'tables'                                        => 'Get all the tables name',
        'clean -t tableName'                            => 'Delete all the row of the given table name',
        'show -t tableName [-s startIndex -e endIndex]' => 'Show table data begin at startIndex and stop at endIndex',
        'help'                                          => 'Display all the commands'
    );

    /**
     * @var string[] $commandsHistoric Historic of all the command written by the user in the current console session
     */
    private $commandsHistoric = array();

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    /*-----  End of Magic methods  ------*/

    /*======================================
    =            Public methods            =
    ======================================*/
    
    /**
     * Launch a console session
     */
    public function launchConsole()
    {
        echo PHP_EOL . 'Welcome to the ORM in console' . PHP_EOL . PHP_EOL;
        $this->processCommand($this->userInput());
    }
    
    /*-----  End of Public methods  ------*/

    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Let the user enter a command in his console input
     *
     * @return string The command written by the user
     */
    private function userInput()
    {
        echo 'cmd: ';

        $handle = fopen('php://stdin', 'r');
       
        return trim(fgets($handle));
    }

    /**
     * Process the command entered by the user and output the result in the console
     *
     * @param  string $command The command passed by the user
     */
    private function processCommand($command)
    {
        $exit = false;
        preg_match('/^[a-zA-Z ]+/', $command, $commandName);

        echo PHP_EOL;

        switch (rtrim($commandName[0])) {
            case 'exit':
                $exit = true;
                echo 'ORM console closing' . PHP_EOL;
                break;

            case 'last cmd':
                echo 'The last cmd was: ' . $this->getLastCommand() . PHP_EOL;
                break;

            case 'all cmd':
                echo 'Commands historic:' . $this->tablePrettyPrint($this->commandsHistoric) . PHP_EOL;
                break;

            case 'tables':
                echo 'Tables name: ' . PHP_EOL . $this->tablePrettyPrint(DB::getAllTables()) . PHP_EOL;
                break;

            case 'clean':
                $this->cleanTable($command);
                break;

            case 'show':
                $this->showTable($command);
                break;

            case 'help':
                echo 'List of all commands' . PHP_EOL . $this->tableAssociativPrettyPrint(self::$COMMANDS, 'comands');
                break;

            default:
                echo 'The command : "' . $command
                    . '" is not recognized as a command, type help to display all the commands' . PHP_EOL;
                break;
        }

        echo PHP_EOL;

        if ($command !== $this->getLastCommand()) {
            $this->commandsHistoric[] = $command;
        }

        if (!$exit) {
            $this->processCommand($this->userInput());
        }
    }

    /**
     * Delete all the data in a table
     *
     * @param string $command The command passed with its arguments
     */
    private function cleanTable($command)
    {
        $args = $this->getArgs($command);

        if (!isset($args['t'])) {
            echo 'You need to specify a table name with -t parameter' . PHP_EOL;
        } elseif (!in_array($args['t'], DB::getAllTables())) {
            echo 'The table "' . $args['t'] . '" does not exist' . PHP_EOL;
        } else {
            DB::cleanTable($args['t']);
            echo 'The table "' . $args['t'] . '" is cleaned' . PHP_EOL;
        }
    }

    /**
     * Display the data of a table
     *
     * @param  string $command The commande passed by the user with its arguments
     */
    private function showTable($command)
    {
        $args = $this->getArgs($command);
        $data = null;

        if (!isset($args['t'])) {
            echo 'You need to specify a table name with -t parameter' . PHP_EOL;
        } elseif (!in_array($args['t'], DB::getAllTables())) {
            echo 'The table "' . $args['t'] . '" does not exist' . PHP_EOL;
        } elseif (isset($args['s']) && isset($args['e']) && is_numeric($args['s']) && is_numeric($args['e'])) {
            $data = DB::showTable($args['t'], $args['s'], $args['e']);
        } else {
            $data = DB::showTable($args['t']);
        }

        if ($data !== null) {
            echo $this->prettySqlResult($args['t'], $data) . PHP_EOL;
        }
    }

    /**
     * Get the last command passed by the user
     *
     * @return string The last command
     */
    private function getLastCommand()
    {
        $nbCommands = count($this->commandsHistoric);

        if ($nbCommands > 0) {
            $cmd = $this->commandsHistoric[$nbCommands - 1];
        } else {
            $cmd = '';
        }

        return $cmd;
    }

    /**
     * Get the command arguments in an array (argName => argValue)
     *
     * @param  string $command The command
     * @return array           The arguments in an array (argName => argValue)
     */
    private function getArgs($command)
    {
        preg_match_all('/\-(?P<argKey>[a-zA-Z]+) (?P<argValue>[a-zA-Z0-9 _]+)/', $command, $matches);

        return $this->filterPregMatchAllWithFlags($matches, 'argKey', 'argValue');
    }

    /**
     * Pretty output a table without keys
     *
     * @param  array $table The table to print
     * @return string       The pretty output table data
     */
    private function tablePrettyPrint($table)
    {
        return PHP_EOL . '- ' . implode(PHP_EOL . '- ', $table);
    }

    /**
     * Pretty output a table with keys
     *
     * @param  array  $table    The associative array to print
     * @param  string $category The table category to keep the pretty align in memory
     * @return string           The pretty output table data
     */
    private function tableAssociativPrettyPrint($table, $category)
    {
        $this->setMaxSize($category, array_keys($table));

        $string = '';

        foreach ($table as $key => $value) {
            $string .= $this->smartAlign($key, $category) . ' : ' . $value . PHP_EOL;
        }

        return PHP_EOL . $string;
    }

    /**
     * Format the SQL result in a pretty output
     *
     * @param  string $tableName The table name
     * @param  array  $data      Array containing the SQL result
     * @return string            The pretty output
     */
    private function prettySqlResult($tableName, $data)
    {
        $columns       = $this->filterFecthAllByColumn($data);
        $colmunsNumber = count($columns);
        $rowsNumber    = count($columns[key($columns)]);
        $columnsName   = array();
        $maxLength     = 0;

        foreach ($columns as $key => $value) {
            $columnsName[] = $key;
            $this->setMaxSize($key, $columns[$key], strlen($key));
            $maxLength += ($this->getMaxSize($key) + 3); // 2 because 2 spaces and 1 | are added between name
        }

        $maxLength      -= 1; // don't touch it's magic ;p
        $separationLine = '+' . str_pad('', $maxLength, '-', STR_PAD_BOTH) . '+' . PHP_EOL;
        $prettyString   = $separationLine;
        $prettyString   .= '|' . str_pad($tableName, $maxLength, ' ', STR_PAD_BOTH) . '|' . PHP_EOL ;
        $prettyString   .= $separationLine;

        for ($i = 0; $i < $colmunsNumber; $i++) {
            $prettyString .= '| ' . $this->smartAlign($columnsName[$i], $columnsName[$i], 0, STR_PAD_BOTH) . ' ';
        }
        
        $prettyString .= '|' . PHP_EOL . $separationLine;

        for ($i = 0; $i < $rowsNumber; $i++) {
            for ($j = 0; $j < $colmunsNumber; $j++) {
                $prettyString .= '| ' .
                    $this->smartAlign($columns[$columnsName[$j]][$i], $columnsName[$j]) . ' ';
            }

            $prettyString .= '|' . PHP_EOL;
        }

        return $prettyString . $separationLine;
    }
    
    /*-----  End of Private methods  ------*/
}
