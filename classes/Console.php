<?php

namespace utilities\classes;

use \utilities\classes\DataBase as DB;

/**
* @class Console
*/
class Console
{
    use \utilities\traits\BeautifullIndentTrait;

    private static $COMMANDS = array(
        'exit'     => 'Exit the ORM console',
        'last cmd' => 'Get the last command written',
        'all cmd'  => 'Get all the commands written',
        'tables'   => 'Get all the tables name',
        'help'     => 'Display all the commands'
    );

    private $commandsHistoric = array();

    public function __construct()
    {
    }

    public function launchConsole()
    {
        echo PHP_EOL . 'Welcome to the ORM in console' . PHP_EOL . PHP_EOL;
        $this->processCommand($this->userInput());
    }

    private function userInput()
    {
        echo 'cmd: ';

        $handle = fopen('php://stdin', 'r');
       
        return trim(fgets($handle));
    }

    private function processCommand($command)
    {
        $exit = false;

        echo PHP_EOL;

        switch ($command) {
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

    public function tablePrettyPrint($table)
    {
        return PHP_EOL . '- ' . implode(PHP_EOL . '- ', $table);
    }

    public function tableAssociativPrettyPrint($table, $category)
    {
        $this->setMaxSize($category, array_keys($table));

        $string = '';

        foreach ($table as $key => $value) {
            $string .= $this->smartAlign($key, $category) . ' : ' . $value . PHP_EOL;
        }

        return PHP_EOL . $string;
    }
}
