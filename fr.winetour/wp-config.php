<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/Editing_wp-config.php Modifier
 * wp-config.php} (en anglais). C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'winetourbordo');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'winetourbordo');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'ZQBT7vfu');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'mysql51-8.business');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'GlSl@_GP*pr<4OJ[2Lqo7;!YrdS.sH>CVy[SJ=boU|</UQiCUe/w}c?g~0u4TVd?'); 
define('SECURE_AUTH_KEY',  '!c#TgduU5xD7EAgx; Dl[N`c23vq-]L-:~I}FNQ6L-=RjKZ1y7mp05hG>^@S.gL}'); 
define('LOGGED_IN_KEY',    'l+R3/+ s/oE6JGxUo=6m|L(`]gsBtMn!}Jb-9LJC|r}mPVl63<![rQukct=X7<gm'); 
define('NONCE_KEY',        '`/-YuQ`.KgLY?qZ|dQ:]^CxLv>HWdEpw+7`w&FfVs8tlA(4Bx+6|<360m:MS;g)K'); 
define('AUTH_SALT',        'k_L2l0l>F*Ln_:kT+aOp(gp!|nN+-!Y|<YcOoPyR4@|`mYf7dtA%i1zW/+H19|CD'); 
define('SECURE_AUTH_SALT', 'wV+D %j9Aa_b-kOwEnEkPN5aC`WM]>=X!u|X22z!K++Zw7!gbqXp4X@Yd%, #M>n'); 
define('LOGGED_IN_SALT',   ':k[|[1-4Y&2>p$t?RbU^Uq48@,DBG<+$?K0.h#RmR##A%]->$+hSyz[||)V4^VL~'); 
define('NONCE_SALT',       'wV(_=rZCV2(f{8 -5sa@Io:x;CR%d WHbc3r7+_+MRykP/@Aw<Sd4)I{nTLe{.rI'); 
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/**
 * Langue de localisation de WordPress, par défaut en Anglais.
 *
 * Modifiez cette valeur pour localiser WordPress. Un fichier MO correspondant
 * au langage choisi doit être installé dans le dossier wp-content/languages.
 * Par exemple, pour mettre en place une traduction française, mettez le fichier
 * fr_FR.mo dans wp-content/languages, et réglez l'option ci-dessous à "fr_FR".
 */
define('WPLANG', 'fr_FR');

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');