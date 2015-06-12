<?php
/**
 * In dieser Datei werden die Grundeinstellungen für WordPress vorgenommen.
 *
 * Zu diesen Einstellungen gehören: MySQL-Zugangsdaten, Tabellenpräfix,
 * Secret-Keys, Sprache und ABSPATH. Mehr Informationen zur wp-config.php gibt es
 * auf der {@link http://codex.wordpress.org/Editing_wp-config.php wp-config.php editieren}
 * Seite im Codex. Die Informationen für die MySQL-Datenbank bekommst du von deinem Webhoster.
 *
 * Diese Datei wird von der wp-config.php-Erzeugungsroutine verwendet. Sie wird ausgeführt,
 * wenn noch keine wp-config.php (aber eine wp-config-sample.php) vorhanden ist,
 * und die Installationsroutine (/wp-admin/install.php) aufgerufen wird.
 * Man kann aber auch direkt in dieser Datei alle Eingaben vornehmen und sie von
 * wp-config-sample.php in wp-config.php umbenennen und die Installation starten.
 *
 * @package WordPress
 */

/**  MySQL Einstellungen - diese Angaben bekommst du von deinem Webhoster. */
/**  Ersetze database_name_here mit dem Namen der Datenbank, die du verwenden möchtest. */
define('DB_NAME', 'usr_web14_1');

/** Ersetze username_here mit deinem MySQL-Datenbank-Benutzernamen */
define('DB_USER', 'web14');

/** Ersetze password_here mit deinem MySQL-Passwort */
define('DB_PASSWORD', 'dmc20101969');

/** Ersetze localhost mit der MySQL-Serveradresse */
define('DB_HOST', 'localhost');

/** Der Datenbankzeichensatz der beim Erstellen der Datenbanktabellen verwendet werden soll */
define('DB_CHARSET', 'utf8');

/** Der collate type sollte nicht geändert werden */
define('DB_COLLATE', '');

/**#@+
 * Sicherheitsschlüssel
 *
 * Ändere jeden KEY in eine beliebige, möglichst einzigartige Phrase.
 * Auf der Seite {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * kannst du dir alle KEYS generieren lassen.
 * Bitte trage für jeden KEY eine eigene Phrase ein. Du kannst die Schlüssel jederzeit wieder ändern,
 * alle angemeldeten Benutzer müssen sich danach erneut anmelden.
 *
 * @seit 2.6.0
 */
define('AUTH_KEY',         'w^}2ePV=9fk5YxFr)wy}~-N~,7VQZ-Qc}fj#ySKj2I(_NzU0h*g!kayb?pG)Kc!~');
define('SECURE_AUTH_KEY',  'P2/I4H?Dra1rYBStge%1CD./vOMw0T?X!Ag.<Aub c<-u)]5x63Yc:H)P_BrFs|H');
define('LOGGED_IN_KEY',    '7d#37<.(<AK-(]JeY;77nH[?U@ hLx?Th$r3%DvF:0s*&!nTw1>b |2&u1mt+MRA');
define('NONCE_KEY',        '<-eA^Q=Hp$-V<t/4XQoA};mQIdDxDY;BUm:|Td=+}-,$vIqdd.OGEkQn=kheSgH$');
define('AUTH_SALT',        '9C9^qJ~4Wx?ze:B_O3&J;0idMg.U|WK%)|$_yc6VQ3:Sb!-)<2i-$I&glle$)3`w');
define('SECURE_AUTH_SALT', 'X`{L)T|p23+akE^nfLMg~CvI;FlsnB#xFm@2*Ip+7Yp_O`{J$qsjnHGM{||LPqs;');
define('LOGGED_IN_SALT',   '&#ZcoZL)q2w.UcA(~ma(NB,b!uIt?VgeB~(!h)O9C{n9c`Der+`],$pHKJtL 4Gn');
define('NONCE_SALT',       '}mAe8)xmj_4:Eufp:>%@`Y+us9>](^?TbYV`vYG2xB=]=y-FAiH7v^mJoiYZAzNQ');

/**#@-*/

/**
 * WordPress Datenbanktabellen-Präfix
 *
 *  Wenn du verschiedene Präfixe benutzt, kannst du innerhalb einer Datenbank
 *  verschiedene WordPress-Installationen betreiben. Nur Zahlen, Buchstaben und Unterstriche bitte!
 */
$table_prefix  = 'dmc_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
