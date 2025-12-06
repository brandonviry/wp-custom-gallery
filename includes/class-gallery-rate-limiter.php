<?php
/**
 * Classe pour gérer le rate limiting des actions sensibles
 * Protection contre le déni de service (DoS) et abus
 */
class WP_Gallery_Rate_Limiter {

    /**
     * Générer la clé de transient unique par action et utilisateur
     */
    private static function get_transient_key($action, $user_id) {
        return 'gallery_rate_limit_' . $action . '_' . $user_id;
    }

    /**
     * Vérifier si l'utilisateur a dépassé la limite de requêtes
     *
     * @param string $action Nom de l'action (save_gallery, delete_gallery, etc.)
     * @param int $max_attempts Nombre maximum de tentatives autorisées
     * @param int $time_window Fenêtre de temps en secondes
     * @return bool True si autorisé, False si limite dépassée
     */
    public static function check_rate_limit($action, $max_attempts = 20, $time_window = 60) {
        $user_id = get_current_user_id();

        // Si l'utilisateur n'est pas connecté, bloquer directement
        if (!$user_id) {
            return false;
        }

        $transient_key = self::get_transient_key($action, $user_id);
        $attempts = get_transient($transient_key);

        // Première tentative : créer le compteur
        if ($attempts === false) {
            set_transient($transient_key, 1, $time_window);
            return true;
        }

        // Limite dépassée
        if ($attempts >= $max_attempts) {
            return false;
        }

        // Incrémenter le compteur
        set_transient($transient_key, $attempts + 1, $time_window);
        return true;
    }

    /**
     * Réinitialiser le compteur pour une action spécifique
     * Utile après une action réussie critique
     */
    public static function reset_limit($action) {
        $user_id = get_current_user_id();
        if (!$user_id) return;

        $transient_key = self::get_transient_key($action, $user_id);
        delete_transient($transient_key);
    }

    /**
     * Obtenir le temps restant avant que la limite soit réinitialisée
     *
     * @param string $action Nom de l'action
     * @return int Nombre de secondes restantes, ou 0 si pas de limite active
     */
    public static function get_time_remaining($action) {
        $user_id = get_current_user_id();
        if (!$user_id) return 0;

        $transient_key = self::get_transient_key($action, $user_id);
        $timeout = get_option('_transient_timeout_' . $transient_key);

        if ($timeout === false) {
            return 0;
        }

        $remaining = $timeout - time();
        return max(0, $remaining);
    }
}
