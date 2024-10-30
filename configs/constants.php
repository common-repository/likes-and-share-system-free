<?php

defined('LASS_ASSETS_ADMIN') OR define('LASS_ASSETS_ADMIN', plugins_url('/extensions/assets/admin', plugin_dir_path( __FILE__ )) );

defined('LASS_ASSETS_FRONT') OR define('LASS_ASSETS_FRONT', plugins_url('/extensions/assets/front', plugin_dir_path( __FILE__ )) );

defined('LASS_ADMIN_IMG') OR define('LASS_ADMIN_IMG', plugins_url('/extensions/assets/admin/img', plugin_dir_path( __FILE__ )) );

defined('LASS_FRONT_IMG') OR define('LASS_FRONT_IMG', plugins_url('/extensions/assets/front/img', plugin_dir_path( __FILE__ )) );

defined('LASS_TEMPLATES_ADMIN') OR define('LASS_TEMPLATES_ADMIN', dirname(__DIR__) . '/extensions/templates/admin' );

defined('LASS_TEMPLATES_FRONT') OR define('LASS_TEMPLATES_FRONT', dirname(__DIR__) . '/extensions/templates/front' );

defined('LASS_ICONS_PATH') OR define('LASS_ICONS_PATH', dirname(__DIR__) . '/extensions/assets/front/lass-icons' );

define ( 'LASS_PLUGIN_VERSION', '1.0.0');