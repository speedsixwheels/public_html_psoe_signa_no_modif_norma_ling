<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u937561055_S45Gn' );

/** Database username */
define( 'DB_USER', 'u937561055_V69FX' );

/** Database password */
define( 'DB_PASSWORD', 'IRaIkU2uJI' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          '@pE30MqaLj<s))4In/E{lm7/Y%/SZEd/U)n:2z0sZ{5~:csm;jO5b=<h>nS{z^}2' );
define( 'SECURE_AUTH_KEY',   'sjHl&+UfxTD]S_Sb&nh5?$|SG8PEgLMC8z~5x$tM)imKN!#V}PaP8c0m72wOrnrg' );
define( 'LOGGED_IN_KEY',     'Bl%O8,}Q|t_#~pm8{Q`u&^]o+.}z8sF62f(2>iKm:gzR20p5ea30(.}DUP2:]X5B' );
define( 'NONCE_KEY',         '{=!)Oo(Ve;fK!qYvawy]t|bE*?ph_RCGZ=n.i</Xyv>Pxi7>iL/fm}5}<r>2Sbao' );
define( 'AUTH_SALT',         'jX_PEFzX?(hYXW8H{SdN*DY?p@L3|!ui$t.O:B4KJPcX /66|IS8}0SXi@]J+?&~' );
define( 'SECURE_AUTH_SALT',  '}a]!7NGhJYFdZt%]Rzh2NHx q]8=]huwVGpVR;J.Rg5L)8#3M**BUh<=|^oAe;Nw' );
define( 'LOGGED_IN_SALT',    'Lc];9 OoVikR> XfyZ(Y<MN6tb^`8zl=O`bj.>gS7xDkxlh&L>9XlA{_nxzx.9l0' );
define( 'NONCE_SALT',        '4S&:$@$sd|n_#3PgI?3L.0]>K3)r|G3FUOd#V7jW$`yw{?jS?x<y)zBpQX7Az.i>' );
define( 'WP_CACHE_KEY_SALT', 'M=i.q.Lz0Dy&$sSNt{ vo+~CJ^gQ57:UeCD=0LOZ$qSriBNz|vSbxB 39jGABF-g' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '6e2eb7fc3584f94fcd6853163476e634' );
define( 'WP_AUTO_UPDATE_CORE', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
