<?php

namespace utilities\interfaces;

/**
 * Décrit une instance logger
 *
 * Le message DOIT être une chaîne ou un objet qui implémente __ toString ().
 *
 * Le message PEUT contenir des marqueurs à la forme: {foo} où foo
 * sera remplacé par les données de contexte à clé "foo".
 *
 * Le tableau de contexte peut contenir des données arbitraires, la seule
 * hypothèse qui peut être faite par des réalisateurs, c'est que si une instance
 * de Exception est donné pour produire une trace de la pile, il DOIT être dans
 * une clé nommée "exception".
 *
 * Voir https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * pour la spécification d'interface complète.
 */
interface LoggerInterface
{
    /**
     * Le système est inutilisable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array());

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
    public function alert($message, array $context = array());

    /**
     * Conditions critiques.
     *
     * Exemple: Composant d'application indisponible, exception inattendue.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array());

    /**
     * Erreurs d'exécution qui ne nécessitent pas une action immédiate mais doit
     * normalement être journalisée et contrôlée.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array());

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
    public function warning($message, array $context = array());

    /**
     * Événements normaux mais significatifs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array());

    /**
     * Événements intéressants.
     *
     * Exemple: Connexion utilisateur, journaux SQL.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array());

    /**
     * Informations détaillées de débogage.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array());

    /**
     * Logs avec un niveau arbitraire.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array());
}
