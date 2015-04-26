<?php

namespace utilities\classes\logger;

use \utilities\classes\logger\LogLevel as LogLevel;
use \utilities\classes\ini\IniManager as Ini;
use \utilities\interfaces\LoggerInterface as LoggerInterface;

/**
* FileLogger
*/
class FileLogger implements LoggerInterface
{
    private $filePath;

    public function __construct($filePath = null)
    {
        if ($filePath !== null && is_string($filePath)) {
            $this->filePath = $filePath;
        } else {
            $this->filePath = Ini::getParamFromSection('FileLogger', 'filePath');
        }
    }

    /**
     * Le système est inutilisable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Des mesures doivent être prises immédiatement.
     *
     * Exemple: Tout le site est hors service, la base de données est
     * indisponible, etc. Cela devrait déclencher des alertes par SMS et vous
     * réveiller.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Conditions critiques.
     *
     * Exemple: Composant d'application indisponible, exception inattendue.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Erreurs d'exécution qui ne nécessitent pas une action immédiate mais doit
     * normalement être journalisée et contrôlée.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Événements exceptionnels qui ne sont pas des erreurs.
     *
     * Exemple: Utilisation des API obsolètes, mauvaise utilisation d'une API,
     * indésirables élements qui ne sont pas nécessairement mauvais.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Événements normaux mais significatifs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Événements intéressants.
     *
     * Exemple: Connexion utilisateur, journaux SQL.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Informations détaillées de débogage.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    /**
     * Logs avec un niveau arbitraire.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    private function writeInFile($message, $context)
    {
        $string = date('Y-m-d H:i:s')
            . "\t\t"
            . $message
            . "\n";

        file_put_contents($this->filePath, $string, FILE_APPEND);
    }
}
