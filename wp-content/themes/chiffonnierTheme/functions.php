<?php
/* ================= chargement des styles parent et enfant ================= */
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        filemtime( get_template_directory() . '/style.css' ) // version = date de modification
    );

    // charger la feuille de style du thème enfant
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        filemtime( get_stylesheet_directory() . '/style.css' ) // version = date de modification
    );

    // charger le fichier JavaScript pour le menu mobile
    wp_enqueue_script(
        'custom-script',
        get_template_directory_uri() . '/script.js',
        array('jquery'),
        filemtime( get_template_directory() . '/script.js' ),
        true
    );

    // charger Font Awesome depuis CDN
    wp_enqueue_style(
        'fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        array(),
        '6.5.0'
    );
});

/* ================= déclaration de l'emplacement du menu primary ================= */
add_action( 'after_setup_theme', function() {
    register_nav_menu('primary', 'Menu principal');
});

/* ================= déclaration des polices d'écriture ================= */
function chiffonnier_enqueue_fonts_file() {

    // définir les polices et leurs fichiers
    $fonts = [
         //livvic
        'livvic-Regular' => [
            'weight' => 400,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Regular.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Regular.woff'
            ]
        ],
        'livvic-Bold' => [
            'weight' => 700,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Bold.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Bold.woff'
            ]
        ],
        'livvic-Italic' => [
            'weight' => 400,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Italic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Italic.woff'
            ]
        ],
        'livvic-Black' => [
            'weight' => 900,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Black.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Black.woff'
            ]
        ],
        'livvic-BlackItalic' => [
            'weight' => 900,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-BlackItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-BlackItalic.woff'
            ]
        ],
        'livvic-BoldItalic' => [
            'weight' => 700,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-BoldItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-BoldItalic.woff'
            ]
        ],
        'livvic-ExtraLight' => [
            'weight' => 200,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-ExtraLight.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-ExtraLight.woff'
            ]
        ],
        'livvic-ExtraLightItalic' => [
            'weight' => 200,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-ExtraLightItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-ExtraLightItalic.woff'
            ]
        ],
        'livvic-Light' => [
            'weight' => 300,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Light.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Light.woff'
            ]
        ],
        'livvic-LightItalic' => [
            'weight' => 300,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-LightItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-LightItalic.woff'
            ]
        ],
        'livvic-Medium' => [
            'weight' => 500,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Medium.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Medium.woff'
            ]
        ],
        'livvic-MediumItalic' => [
            'weight' => 500,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-MediumItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-MediumItalic.woff'
            ]
        ],
        'livvic-SemiBold' => [
            'weight' => 600,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-SemiBold.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-SemiBold.woff'
            ]
        ],
        'livvic-SemiBoldItalic' => [
            'weight' => 600,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-SemiBoldItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-SemiBoldItalic.woff'
            ]
        ],
        'livvic-Thin' => [
            'weight' => 100,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-Thin.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-Thin.woff'
            ]
        ],
        'livvic-ThinItalic' => [
            'weight' => 100,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/livvic/livvic-woff2/Livvic-ThinItalic.woff2',
                'woff'  => 'assets/fonts/livvic/livvic-woff/Livvic-ThinItalic.woff'
            ]
        ],

        // arimo
        'arimo-Bold' => [
            'weight' => 700,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-Bold.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-Bold.woff'
            ]
        ],
        'arimo-BoldItalic' => [
            'weight' => 700,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-BoldItalic.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-BoldItalic.woff'
            ]
        ],
        'arimo-Italic' => [
            'weight' => 400,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-Italic.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-Italic.woff'
            ]
        ],
        'arimo-Medium' => [
            'weight' => 500,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-Medium.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-Medium.woff'
            ]
        ],
        'arimo-MediumItalic' => [
            'weight' => 500,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-MediumItalic.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-MediumItalic.woff'
            ]
        ],
        'arimo-Regular' => [
            'weight' => 400,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-Regular.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-Regular.woff'
            ]
        ],
        'arimo-SemiBold' => [
            'weight' => 600,
            'style'  => 'normal',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-SemiBold.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-SemiBold.woff'
            ]
        ],
        'arimo-SemiBoldItalic' => [
            'weight' => 600,
            'style'  => 'italic',
            'files'  => [
                'woff2' => 'assets/fonts/arimo/arimo-woff2/Arimo-SemiBoldItalic.woff2',
                'woff'  => 'assets/fonts/arimo/arimo-woff/Arimo-SemiBoldItalic.woff'
            ]
        ]
    ];

    // générer le CSS pour chaque police
    $css = "";
    foreach ($fonts as $name => $data) {
        $src_parts = [];
        foreach ($data['files'] as $format => $file) {
            $src_parts[] = "url('" . get_stylesheet_directory_uri() . '/' . $file . "') format('{$format}')";
        }
        $src = implode(",\n       ", $src_parts);

        $css .= "@font-face {
    font-family: '{$name}';
    src: {$src};
    font-weight: {$data['weight']};
    font-style: {$data['style']};
}\n";
    }

    // chemin du fichier CSS à générer
    $file_path = get_stylesheet_directory() . '/fonts.css';
    
    // écrire le CSS dans fonts.css
    file_put_contents($file_path, $css);

    // URL pour enqueue
    $fonts_css_url = get_stylesheet_directory_uri() . '/fonts.css';
    
    // enqueue le CSS généré
    wp_enqueue_style('chiffonnier-fonts', $fonts_css_url, [], null);
}
add_action('wp_enqueue_scripts', 'chiffonnier_enqueue_fonts_file');

