<?php 
$module_name = variable_get('deployment_module_name'); 
define("PERMISSIONS_FILE", __DIR__ . "/../../".$module_name."/config/permissions.json"); 
define("USER_ACCOUNT_SETTINGS_FILE", __DIR__ . "/../../".$module_name."/config/user_account_settings.json"); 
define("SITE_SETTINGS_FILE", __DIR__ . "/../../".$module_name."/config/site_settings.json"); 
?>