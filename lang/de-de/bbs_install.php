<?php

return array(
	'installed_tips' => 'Das Programm ist bereits installiert. Für eine Neuinstallation bitte conf/conf.php löschen!',
	'please_set_conf_file_writable' => 'Bitte conf/conf.php beschreibbar machen!',
	'evn_not_support_php_mysql' => 'Die aktuelle PHP-Umgebung unterstützt mysql und pdo_mysql nicht, Installation kann nicht fortgesetzt werden.',
	'dbhost_is_empty' => 'Datenbank-Host darf nicht leer sein',
	'dbname_is_empty' => 'Datenbankname darf nicht leer sein',
	'dbuser_is_empty' => 'Benutzername darf nicht leer sein',
	'adminuser_is_empty' => 'Administrator-Benutzername darf nicht leer sein',
	'adminpass_is_empty' => 'Administrator-Passwort darf nicht leer sein',
	'conguralation_installed' => 'Glückwunsch, Installation erfolgreich! Bitte aus Sicherheitsgründen das install-Verzeichnis löschen.',
	
	'step_1_title' => '一、Installationsumgebungsprüfung',
	'runtime_env_check' => 'Prüfung der Website-Laufzeitumgebung',
	'required' => 'Erforderlich',
	'current' => 'Aktuell',
	'check_result' => 'Prüfergebnis',
	'passed' => 'Bestanden',
	'not_passed' => 'Bestanden',
	'not_the_best' => 'Nicht die ideale Umgebung',
	'dir_writable_check' => 'Prüfung der Verzeichnis-/Dateiberechtigungen',
	'writable' => 'Beschreibbar',
	'unwritable' => 'Nicht beschreibbar',
	'check_again' => 'Erneut prüfen',
	'os' => 'Betriebssystem',
	'unix_like' => 'Unix-ähnlich',
	'php_version' => 'PHP-Version',
	
	'step_2_title' => '二、Datenbankeinstellungen',
	'db_type' => 'Datenbanktyp',
	'db_engine' => 'Datenbank-Engine',
	'db_host' => 'Datenbankserver',
	'db_name' => 'Datenbankname',
	'db_user' => 'Datenbank-Benutzername',
	'db_pass' => 'Datenbank-Passwort',
	'step_3_title' => '三、Administratorinformationen',
	'admin_email' => 'Administrator-E-Mail',
	'admin_username' => 'Administrator-Benutzername',
	'admin_pw' => 'Administrator-Passwort',
	'installing_about_moment' => 'Installation läuft, dauert etwa eine Minute',
	'license_title' => 'Xiuno BBS 4.0 chinesische Lizenzvereinbarung',
	'license_content' => 'Vielen Dank, dass Sie sich für Xiuno BBS 4.0 entschieden haben. Es ist ein inländisches, kompaktes und stabiles leichtgewichtiges Forum, das auch bei großen Datenmengen eine hohe Belastbarkeit bewahrt. Es hat nur etwas mehr als 20 Tabellen, der Quellcode ist komprimiert etwa 1 MB groß, läuft sehr schnell, verarbeitet einzelne Anfragen im 0,01-Sekunden-Bereich und kann in Umgebungen mit APC, Yac, XCache bis zu 0,00x Sekunden erreichen. Die Abhängigkeit von Drittanbieter-Bibliotheken ist äußerst gering, im Frontend nur jquery.js. Der Autor betrachtet es wie einen rein handgefertigten Ferrari – kraftvoll und ohne jegliches Übergewicht, einfach zu deployen und zu warten, eine sehr gute Grundlage für die Sekundärentwicklung.
	
Xiuno BBS 4.0 verwendet Bootstrap 4 + JQuery 3 als Frontend-Bibliotheken und unterstützt mobile Browser umfassend. Das Backend XiunoPHP 4.0 unterstützt den NoSQL-Zugriff auf verschiedene Datenbanken – diese Version ist ein großer Sprung nach vorn.
	
Xiuno wird „Xiuluo" ausgesprochen, englisch Shura, im Buddhismus eine der sechs Daseinsbereiche – der „Ashura-Bereich", der sich zwischen dem Menschen- und dem Götterbereich befindet.

Xiuno BBS 4.0 wird unter der MIT-Lizenz veröffentlicht. Sie dürfen es frei ändern, abgeleitete Versionen erstellen und kommerziell nutzen, ohne rechtliche Risiken befürchten zu müssen (nach Änderungen sollte der ursprüngliche Urheberrechtshinweis erhalten bleiben).',
	'license_date' => 'Veröffentlicht: 22. Januar 2018',
	'agree_license_to_continue' => 'Vereinbarung zustimmen und Installation fortsetzen',
	'install_title' => 'Xiuno BBS 4.0 Installationsassistent',
	'install_guide' => 'Installationsassistent',
	
	'function_check' => 'Prüfung der Funktionsabhängigkeiten',
	'supported' => 'Unterstützt',
	'not_supported' => 'Nicht unterstützt',
	'function_glob_not_exists' => 'Die Backend-Plugin-Funktion hängt von dieser Funktion ab. Bitte php.ini konfigurieren, disabled_functions = ; setzen, um die Beschränkung aufzuheben',
	'function_gzcompress_not_exists' => 'Die Backend-Plugin-Funktion hängt von dieser Funktion ab. Auf Linux-Hosts bitte Compiler-Parameter --with-zlib hinzufügen, auf Windows-Hosts in php.ini die Zeile extension=php_zlib.dll auskommentieren',
	'function_mb_substr_not_exists' => 'Das System hängt von dieser Funktion ab. Auf Linux-Hosts bitte Compiler-Parameter --with-mbstring hinzufügen, auf Windows-Hosts in php.ini die Zeile extension=php_mbstring.dll auskommentieren',
	
	// hook lang_de_de_bbs_install.php

);

?>
