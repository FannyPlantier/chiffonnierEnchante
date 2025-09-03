<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'chiffonnierwp' );

/** Database username */
define( 'DB_USER', 'chiffonnierWpUser' );

/** Database password */
define( 'DB_PASSWORD', '!WPp@ssw0rd' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '!rzfTSROMA_W1QxVCAx?|M}hIDzcY(:uL^nDXopIpLhMFlOo#g^G%[Xa G 4x|oj');
define('SECURE_AUTH_KEY',  '1t+|N:!?QI;`-]:?@d5-,#:t)kzi*P$f?f_B@[+t/1dPVtp?<}x uBD8Z77p5iJU');
define('LOGGED_IN_KEY',    'W%Y%~`rE~j(0DPs?baz.e>gJ&7Tfmbw>pu+D#&:u~AmyL9[_0Nhw/I/q!.c4XeCN');
define('NONCE_KEY',        'p9JUE7u2Yp09J0_Sg8;.HaCWu.nr5wm*3r+>RyW~S9N[X5Vj-mJ/MlPj);P=+ T8');
define('AUTH_SALT',        'R?[!Ful|KQiH-z>]%ZQDb`wwKVI~/|&L.D+zTxsV6|x+M-Wc-S,-;+Xz<@_t[@#1');
define('SECURE_AUTH_SALT', ',)d39i.:OyB*)m}__w]th|zWH}#ef%P&=hhU;4Cn>swAEKO(M4I+#5g2/n%UBeU/');
define('LOGGED_IN_SALT',   '2niqwl`gPbK=|=VdB$NaMq#LYx01(r_LqD{=$Ag=I-U(h_:#(1PyEOg=)|PJayT!');
define('NONCE_SALT',       '!h6v=dKuQn+G5]<U.q6~-kvWz1x[PSr-qLaXhizezH?zu>mFi/{Vu+%/|dW2K=%[');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* helloAsso-manager authentication */
define('HELLOASSO_CLIENT_ID', '740ca585ed364ad5b943a3f7f191b5df');
define('HELLOASSO_CLIENT_SECRET', 'XzexIQhYaMuug4lANtVDPSurK7FkMXE4');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
