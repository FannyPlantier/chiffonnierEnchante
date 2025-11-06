<?php
/*
Plugin Name: HelloAsso Agenda API
Description: Récupère et affiche les événements HelloAsso via l'API avec debug intégré.
Version: 1.0
Author: Fanny Plantier
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------
// 1. GESTION DE L'AUTHENTIFICATION (ACCESS TOKEN)
// -----------------------------------------------------------
function ha_get_access_token() {
    if ( ! defined('HA_CLIENT_ID') || ! defined('HA_CLIENT_SECRET') || ! defined('HA_TOKEN_URL') ) {
        error_log('Erreur configuration API : HA_CLIENT_ID / HA_CLIENT_SECRET / HA_TOKEN_URL manquants');
        return false;
    }

    $token = get_transient('helloasso_access_token');
    if ($token) {
        return $token;
    }

    $auth_string = base64_encode(HA_CLIENT_ID . ':' . HA_CLIENT_SECRET);

    $response = wp_remote_post(HA_TOKEN_URL, [
        'headers' => [
            'Authorization' => 'Basic ' . $auth_string,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'body' => ['grant_type' => 'client_credentials'],
        'timeout' => 15,
    ]);

    if ( is_wp_error($response) ) {
        error_log('Erreur WP Remote Post: ' . $response->get_error_message());
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    error_log('HelloAsso token response: ' . print_r($data, true));

    if ($code !== 200) {
        error_log("Erreur HelloAsso HTTP $code : $body");
        return false;
    }

    if (isset($data['access_token'])) {
        $expires_in = isset($data['expires_in']) ? (int)$data['expires_in'] : HOUR_IN_SECONDS;
        set_transient('helloasso_access_token', $data['access_token'], $expires_in - 60);
        return $data['access_token'];
    }

    error_log('Erreur HelloAsso : pas de access_token dans la réponse.');
    return false;
}

// -----------------------------------------------------------
// 2. RÉCUPÉRATION DES DONNÉES D'ÉVÉNEMENTS
// -----------------------------------------------------------
function ha_get_events_from_api() {
    $events_data = get_transient('helloasso_events_cache');
    if ($events_data !== false) {
        return $events_data;
    }

    $token = ha_get_access_token();
    if (!$token) {
        return [];
    }

    if (!defined('HA_ORGANIZATION_SLUG')) {
        error_log("Erreur configuration API : HA_ORGANIZATION_SLUG manquant");
        return [];
    }

    $slug = HA_ORGANIZATION_SLUG;
    $pageSize = 50;
    $api_url = "https://api.helloasso.com/v5/organizations/{$slug}/forms?states=Public&formTypes=Event&pageIndex=1&pageSize={$pageSize}";

    $response = wp_remote_get($api_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ],
        'timeout' => 30,
    ]);

    if ( is_wp_error($response) ) {
        error_log('Erreur WP Remote Get: ' . $response->get_error_message());
        return [];
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    error_log('HelloAsso events response: ' . print_r($data, true));

    if ($code !== 200) {
        error_log("Erreur HelloAsso API HTTP $code : $body");
        return [];
    }

    // Vérifie la structure JSON, s'adapte si c'est 'forms' ou 'data'
    if (isset($data['forms']) && is_array($data['forms'])) {
        $events = $data['forms'];
    } elseif (isset($data['data']) && is_array($data['data'])) {
        $events = $data['data'];
    } else {
        $events = [];
    }

    set_transient('helloasso_events_cache', $events, HOUR_IN_SECONDS);

    return $events;
}

// -----------------------------------------------------------
// 3. AFFICHAGE VIA SHORTCODE
// -----------------------------------------------------------
function ha_display_agenda_shortcode() {
    $token = ha_get_access_token();
    if (!$token) {
        return '<p style="color:red;">Impossible d\'obtenir le jeton d\'accès. Vérifie HA_CLIENT_ID / HA_CLIENT_SECRET.</p>';
    }

    $events = ha_get_events_from_api();

    if (empty($events)) {
        return '<p style="color:red;">Aucun événement récupéré. Vérifie le debug côté serveur.</p>';
    }

    $output = '<div class="helloasso-agenda-list">';

    foreach ($events as $event) {
        $title = isset($event['name']) ? esc_html($event['name']) : 'Titre non défini';
        $description = isset($event['description']) ? wp_kses_post($event['description']) : '';
        $url = isset($event['url']) ? esc_url($event['url']) : '#';
        $start_date = isset($event['startDate']) ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($event['startDate'])) : 'Date non précisée';

        $output .= '<article class="helloasso-event">';
        $output .= '<h2>' . $title . '</h2>';
        $output .= '<p class="event-date">Date : <strong>' . $start_date . '</strong></p>';
        if ($description) {
            $output .= '<div class="event-description">' . $description . '</div>';
        }
        $output .= '<p><a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="button ha-reserve-button">Réserver ma place et régler</a></p>';
        $output .= '</article>';
    }

    $output .= '</div>';

    return $output;
}

add_shortcode('helloasso_agenda', 'ha_display_agenda_shortcode');