/* ================= page mentions légales ================= */
    // insertion image de fond pour la page mentions légales
add_action('wp_enqueue_scripts', function() {
if (is_page(491)) {
    $bg_url = get_stylesheet_directory_uri() . '/assets/images/lignes_verticales.svg';
    wp_add_inline_style(
        'child-style',
        ".page-id-491 { 
            background-image: url('{$bg_url}'); 
            background-size: 10rem auto; 
            background-repeat: repeat-y; 
            background-position: 10rem 5rem; 
        }"
    );
}
});

/* ================= page contact ================= */
    // insertion image de fond pour la page contact
add_action('wp_enqueue_scripts', function() {
if (is_page(73)) {
    $bg_url = get_stylesheet_directory_uri() . '/assets/images/contact.png';
    wp_add_inline_style(
        'child-style',
        ".page-id-73 { 
            background-image: url('{$bg_url}'); 
            background-size: 35rem auto; 
            background-repeat: no-repeat; 
            background-position: calc(100% - 2rem) 15rem;
        }"
    );
}
});

/* ================= page conférences ================= */
    // insertion image de fond pour la page conférences
add_action('wp_enqueue_scripts', function() {
if (is_page(71)) {
    $bg_url = get_stylesheet_directory_uri() . '/assets/images/courbe.svg';
    wp_add_inline_style(
        'child-style',
        ".page-id-71 { 
            background-image: url('{$bg_url}'); 
            background-repeat: repeat-y; 
            background-size: auto 21rem;
            background-position: calc(100% - 2rem) 5rem;
        }"
    );
}
});

/* ================= page ateliers tissage ================= */
    // insertion image de fond pour la page ateliers tissage
add_action('wp_enqueue_scripts', function() {
    if (is_page(63)) {
        $bg_top = get_stylesheet_directory_uri() . '/assets/images/coin.svg';
        $bg_bottom = get_stylesheet_directory_uri() . '/assets/images/coin-bas.svg';
        wp_add_inline_style(
            'child-style',
            ".page-id-63 { 
                background-image: url('{$bg_top}'), url('{$bg_bottom}');
                background-size: 15rem auto, 12rem auto;
                background-repeat: no-repeat, no-repeat;
                background-position: right top 7rem, left bottom 2.9rem;
            }"
        );
    }
});

    // puce personnalisée
    add_action('wp_enqueue_scripts', function() {
    $puce_url = get_stylesheet_directory_uri() . '/assets/images/puce.svg';
    $css = "
        .scolaire-photo-g-liste ul li::before,
        .scolaire-photo-d-liste ul li::before,
        .scolaire-photo-d-liste2 ul li::before {
            content: '';
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            background-image: url('{$puce_url}');
            background-size: contain;
            background-repeat: no-repeat;
            vertical-align: middle;
        }
        .scolaire-photo-g-liste ul,
        .scolaire-photo-d-liste ul,
        .scolaire-photo-d-liste2 ul {
            list-style: none; /* on enlève les puces par défaut */
            padding-left: 0;
        }
    ";
    wp_add_inline_style('child-style', $css);
});

/* ================= page troc ================= */
    // insertion image de fond pour la page troc
add_action('wp_enqueue_scripts', function() {
    if (is_page(69)) {
        $bg_top = get_stylesheet_directory_uri() . '/assets/images/boucle-haut.svg';
        $bg_bottom = get_stylesheet_directory_uri() . '/assets/images/boucle-bas.svg';
        wp_add_inline_style(
            'child-style',
            ".page-id-69 { 
                background-image: url('{$bg_top}'), url('{$bg_bottom}');
                background-size: auto 20rem, auto 20rem;
                background-repeat: no-repeat, no-repeat;
                background-position: left top 7rem, right bottom 2.9rem;
            }"
        );
    }
});