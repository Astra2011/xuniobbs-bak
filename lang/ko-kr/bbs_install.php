<?php

return array(
	'installed_tips' => '프로그램이 이미 설치되었습니다. 재설치하려면 conf/conf.php를 삭제해 주세요!',
	'please_set_conf_file_writable' => 'conf/conf.php 파일을 쓰기 가능하게 설정해 주세요!',
	'evn_not_support_php_mysql' => '현재 PHP 환경이 mysql과 pdo_mysql을 지원하지 않아 설치를 계속할 수 없습니다.',
	'dbhost_is_empty' => '데이터베이스 호스트는 비워둘 수 없습니다',
	'dbname_is_empty' => '데이터베이스 이름은 비워둘 수 없습니다',
	'dbuser_is_empty' => '사용자 이름은 비워둘 수 없습니다',
	'adminuser_is_empty' => '관리자 사용자 이름은 비워둘 수 없습니다',
	'adminpass_is_empty' => '관리자 비밀번호는 비워둘 수 없습니다',
	'conguralation_installed' => '축하합니다, 설치 성공! 안전을 위해 install 디렉터리를 삭제해 주세요.',
	
	'step_1_title' => '一、설치 환경 검사',
	'runtime_env_check' => '웹사이트 실행 환경 검사',
	'required' => '필요',
	'current' => '현재',
	'check_result' => '검사 결과',
	'passed' => '통과',
	'not_passed' => '통과',
	'not_the_best' => '최적의 환경이 아닙니다',
	'dir_writable_check' => '디렉터리/파일 권한 검사',
	'writable' => '쓰기 가능',
	'unwritable' => '쓰기 불가',
	'check_again' => '다시 검사',
	'os' => '운영 체제',
	'unix_like' => '유닉스 계열',
	'php_version' => 'PHP 버전',
	
	'step_2_title' => '二、데이터베이스 설정',
	'db_type' => '데이터베이스 유형',
	'db_engine' => '데이터베이스 엔진',
	'db_host' => '데이터베이스 서버',
	'db_name' => '데이터베이스 이름',
	'db_user' => '데이터베이스 사용자 이름',
	'db_pass' => '데이터베이스 비밀번호',
	'step_3_title' => '三、관리자 정보',
	'admin_email' => '관리자 이메일',
	'admin_username' => '관리자 사용자 이름',
	'admin_pw' => '관리자 비밀번호',
	'installing_about_moment' => '설치 중입니다. 약 1분 정도 걸립니다',
	'license_title' => 'Xunio BBS 4.0 중국어판 라이선스 계약',
	'license_content' => 'Xunio BBS 4.0을 선택해 주셔서 감사합니다. 이는 국산의, 작고 안정적이며 대량 데이터 환경에서도 높은 부하 능력을 유지하는 경량 포럼입니다. 테이블은 20여 개뿐이고 소스 코드 압축 후 약 1MB이며, 실행 속도가 매우 빠르고 단일 요청 처리는 0.01초 수준, APC·Yac·XCache 환경에서는 0.00x초까지 가능합니다. 서드파티 라이브러리 의존성이 극히 적고 프런트엔드는 jquery.js에만 의존합니다. 저자는 이것이 순수 수제작 페라리와 같다고 생각합니다. 강력하고 군더더기가 없으며 배포와 유지보수가 쉽고, 2차 개발의 훌륭한 기반입니다.
	
Xunio BBS 4.0은 Bootstrap 4 + JQuery 3을 프런트엔드 라이브러리로 사용하며 모바일 브라우저를 전면 지원합니다. 백엔드 XunioPHP 4.0은 NoSQL 방식으로 다양한 데이터베이스를 조작할 수 있게 되었으며, 이 버전은 큰 도약입니다.
	
Xunio의 발음은 「시우루오」, 영어로는 Shura이며, 불교에서 육도 중 하나인 「수라도」로, 인도(人道)와 천도(天道) 사이에 위치합니다.

Xunio BBS 4.0은 MIT 라이선스로 공개됩니다. 자유롭게 수정·파생 버전 제작·상업적 사용이 가능하며 법적 위험을 걱정할 필요가 없습니다(수정 후 원래 저작권 정보를 유지해야 합니다).',
	'license_date' => '공개일: 2018년 1월 22일',
	'agree_license_to_continue' => '계약에 동의하고 설치 계속',
	'install_title' => 'Xunio BBS 4.0 설치 마법사',
	'install_guide' => '설치 마법사',
	
	'function_check' => '함수 의존성 검사',
	'supported' => '지원',
	'not_supported' => '지원 안 함',
	'function_glob_not_exists' => '백엔드 플러그인 기능이 이 함수에 의존합니다. php.ini에서 disabled_functions = ;로 설정하여 이 함수의 제한을 해제해 주세요',
	'function_gzcompress_not_exists' => '백엔드 플러그인 기능이 이 함수에 의존합니다. Linux 호스트는 컴파일 매개변수 --with-zlib를 추가하고, Windows 호스트는 php.ini에서 extension=php_zlib.dll 주석을 해제해 주세요',
	'function_mb_substr_not_exists' => '시스템이 이 함수에 의존합니다. Linux 호스트는 컴파일 매개변수 --with-mbstring을 추가하고, Windows 호스트는 php.ini에서 extension=php_mbstring.dll 주석을 해제해 주세요',
	
	// hook lang_ko_kr_bbs_install.php

);

?>
