<?php

class HelloAsso_API {

    const TOKEN_OPTION_KEY = 'helloasso_api_token_data';
    const API_URL = 'https://api.helloasso.com';

    public function __construct() {
        add_shortcode( 'ha_events', array( $this, 'display_events_shortcode' ) );
    }

    /**
     * Obtient un access_token valide (et le rafraîchit si nécessaire).
     * @return string|false L'access token valide ou false en cas d'échec.
     */
    private function get_valid_access_token() {
        $token_data = get_option( self::TOKEN_OPTION_KEY, array() );
        $current_time = time();

        // 1. Vérifie si un token valide existe déjà (avec une marge de 60s)
        if ( isset( $token_data['access_token'] ) && isset( $token_data['expires_at'] ) && $token_data['expires_at'] > ($current_time + 60) ) {
            return $token_data['access_token'];
        }

        // 2. Tente de rafraîchir le token si un refresh_token est disponible
        if ( isset( $token_data['refresh_token'] ) ) {
            $new_token_data = $this->refresh_access_token( $token_data['refresh_token'] );
            if ( $new_token_data ) {
                return $new_token_data['access_token'];
            }
        }

        // 3. Demande un nouveau token si aucun n'est valide ou rafraîchissable
        $new_token_data = $this->request_new_access_token();
        if ( $new_token_data ) {
            return $new_token_data['access_token'];
        }

        return false; // Échec total
    }


    /**
     * Demande le token initial (Client Credentials Grant Type).
     * @return array|false Les données du token ou false en cas d'échec.
     */
    private function request_new_access_token() {
        $response = wp_remote_post( self::API_URL . '/oauth2/token', array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => array(
                'grant_type'    => 'client_credentials',
                'client_id'     => HA_CLIENT_ID, 
                'client_secret' => HA_CLIENT_SECRET,
            ),
        ) );

