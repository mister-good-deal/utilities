<?php

namespace utilities\classes;

use \utilities\classes\LogLevel as LogLevel;
use \utilities\interfaces\LoggerInterface as LoggerInterface;

/**
* ConsoleLogger class
*/
class ConsoleLogger implements LoggerInterface
{
    public static $LEVELS = array(
        LogLevel::EMERGENCY => 'emergency',
        LogLevel::ALERT     => 'alert',
        LogLevel::CRITICAL  => 'critical',
        LogLevel::ERROR     => 'error',
        LogLevel::WARNING   => 'warning',
        LogLevel::NOTICE    => 'notice',
        LogLevel::INFO      => 'info',
        LogLevel::DEBUG     => 'debug'
    );

    private $colors;
    
    public function __construct()
    {
        $this->colors = new ConsoleColors();
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'white', 'red');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'light_gray', 'red');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'red', 'black');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'light_red', 'black');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'yellow', 'red');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'light_gray', 'black');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'light_green', 'black');
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
        echo $this->colors->getColoredString($message . $this->formatContext($context), 'cyan', 'black');
    }

    /**
     * Logs avec un niveau arbitraire.
     *
     * @param int $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (in_array($level, array_keys(self::$LEVELS))) {
            call_user_func(__CLASS__ . '::' . self::$LEVELS[$level], $message, $context);
        } else {
            $this->info($message, $context);
        }
    }

    private function formatContext($contexts)
    {
        $string = '';

        foreach ($contexts as $num => $context) {
            if (is_array($context)) {
                $string .= "\nContext(" . ($num + 1) . ")\n";

                if (isset($context['class'])) {
                    $string .= "\tin class:\t" . $context['class'] . "\n";
                }

                if (isset($context['function'])) {
                    $string .= "\tin function:\t" . $context['function'] . "\n";
                }

                if (isset($context['file'])) {
                    $string .= "\tin file:\t" . $context['file'] . "\n";
                }

                if (isset($context['line'])) {
                    $string .= "\tat line:\t" . $context['line'] . "\n";
                }

                // TODO implement args display
                // if (isset($context['args'])){
                //     $string .= "\twith args:\t" . $context['args'] . "\n";
                // }
            }
        }

        return $string;
    }
}
