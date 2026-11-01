<?php

return array(
	'installed_tips' => 'El programa ya está instalado. Para reinstalar, elimine conf/conf.php e install/install.lock.',
	'please_set_conf_file_writable' => 'Haga que conf/conf.php sea escribible !',
	'evn_not_support_php_mysql' => 'El entorno PHP actual no admite mysql y pdo_mysql, no se puede continuar la instalación.',
	'dbhost_is_empty' => 'El host de la base de datos no puede estar vacío',
	'dbhost_is_invalid' => 'Host de base de datos no válido. Solo se permiten letras, dígitos, puntos, guiones, guiones bajos y puerto',
	'dbname_is_empty' => 'El nombre de la base de datos no puede estar vacío',
	'dbname_is_invalid' => 'Nombre de base de datos no válido. Solo letras, dígitos, guiones y guiones bajos, máximo 64 caracteres',
	'dbuser_is_empty' => 'El nombre de usuario no puede estar vacío',
	'dbuser_is_invalid' => 'Nombre de usuario de base de datos no válido',
	'dbtype_is_invalid' => 'Tipo de base de datos no válido',
	'adminuser_is_empty' => 'El nombre de usuario del administrador no puede estar vacío',
	'adminpass_is_empty' => 'La contraseña del administrador no puede estar vacía',
	'adminemail_is_empty' => 'El correo del administrador no puede estar vacío',
	'conguralation_installed' => '¡Felicitaciones, instalación correcta! Por seguridad, elimine el directorio install.',
	
	'step_1_title' => '一、Verificación del entorno de instalación',
	'runtime_env_check' => 'Verificación del entorno de ejecución',
	'required' => 'Requerido',
	'current' => 'Actual',
	'check_result' => 'Resultado',
	'passed' => 'Aprobado',
	'not_passed' => 'Aprobado',
	'not_the_best' => 'Entorno no óptimo',
	'dir_writable_check' => 'Verificación de permisos de directorio/archivo',
	'writable' => 'Escribible',
	'unwritable' => 'No escribible',
	'check_again' => 'Volver a verificar',
	'os' => 'Sistema operativo',
	'unix_like' => 'Tipo UNIX',
	'php_version' => 'Versión de PHP',
	
	'step_2_title' => '二、Configuración de la base de datos',
	'db_type' => 'Tipo de base de datos',
	'db_engine' => 'Motor de base de datos',
	'db_host' => 'Servidor de base de datos',
	'db_name' => 'Nombre de la base de datos',
	'db_user' => 'Nombre de usuario de la base',
	'db_pass' => 'Contraseña de la base',
	'step_3_title' => '三、Información del administrador',
	'admin_email' => 'Correo del administrador',
	'admin_username' => 'Nombre de usuario del administrador',
	'admin_pw' => 'Contraseña del administrador',
	'installing_about_moment' => 'Instalando, tarda aproximadamente un minuto',
	'license_title' => 'Acuerdo de licencia en chino de Xunio BBS 4.0',
	'license_content' => 'Gracias por elegir Xunio BBS 4.0, un foro ligero, compacto, estable y capaz de mantener una alta capacidad de carga incluso con grandes volúmenes de datos. Solo tiene poco más de 20 tablas, el código fuente comprimido ocupa aproximadamente 1 MB, funciona muy rápido, procesa cada solicitud en 0,01 segundos y puede alcanzar 0,00x segundos en entornos con APC, Yac o XCache. La dependencia de bibliotecas de terceros es mínima, solo jquery.js en el front. El autor lo considera como un Ferrari artesanal: potente y sin grasa, fácil de desplegar y mantener, una excelente base para el desarrollo secundario.
	
Xunio BBS 4.0 utiliza Bootstrap 4 + JQuery 3 como bibliotecas front y admite navegadores móviles. El backend XunioPHP 4.0 permite manipular varias bases de datos al estilo NoSQL; esta versión es un gran salto adelante.
	
Xunio se pronuncia «xiuluo», Shura en inglés, uno de los seis reinos del budismo: el «reino de los asuras», situado entre el reino humano y el de los dioses.

Xunio BBS 4.0 se publica bajo la licencia MIT. Puede modificarlo libremente, crear versiones derivadas y usarlo comercialmente sin riesgo legal (conserve los avisos de derechos de autor originales tras modificarlo).',
	'license_date' => 'Publicado: 22 de enero de 2018',
	'agree_license_to_continue' => 'Aceptar el acuerdo y continuar la instalación',
	'install_title' => 'Asistente de instalación de Xunio BBS 4.0',
	'install_guide' => 'Asistente de instalación',
	
	'function_check' => 'Verificación de dependencias de funciones',
	'supported' => 'Admitido',
	'not_supported' => 'No admitido',
	'function_glob_not_exists' => 'La función de complementos del backend depende de esta función. Configure php.ini con disabled_functions = ; para eliminar la restricción',
	'function_gzcompress_not_exists' => 'La función de complementos del backend depende de esta función. En Linux añada --with-zlib; en Windows descomente extension=php_zlib.dll en php.ini',
	'function_mb_substr_not_exists' => 'El sistema depende de esta función. En Linux añada --with-mbstring; en Windows descomente extension=php_mbstring.dll en php.ini',
	
	// hook lang_es_es_bbs_install.php

);

?>
