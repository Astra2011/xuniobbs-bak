<?php

return array(
	'installed_tips' => 'Le programme est déjà installé. Pour réinstaller, veuillez supprimer conf/conf.php et install/install.lock !',
	'please_set_conf_file_writable' => 'Veuillez rendre conf/conf.php inscriptible !',
	'evn_not_support_php_mysql' => 'L\'environnement PHP actuel ne prend pas en charge mysql et pdo_mysql, installation impossible.',
	'dbhost_is_empty' => 'L\'hôte de la base de données ne peut pas être vide',
	'dbhost_is_invalid' => 'Hôte de base de données invalide. Seuls les lettres, chiffres, points, tirets, underscores et le port sont autorisés',
	'dbname_is_empty' => 'Le nom de la base de données ne peut pas être vide',
	'dbname_is_invalid' => 'Nom de base de données invalide. Seuls lettres, chiffres, tirets et underscores, 64 caractères max',
	'dbuser_is_empty' => 'Le nom d\'utilisateur ne peut pas être vide',
	'dbuser_is_invalid' => 'Nom d\'utilisateur de base de données invalide',
	'dbtype_is_invalid' => 'Type de base de données invalide',
	'adminuser_is_empty' => 'Le nom d\'utilisateur administrateur ne peut pas être vide',
	'adminpass_is_empty' => 'Le mot de passe administrateur ne peut pas être vide',
	'adminemail_is_empty' => 'L\'e-mail de l\'administrateur ne peut pas être vide',
	'conguralation_installed' => 'Félicitations, installation réussie ! Par sécurité, supprimez le répertoire install.',
	
	'step_1_title' => '一、Vérification de l\'environnement',
	'runtime_env_check' => 'Vérification de l\'environnement d\'exécution',
	'required' => 'Requis',
	'current' => 'Actuel',
	'check_result' => 'Résultat',
	'passed' => 'Réussi',
	'not_passed' => 'Réussi',
	'not_the_best' => 'Environnement non optimal',
	'dir_writable_check' => 'Vérification des droits répertoire/fichier',
	'writable' => 'Inscriptible',
	'unwritable' => 'Non inscriptible',
	'check_again' => 'Revérifier',
	'os' => 'Système d\'exploitation',
	'unix_like' => 'De type UNIX',
	'php_version' => 'Version PHP',
	
	'step_2_title' => '二、Paramètres de la base de données',
	'db_type' => 'Type de base de données',
	'db_engine' => 'Moteur de base de données',
	'db_host' => 'Serveur de base de données',
	'db_name' => 'Nom de la base de données',
	'db_user' => 'Nom d\'utilisateur de la base',
	'db_pass' => 'Mot de passe de la base',
	'step_3_title' => '三、Informations administrateur',
	'admin_email' => 'E-mail administrateur',
	'admin_username' => 'Nom d\'utilisateur administrateur',
	'admin_pw' => 'Mot de passe administrateur',
	'installing_about_moment' => 'Installation en cours, environ une minute',
	'license_title' => 'Contrat de licence chinois de Xunio BBS 4.0',
	'license_content' => 'Merci d\'avoir choisi Xunio BBS 4.0, un forum léger, compact, stable et capable de maintenir une forte charge même avec de gros volumes de données. Il ne compte qu\'une vingtaine de tables, le code source compressé fait environ 1 Mo, fonctionne très vite, traite chaque requête en 0,01 seconde et peut atteindre 0,00x seconde avec APC, Yac ou XCache. La dépendance aux bibliothèques tierces est minime, seul jquery.js côté front. L\'auteur le considère comme une Ferrari entièrement artisanale : puissante, sans aucun gras, facile à déployer et à maintenir, une excellente base de développement secondaire.
	
Xunio BBS 4.0 utilise Bootstrap 4 + JQuery 3 côté front et prend en charge les navigateurs mobiles. Le backend XunioPHP 4.0 permet de manipuler diverses bases de données à la manière NoSQL ; cette version est un grand bond en avant.
	
Xunio se prononce « xiuluo », Shura en anglais, l\'un des six royaumes du bouddhisme — le « royaume des Ashuras », situé entre le royaume des humains et celui des dieux.

Xunio BBS 4.0 est publié sous licence MIT. Vous pouvez librement le modifier, créer des versions dérivées et l\'utiliser commercialement sans risque juridique (conservez les mentions de droits d\'auteur après modification).',
	'license_date' => 'Publié : 22 janvier 2018',
	'agree_license_to_continue' => 'Accepter le contrat et continuer l\'installation',
	'install_title' => 'Assistant d\'installation de Xunio BBS 4.0',
	'install_guide' => 'Assistant d\'installation',
	
	'function_check' => 'Vérification des dépendances de fonctions',
	'supported' => 'Pris en charge',
	'not_supported' => 'Non pris en charge',
	'function_glob_not_exists' => 'La fonctionnalité d\'extension du backend dépend de cette fonction. Configurez php.ini avec disabled_functions = ; pour lever la restriction',
	'function_gzcompress_not_exists' => 'La fonctionnalité d\'extension du backend dépend de cette fonction. Sur Linux, ajoutez --with-zlib ; sur Windows, décommentez extension=php_zlib.dll dans php.ini',
	'function_mb_substr_not_exists' => 'Le système dépend de cette fonction. Sur Linux, ajoutez --with-mbstring ; sur Windows, décommentez extension=php_mbstring.dll dans php.ini',
	
	// hook lang_fr_fr_bbs_install.php

);

?>
