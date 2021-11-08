<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hmkshopc_wp247' );

/** MySQL database username */
define( 'DB_USER', 'hmkshopc_wp247' );

/** MySQL database password */
define( 'DB_PASSWORD', '1652p5SE-[' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '0a4wcgyi7oedv4jaidgpiwrue17zbdavvieoegmfhypqqahh6xefalp3xzpwxrwp' );
define( 'SECURE_AUTH_KEY',  'ku9x5drbxaj9u32cvl9izaceuamjuqu9sdtbg2gkosfftqafvsk2dfxgw4ovj3nt' );
define( 'LOGGED_IN_KEY',    'qzz4a0lqkxqwdzd1lnpxpx9k3pgc08yuxz9jil1141adzhpegbhberrca0iimooh' );
define( 'NONCE_KEY',        'bm3jm4teombblombrcct6lmegnb22sl9oi9uron4qj6jbomimsgojzlgeb1cmcrj' );
define( 'AUTH_SALT',        '1sbs7coanxf9affpklsrk0qnh47s7jnzq7wdhynddmmirgpxvlkhnnmjj4eloj0j' );
define( 'SECURE_AUTH_SALT', 'mqvl2zsoywxaat0xbwbedi87v5hai8hygiyncjacr2qmzbljrvglfje82dobdf5q' );
define( 'LOGGED_IN_SALT',   'bmihcdgoskvdeo18w6pgnyocq3riheusdlzhgeo7acofdxf2fd0vyvhixikmdumj' );
define( 'NONCE_SALT',       'd3sqnu10ohzfvxsw3vulflnj8p0ywnfkf1pr8psisiplwi81xfu7tfeouoz4qjxf' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp3p_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
