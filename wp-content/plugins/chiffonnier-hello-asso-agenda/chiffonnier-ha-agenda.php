<?php
/*
Plugin Name: HelloAsso Agenda API
Description: Récupère et affiche les événements HelloAsso via l'API.
Version: 1.0
Author: Fanny Plantier
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------
// 1. GESTION DE L'AUTHENTIFICATION (ACCESS TOKEN)
// -----------------------------------------------------------

/**
 * Gère l'obtention et le renouvellement de l'Access Token HelloAsso.
 * Le jeton est stocké temporairement (transient) pour éviter une requête à chaque chargement de page.
 * 
 * @return string|false L'Access Token ou false en cas d'échec.
 */
function ha_get_access_token() {
    // Vérification de l'existence des constantes de sécurité (obligatoire)
    if ( ! defined('HA_CLIENT_ID') || ! defined('HA_CLIENT_SECRET') || ! defined('HA_TOKEN_URL') ) {
        error_log( 'Erreur de configuration API : Les identifiants ne sont pas définis.' );
        return false;
    }
    
    // 1. Essayer de récupérer le jeton depuis le cache (transient)
    $token = get_transient( 'helloasso_access_token' );
    
    if ( $token ) {
        return $token; // Le jeton est toujours valide, on le retourne
    }

    // 2. Le jeton n'est pas dans le cache ou a expiré, on le regénère
    // Création de l'en-tête Basic Auth (Client ID:Client Secret encodé en Base64)
    $auth_string = base64_encode( HA_CLIENT_ID . ':' . HA_CLIENT_SECRET );

   
    $response = wp_remote_post( HA_TOKEN_URL, array(
        'headers' => array(
            'Authorization' => 'Basic ' . $auth_string,
        ),
        'body' => array('grant_type' => 'client_credentials'),
        'timeout' => 15,
    ));

    // 3. Vérification de la réponse
    error_log('HelloAsso token response: ' . print_r($response, true));

    if ( is_wp_error( $response ) ) {
        error_log('Erreur WP : ' . $response->get_error_message());
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($code !== 200) {
        error_log("Erreur HelloAsso: HTTP $code. Réponse brute: $body");
        return false;
    }

    if (isset($data['access_token'])) {
        $token = $data['access_token'];
        $expires_in = isset($data['expires_in']) ? (int)$data['expires_in'] : HOUR_IN_SECONDS;
        set_transient('helloasso_access_token', $token, $expires_in - 60);
        return $token;
    }

    error_log('Erreur HelloAsso : pas de access_token dans la réponse.');
    return false;
}



// -----------------------------------------------------------
// 2. RÉCUPÉRATION DES DONNÉES D'ÉVÉNEMENTS
// -----------------------------------------------------------

/**
 * Récupère les événements HelloAsso de l'API. Utilise un cache de 1 heure pour les données.
 * 
 * @return array Liste des événements ou tableau vide en cas d'échec.
 */
function ha_get_events_from_api() {
    // 1. Récupération des données en cache (transient de données d'événements)
    $events_data = get_transient( 'helloasso_events_cache' );

    // Si les données sont dans le cache, on les retourne immédiatement
    if ( false !== $events_data ) {
        return $events_data;
    }

    // 2. Obtenir le jeton d'accès pour l'authentification
    $access_token = ha_get_access_token();

    if ( ! $access_token ) {
        return array(); // Impossible d'obtenir le jeton
    }

    // 3. Vérifier et définir le slug de l'organisation
    if ( ! defined('HA_ORGANIZATION_SLUG') ) {
        error_log( 'Erreur de configuration API : Le slug de l\'organisation n\'est pas défini.' );
        return array();
    }
    
    $organization_slug = HA_ORGANIZATION_SLUG; 
    
    // Endpoint pour récupérer les événements 'Event'
    $pageSize = 50;
    $api_url = "https://api.helloasso.com/v5/organizations/{$organization_slug}/forms?states=Public&formTypes=Event&pageIndex=1&pageSize={$pageSize}";
    
    // 4. Lancement de l'appel API
    $response = wp_remote_get( $api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Accept'        => 'application/json',
        ),
        'timeout' => 30, // Temps d'attente maximum
    ) );

    // 5. Vérification et traitement de la réponse
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
        error_log( 'Erreur HelloAsso API lors de la récupération des événements.' );
        return array(); 
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    // On suppose que la liste des événements est dans la clé 'data'
    $events_list = isset($data['data']) && is_array($data['data']) ? $data['data'] : array();

    // 6. Mise en cache des nouvelles données pour 1 heure
    set_transient( 'helloasso_events_cache', $events_list, HOUR_IN_SECONDS );

    return $events_list;
}

// -----------------------------------------------------------
// 3. AFFICHAGE VIA SHORTCODE
// -----------------------------------------------------------

/**
 * Génère le HTML des événements et l'associe au shortcode.
 *
 * @return string Le code HTML à afficher.
 */
function ha_display_agenda_shortcode() {

    $events = ha_get_events_from_api();

    if ( empty( $events ) ) {
        return '<p class="ha-no-event">Aucun événement n\'est actuellement disponible.</p>';
    }

    $output = '<div class="helloasso-agenda-list">';

    foreach ( $events as $event ) {
        // Nettoyage et récupération des données clés
        $title       = isset($event['name']) ? esc_html( $event['name'] ) : 'Titre non défini';
        $description = isset($event['description']) ? wp_kses_post( $event['description'] ) : '';
        $url         = isset($event['url']) ? esc_url( $event['url'] ) : '#'; // Lien de réservation/paiement
        
        // Formatage de la date (à adapter selon le format précis dans l'API v5)
        $start_date  = isset( $event['startDate'] ) ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $event['startDate'] ) ) : 'Date non précisée';

        $output .= '<article class="helloasso-event">';
        $output .= '<h2>' . $title . '</h2>';
        $output .= '<p class="event-date">Date : <strong>' . $start_date . '</strong></p>';
        
        if ($description) {
            $output .= '<div class="event-description">' . $description . '</div>';
        }
        
        // Le lien vers la page HelloAsso pour l'achat/réservation
        $output .= '<p><a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="button ha-reserve-button">Réserver ma place et régler</a></p>';
        
        $output .= '</article>';
    }

    $output .= '</div>';

    return $output; 
}

// Enregistrement du shortcode : Utilisation : [helloasso_agenda]
add_shortcode( 'helloasso_agenda', 'ha_display_agenda_shortcode' );

