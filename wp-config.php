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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'note_wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'MySQLLocalP@33' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3307' );

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
define( 'AUTH_KEY',         'y|OS)B*SM_p$UjB/c-v#o$Pp!A27shv4Abc+h#W.U+/NyWW-6*M#MM$;Luddc&O[' );
define( 'SECURE_AUTH_KEY',  '5J*ws*Po=&[3hhI1z_QBn#4p9[htmKT9kim+FloVy>Xe: 6K>E(^jD<3h30_&H)z' );
define( 'LOGGED_IN_KEY',    ';06k@:fFgFxHHgqI0:vNAPt<zx-WW8t1c,m4Z/ZW]5LpAqAnLv c`W9lAZd+SGC>' );
define( 'NONCE_KEY',        '01dHZb.u/YU6Tb;yu9A`$pQ63fk?tG~]Nd[&_Vl:_4UYw,/S0qz-So@fwVD.BSYW' );
define( 'AUTH_SALT',        'X%$A RC!d+w`GX3wE>tMk<L7+YjWLQN.zR3>]^u{e-C$Rk*f$I,}q!>@0JZXWA5w' );
define( 'SECURE_AUTH_SALT', '3nen5{:NO 9v}@V<| yNx-1a-w?RN{|%pK(4SS0.kC*^dSIt(V[!C2%m%>tn1saL' );
define( 'LOGGED_IN_SALT',   '%7Z-x7J[(-pJ)f9S,dz,bw_5:4t~XEL/- if]pg sYxD41?rp#mt#iuLp]vSMzS@' );
define( 'NONCE_SALT',       'Rx8eZFi)O6Ak2%j+;(8gzRo$6[a|n4(97>!e=n7E -1#QmLdN=3zqHop5<y^f-Wz' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