        return $this->handle_token_response( $response );
    }

    /**
     * Rafraîchit l'access token.
     * @param string $refresh_token Le refresh token actuel.
     * @return array|false Les nouvelles données du token ou false.
     */
    private function refresh_access_token( $refresh_token ) {
        $response = wp_remote_post( self::API_URL . '/oauth2/token', array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => array(
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refresh_token,
            ),
        ) );

        return $this->handle_token_response( $response );
    }

    /**
     * Gère la réponse de l'API OAuth2 et stocke les données.
     * @param array|WP_Error $response La réponse de wp_remote_post.
     * @return array|false Les données du token formatées ou false.
     */
    private function handle_token_response( $response ) {
        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! empty( $data['access_token'] ) ) {
            // Calcule l'heure d'expiration (en secondes Unix)
            $data['expires_at'] = time() + (int) $data['expires_in'];
            update_option( self::TOKEN_OPTION_KEY, $data );
            return $data;
        }

        return false;
    }
    
    /**
     * Récupère tous les événements publics de l'organisation.
     * @return array|false La liste des formulaires ou false.
     */
    public function get_public_events() {
        $token = $this->get_valid_access_token();

        if ( ! $token ) {
            return false; // Pas de token
        }

        $url = self::API_URL . '/v5/organizations/' . HA_ORGANIZATION_SLUG . '/forms';
        
        // Paramètres de la requête : États Publics et Type Événement
        $args = array(
            'states'    => 'Public', 
            'formTypes' => 'Event', 
        );
        
        // Construction de l'URL avec les Query Params
        $query_url = add_query_arg( $args, $url );

        $response = wp_remote_get( $query_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ),
        ) );

        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        return isset( $data['data'] ) ? $data['data'] : array();
    }

    /**
     * Fonction du Shortcode [ha_events]
     * Affiche les événements sous forme de liste avec widget intégré.
     */
    public function display_events_shortcode( $atts ) {
        $events = $this->get_public_events();

        if ( empty( $events ) || $events === false ) {
            return '<p>Aucun événement public trouvé actuellement.</p>';
        }

        ob_start(); // Démarre la capture de sortie HTML
        
        ?>
        <div class="helloasso-event-container">
            <?php foreach ( $events as $event ) : 
                $title = esc_html( $event['title'] );
                $description = esc_html( $event['description'] );
                $widget_url = esc_url( $event['widgetFullUrl'] ); 
            ?>
                <div class="helloasso-event-item" style="border: 1px solid #eee; padding: 15px; margin-bottom: 20px;">
                    <h2><?php echo $title; ?></h2>
                    <p class="description"><?php echo $description; ?></p>
                    
                    <?php if ( $widget_url ) : ?>
                        <div class="ha-widget-wrapper">
                            <!-- Intégration du widget de réservation et de paiement -->
                            <iframe 
                                src="<?php echo $widget_url; ?>" 
                                style="width: 100%; min-height: 700px; border: none;" 
                                loading="lazy" 
                                title="<?php echo $title; ?> - Réservation HelloAsso">
                            </iframe>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php

        return ob_get_clean(); // Retourne le contenu capturé
    }
    
    // ====================================================================================
    // |                                  MÉTHODES DE DÉBOGAGE (DEBUG)                    |
    // ====================================================================================

    /**
     * **[DEBUG]** Affiche l'état actuel du token et tente d'en obtenir un nouveau.
     */
    public function debug_token_status() {
        echo '<h2>HelloAsso Token Debug</h2>';
        
        $token_data = get_option( self::TOKEN_OPTION_KEY, array() );
        $current_time = time();
        $token = $this->get_valid_access_token();
        
        // Afficher les données stockées
        echo '<h3>1. Données du Token Stockées (wp_options)</h3>';
        echo '<pre>' . print_r( $token_data, true ) . '</pre>';

        if ( $token ) {
            echo '<p style="color: green;">✅ **Access Token Valide Obtenu :** ' . substr($token, 0, 15) . '... (longueur: ' . strlen($token) . ')</p>';
        } else {
            echo '<p style="color: red;">❌ **Échec de l\'obtention de l\'Access Token.**</p>';
        }
        
        // Vérifier l'expiration (basé sur les données stockées si elles existent)
        if ( isset( $token_data['expires_at'] ) ) {
            $expires_in = $token_data['expires_at'] - $current_time;
            echo '<p>Expiration dans : ' . $expires_in . ' secondes.</p>';
        } else {
             echo '<p>Pas d\'information d\'expiration stockée.</p>';
        }
    }

    /**
     * **[DEBUG]** Affiche la réponse brute de l'API des événements.
     */
    public function debug_event_request() {
        echo '<h2>HelloAsso Events Request Debug</h2>';

        $token = $this->get_valid_access_token();

        if ( ! $token ) {
            echo '<p style="color: red;">❌ **Impossible de tester : Token d\'accès manquant ou invalide.** Veuillez essayer <a href="' . admin_url('index.php?ha_debug_token=true') . '">debug_token_status</a> d\'abord.</p>';
            return;
        }

        $url = self::API_URL . '/v5/organizations/' . HA_ORGANIZATION_SLUG . '/forms';
        
        $args = array(
            'states'    => 'Public', 
            'formTypes' => 'Event', 
        );
        
        $query_url = add_query_arg( $args, $url );
        
        echo '<h3>URL Requête API :</h3>';
        echo '<p>' . esc_url( $query_url ) . '</p>';
        echo '<h3>Headers d\'Authentification :</h3>';
        echo '<pre>Authorization: Bearer ' . substr($token, 0, 15) . '...</pre>';

        $response = wp_remote_get( $query_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ),
        ) );

        echo '<h3>Réponse Brute (wp_remote_get) :</h3>';
        echo '<pre>' . print_r( $response, true ) . '</pre>';
        
        if ( is_wp_error( $response ) ) {
            echo '<p style="color: red;">❌ **Erreur WP :** ' . $response->get_error_message() . '</p>';
            return;
        }
        
        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        echo '<h3>Code de Réponse HTTP :</h3>';
        echo ($code === 200 ? '<p style="color: green;">' : '<p style="color: red;">') . $code . '</p>';

        if ($code === 200) {
            echo '<h3>Résultats (Propriété data) :</h3>';
            if ( isset( $data['data'] ) && ! empty( $data['data'] ) ) {
                echo '<p style="color: green;">✅ **' . count($data['data']) . ' événements trouvés !**</p>';
                echo '<pre>' . print_r( $data['data'][0]['title'] ?? $data['data'][0] , true ) . '</pre>'; // Affiche le premier événement
            } else {
                echo '<p style="color: orange;">⚠️ **Code 200 OK, mais aucun événement n\'est retourné dans le tableau "data".** Vérifiez le slug ou les états/types de formulaires.</p>';
            }
        } else {
            echo '<h3>Corps de la Réponse (Erreur) :</h3>';
            echo '<pre>' . esc_html($body) . '</pre>';
            
            if ($code === 401) {
                echo '<p style="color: red;">**Erreur 401 Unauthorized :** Le token est invalide, vérifier les identifiants Client ID/Secret.</p>';
            } elseif ($code === 403) {
                 echo '<p style="color: red;">**Erreur 403 Forbidden :** La clé API n\'a pas les privilèges (AccessPublicData) ou le token a expiré.</p>';
            }
        }
    }
}