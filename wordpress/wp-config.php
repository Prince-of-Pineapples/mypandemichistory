<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
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
define( 'DB_NAME', 'websorac_CovidDB' );

/** MySQL database username */
define( 'DB_USER', 'websorac_sorabot' );

/** MySQL database password */
define( 'DB_PASSWORD', 'sorabot12' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '])2gjkI!*oZuh!~EY)C[;_R&_Y!fz[qM7+II9F9y%g/>Tcr2nYw&8pgVz+N;jF{1' );
define( 'SECURE_AUTH_KEY',  '@%rh}y|nJ8sHU])9r>F.sy6JlVvQAOCfiBw3v-(cWrp],eRpe3|/m)0OoJ=FV480' );
define( 'LOGGED_IN_KEY',    'b=rA$Lew!Vo1+J#C=yH$S]V?, P|HL 7#e>8p]Ng{$jtgY@Hsw_)x/Ra.x9UwXgE' );
define( 'NONCE_KEY',        'RIBZ.pI>X=-ot(MAGO@E&;PQ^clfK_2-Pmu=udRJvCPNc >CZwkZ6FiadMygsIc]' );
define( 'AUTH_SALT',        '3?&B2*f*@HbyYO}ew|ct@Fho!4z*E{<8PdiTIakw8l`:byPw}!yhnVMB=kv#]m<G' );
define( 'SECURE_AUTH_SALT', '+^S.,}hZ#t!qW6.ZDq]FohfGJN,=y*u+Ba74V,#3s}Y#qiATq@8}Ql_^+s8^Td8w' );
define( 'LOGGED_IN_SALT',   'Sw4sg,8[N,6MRBNv4.Fp]2:xw@4&]2]hP%ec+d] 4=>REe{g-(~27a=7XvrBs5dh' );
define( 'NONCE_SALT',       'IFum#5JNm6-pMS<h@E1l0r<sjQ*`P*YtNl.22vao>`koQ4.ST(cC@{Y$(=T>AFpt' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
