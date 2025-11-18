<?php
/**
 * Plugin Name: HelloAsso Events
 * Description: Affiche les événements publics de HelloAsso en utilisant l'API.
 * Version: 1.0
 * Author: Votre Nom
 */

// Empêche l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Déclaration de la variable globale pour l'instance de la classe API
global $chiffonnier_ha_api_instance; 

// --- Fichiers du plugin ---
require_once plugin_dir_path( __FILE__ ) . 'includes/class-helloasso-api.php';

// Initialisation du plugin
function ha_events_init() {
    global $chiffonnier_ha_api_instance; // Déclare que nous allons utiliser la variable globale

    // Vérification de la présence des constantes de configuration
    if ( ! defined( 'HA_CLIENT_ID' ) || ! defined( 'HA_CLIENT_SECRET' ) || ! defined( 'HA_ORGANIZATION_SLUG' ) ) {
        // Optionnel : Afficher une notification d'erreur aux administrateurs si les constantes sont manquantes
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>HelloAsso Events Plugin :</strong> Les constantes <code>HA_CLIENT_ID</code>, <code>HA_CLIENT_SECRET</code> et <code>HA_ORGANIZATION_SLUG</code> doivent être définies dans votre fichier <code>wp-config.php</code>.</p></div>';
            } );
        }
        return;
    }
    
    // Instanciation de la classe et stockage dans la variable globale
    $chiffonnier_ha_api_instance = new HelloAsso_API();

        // 💡 DÉBOGAGE TEMPORAIRE
        if ( current_user_can( 'manage_options' ) && isset( $_GET['ha_debug'] ) ) {
            // Nous utilisons la variable globale ici
            add_action( 'admin_init', array( $chiffonnier_ha_api_instance, 'debug_event_request' ) ); 
        }
        // 💡 FIN DE L'AJOUT TEMPORAIRE
}
add_action( 'plugins_loaded', 'ha_events_init' );