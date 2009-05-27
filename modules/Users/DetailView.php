<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


require_once('include/DetailView/DetailView.php');
require_once('include/export_utils.php');
require_once('include/timezone/timezones.php');



global $current_user;
global $theme;
global $app_strings;
global $mod_strings;
global $timezones;
if (!is_admin($current_user) && !is_admin_for_module($GLOBALS['current_user'],'Users') && ($_REQUEST['record'] != $current_user->id)) sugar_die("Unauthorized access to administration.");

$focus = new User();


$detailView = new DetailView();
$offset=0;
if (isset($_REQUEST['offset']) || !empty($_REQUEST['record'])) {
	$result = $detailView->processSugarBean("USER", $focus, $offset);
	if($result == null) {
	    sugar_die($app_strings['ERROR_NO_RECORD']);
	}
	$focus=$result;
} else {
	header("Location: index.php?module=Users&action=index");
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
}
if(isset($_REQUEST['reset_preferences'])){
	$current_user->resetPreferences($focus);
}
if(isset($_REQUEST['reset_homepage'])){
    $current_user->resetPreferences($focus, 'Home');
    $_COOKIE[$current_user->id . '_activePage'] = '0';
    setcookie($current_user->id . '_activePage','0',3000);
}
if(isset($_REQUEST['reset_dashboard'])){
    $current_user->resetPreferences($focus, 'Dashboard');
    $_COOKIE[$current_user->id . '_activeDashboardPage'] = '0';
    setcookie($current_user->id . '_activeDashboardPage','0',3000);
}

echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$focus->full_name." (".$focus->user_name.")", true);
global $app_list_strings;




$GLOBALS['log']->info("User detail view");

$xtpl=new XTemplate ('modules/Users/DetailView.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
$xtpl->assign("GRIDLINE", $gridline);
$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("ID", $focus->id);
$xtpl->assign("USER_NAME", $focus->user_name);
$xtpl->assign("FULL_NAME", $focus->full_name);
if(!empty($GLOBALS['sugar_config']['authenticationClass'])){
		$authclass =  $GLOBALS['sugar_config']['authenticationClass'];
}else if(!empty($GLOBALS['system_config']->settings['system_ldap_enabled'])){
		$authclass =  'LDAPAuthenticate';
}
if(is_admin($GLOBALS['current_user']) && !empty($authclass)){
$str = '<tr><td valign="top" scope="row">';
$str .= $authclass . ':';
$str .= '</td><td><input type="checkbox" disabled ';
if(!empty($focus->external_auth_only))$str .= ' CHECKED ';
$str .='/></td><td>'.  $mod_strings['LBL_EXTERNAL_AUTH_ONLY'] . ' ' . $authclass. '</td></tr>';
$xtpl->assign('EXTERNAL_AUTH', $str);
}



///////////////////////////////////////////////////////////////////////////////
////	TO SUPPORT LEGACY XTEMPLATES
$xtpl->assign('FIRST_NAME', $focus->first_name);
$xtpl->assign('LAST_NAME', $focus->last_name);
////	END SUPPORT LEGACY XTEMPLATES
///////////////////////////////////////////////////////////////////////////////

$status = '';
if($focus->is_group) { $status = $mod_strings['LBL_GROUP_USER_STATUS']; }
elseif(!empty($focus->status)) {
  // jc:#12261 - while not apparent, replaced the explicit reference to the
  // app_strings['user_status_dom'] element with a call to the ultility translate
  // function to retrieved the mapped value for User::status
  $status = translate('user_status_dom', '', $focus->status);
}
$xtpl->assign("STATUS", $status);

$detailView->processListNavigation($xtpl, "USER", $offset);
$reminder_time = $focus->getPreference('reminder_time');

if(empty($reminder_time)){
	$reminder_time = -1;
}
if($reminder_time != -1){
	$xtpl->assign("REMINDER_CHECKED", 'checked');
	$xtpl->assign("REMINDER_TIME", translate('reminder_time_options', '', $reminder_time));
}

// Display the good usertype
$user_type_label=$mod_strings['LBL_REGULAR_USER'];
$usertype='RegularUser';

if((is_admin($current_user) || $_REQUEST['record'] == $current_user->id || is_admin_for_module($current_user,'Users')) && $focus->is_admin == '1'){
	$user_type_label=$mod_strings['LBL_ADMIN_USER'];
	$usertype='Administrator';
}

if(!empty($focus->is_group) && $focus->is_group == 1){
	$user_type_label=$mod_strings['LBL_GROUP'];
	$usertype='GroupUser';
}







$xtpl->assign("USER_TYPE", $usertype);
$xtpl->assign("USER_TYPE_LABEL", $user_type_label);





// adding custom fields:
require_once('modules/DynamicFields/templates/Files/DetailView.php');
$buttons="<div id='pwd_sent' class='error'>";
if (isset($_REQUEST['pwd_set']) && $_REQUEST['pwd_set']!= 0)
	$buttons.=$mod_strings['LBL_NEW_USER_PASSWORD_'.$_REQUEST['pwd_set']];			
else
	$buttons.="<br>";
$buttons.="</div></td><tr/><tr><td>";
if ((is_admin($current_user) || $_REQUEST['record'] == $current_user->id)
		&& !empty($sugar_config['default_user_name'])
		&& $sugar_config['default_user_name'] == $focus->user_name
		&& isset($sugar_config['lock_default_user_name'])
		&& $sugar_config['lock_default_user_name']) {
	$buttons .= "<input title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='button' onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; this.form.action.value='EditView'\" type='submit' name='Edit' value='  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  '>  ";
}
elseif (is_admin($current_user)|| is_admin_for_module($GLOBALS['current_user'],'Users') || $_REQUEST['record'] == $current_user->id) {
	$buttons .= "<input title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='button' onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; this.form.action.value='EditView'\" type='submit' name='Edit' value='  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  '>  ";
	if (is_admin($current_user)|| is_admin_for_module($GLOBALS['current_user'],'Users')){
		if (!$current_user->is_group){
			if(!(!is_admin($GLOBALS['current_user']) && is_admin_for_module($GLOBALS['current_user'],'Users') && $focus->is_admin)){
				$buttons .= "<input title='".$app_strings['LBL_DUPLICATE_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_DUPLICATE_BUTTON_KEY']."' class='button' onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value=true; this.form.action.value='EditView'\" type='submit' name='Duplicate' value=' ".$app_strings['LBL_DUPLICATE_BUTTON_LABEL']."  '>  ";
			}
			if (!$focus->portal_only && !$focus->is_group){
				$buttons .= "<input title='".$mod_strings['LBL_GENERATE_PASSWORD_BUTTON_TITLE']."' accessKey='".$mod_strings['LBL_GENERATE_PASSWORD_BUTTON_KEY']."' class='button' LANGUAGE=javascript onclick='generatepwd(\"".$focus->id."\");' type='button' name='password' value='".$mod_strings['LBL_GENERATE_PASSWORD_BUTTON_LABEL']."'>  ";
			}
		}
	}
}

if(isset($_SERVER['QUERY_STRING'])) $the_query_string = $_SERVER['QUERY_STRING'];
else $the_query_string = '';

$buttons .="<td width='50%' align='right' nowrap>";
if (!$current_user->is_group){
	$buttons .="<b>".$mod_strings['LBL_RESET_TO_DEFAULT']."</b>: ";
	$buttons .="<input type='button' class='button' onclick='if(confirm(\"{$mod_strings['LBL_RESET_PREFERENCES_WARNING']}\"))window.location=\"".$_SERVER['PHP_SELF'] .'?'.$the_query_string."&reset_preferences=true\";' value='".$mod_strings['LBL_RESET_PREFERENCES']."' />";
	$buttons .="&nbsp;<input type='button' class='button' onclick='if(confirm(\"{$mod_strings['LBL_RESET_HOMEPAGE_WARNING']}\"))window.location=\"".$_SERVER['PHP_SELF'] .'?'.$the_query_string."&reset_homepage=true\";' value='".$mod_strings['LBL_RESET_HOMEPAGE']."' />";
	$buttons .="&nbsp;<input type='button' class='button' onclick='if(confirm(\"{$mod_strings['LBL_RESET_DASHBOARD_WARNING']}\"))window.location=\"".$_SERVER['PHP_SELF'] .'?'.$the_query_string."&reset_dashboard=true\";' value='".$mod_strings['LBL_RESET_DASHBOARD']."' />";
}
if (isset($buttons)) $xtpl->assign("BUTTONS", $buttons);





require_once("include/templates/TemplateGroupChooser.php");
require_once("modules/MySettings/TabController.php");
$chooser = new TemplateGroupChooser();
$controller = new TabController();

//if(is_admin($current_user) || $controller->get_users_can_edit())
if(is_admin($current_user)||is_admin_for_module($GLOBALS['current_user'],'Users'))
{
	$chooser->display_third_tabs = true;
	$chooser->args['third_name'] = 'remove_tabs';
	$chooser->args['third_label'] =  $mod_strings['LBL_REMOVED_TABS'];
}
elseif(!$controller->get_users_can_edit())
{
	$chooser->display_hide_tabs = false;
}
else
{
	$chooser->display_hide_tabs = true;
}

$chooser->args['id'] = 'edit_tabs';
$chooser->args['values_array'] = $controller->get_tabs($focus);
$chooser->args['left_name'] = 'display_tabs';
$chooser->args['right_name'] = 'hide_tabs';
$chooser->args['left_label'] =  $mod_strings['LBL_DISPLAY_TABS'];
$chooser->args['right_label'] =  $mod_strings['LBL_HIDE_TABS'];
$chooser->args['title'] =  $mod_strings['LBL_EDIT_TABS'];
$chooser->args['disable'] = true;

foreach ($chooser->args['values_array'][0] as $key=>$value)
{
$chooser->args['values_array'][0][$key] = $app_list_strings['moduleList'][$key];
}
foreach ($chooser->args['values_array'][1] as $key=>$value)
{
$chooser->args['values_array'][1][$key] = $app_list_strings['moduleList'][$key];
}


$xtpl->assign("TAB_CHOOSER", $chooser->display());
$xtpl->assign("CHOOSE_WHICH", $mod_strings['LBL_CHOOSE_WHICH']);
$xtpl->parse("user_info.tabchooser");


$xtpl->parse("main");

if ($focus->receive_notifications) $xtpl->assign("RECEIVE_NOTIFICATIONS", "checked");

if($focus->getPreference('gridline') == 'on') {
$xtpl->assign("GRIDLINE_CHECK", "checked");
}

if($focus->getPreference('mailmerge_on') == 'on') {
$xtpl->assign("MAILMERGE_ON", "checked");
}








$xtpl->assign("SETTINGS_URL", $sugar_config['site_url']);

$xtpl->assign("EXPORT_DELIMITER", getDelimiter());
$xtpl->assign('EXPORT_CHARSET', $locale->getExportCharset('', $focus));
$xtpl->assign('USE_REAL_NAMES', $focus->getPreference('use_real_names'));


global $timedate;
$xtpl->assign("DATEFORMAT", $sugar_config['date_formats'][$timedate->get_date_format()]);
$xtpl->assign("TIMEFORMAT", $sugar_config['time_formats'][$timedate->get_time_format()]);

$userTZ = $focus->getPreference('timezone');
if(!empty($userTZ) && isset($timezones[$userTZ])) {
	$value = $timezones[$userTZ];
}
if(!empty($value['dstOffset'])) {
	$dst = " (+DST)";
} else {
	$dst = "";
}
$gmtOffset = ($value['gmtOffset'] / 60);
if(!strstr($gmtOffset,'-')) {
	$gmtOffset = "+".$gmtOffset;
}

$xtpl->assign("TIMEZONE", $userTZ. str_replace('_',' '," (GMT".$gmtOffset.") ".$dst) );
$datef = $focus->getPreference('datef');
$timef = $focus->getPreference('timef');

if(!empty($datef))
$xtpl->assign("DATEFORMAT", $sugar_config['date_formats'][$datef]);
if(!empty($timef))
$xtpl->assign("TIMEFORMAT", $sugar_config['time_formats'][$timef]);

$num_grp_sep = $focus->getPreference('num_grp_sep');
$dec_sep = $focus->getPreference('dec_sep');
$xtpl->assign("NUM_GRP_SEP", (empty($num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $num_grp_sep));
$xtpl->assign("DEC_SEP", (empty($dec_sep) ? $sugar_config['default_decimal_seperator'] : $dec_sep));



$currency  = new Currency();
if($focus->getPreference('currency') ) {
	$currency->retrieve($focus->getPreference('currency'));
	$xtpl->assign("CURRENCY", $currency->iso4217 .' '.$currency->symbol );
} else {
	$xtpl->assign("CURRENCY", $currency->getDefaultISO4217() .' '.$currency->getDefaultCurrencySymbol() );
}






$xtpl->parse("user_locale.currency");







$xtpl->assign('CURRENCY_SIG_DIGITS', $locale->getPrecedentPreference('default_currency_significant_digits', $focus));

$xtpl->parse("user_settings");

$xtpl->assign('NAME_FORMAT', $focus->getLocaleFormatDesc());
$xtpl->parse('user_locale');


























$xtpl->assign("DESCRIPTION", nl2br(url2html($focus->description)));
$xtpl->assign("TITLE", $focus->title);
$xtpl->assign("DEPARTMENT", $focus->department);
$xtpl->assign("REPORTS_TO_ID", $focus->reports_to_id);
$xtpl->assign("REPORTS_TO_NAME", $focus->reports_to_name);
$xtpl->assign("PHONE_HOME", $focus->phone_home);
$xtpl->assign("PHONE_MOBILE", $focus->phone_mobile);
$xtpl->assign("PHONE_WORK", $focus->phone_work);
$xtpl->assign("PHONE_OTHER", $focus->phone_other);
$xtpl->assign("PHONE_FAX", $focus->phone_fax);
$xtpl->assign("EMPLOYEE_STATUS", $focus->employee_status);
$xtpl->assign("MESSENGER_ID", $focus->messenger_id);
$xtpl->assign("MESSENGER_TYPE", $focus->messenger_type);
$xtpl->assign("ADDRESS_STREET", $focus->address_street);
$xtpl->assign("ADDRESS_CITY", $focus->address_city);
$xtpl->assign("ADDRESS_STATE", $focus->address_state);
$xtpl->assign("ADDRESS_POSTALCODE", $focus->address_postalcode);
$xtpl->assign("ADDRESS_COUNTRY", $focus->address_country);
$xtpl->assign("EMAIL_ADDRESSES", $focus->emailAddress->getEmailAddressWidgetDetailView($focus));
$xtpl->assign("CALENDAR_PUBLISH_KEY", $focus->getPreference('calendar_publish_key' ));
if (! empty($current_user->email1))
{
    $publish_url = $sugar_config['site_url'].'/vcal_server.php';
    $token = "/";
    //determine if the web server is running IIS
    //if so then change the publish url
    if(isset($_SERVER) && !empty($_SERVER['SERVER_SOFTWARE'])){
        $position = strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'iis');
        if($position !== false){
            $token = '?parms=';
        }
    }

    $publish_url .= $token.'type=vfb&email='.$focus->email1.'&source=outlook&key='.$focus->getPreference('calendar_publish_key' );
    $xtpl->assign("CALENDAR_PUBLISH_URL", $publish_url);
    $xtpl->assign("CALENDAR_SEARCH_URL", $sugar_config['site_url'].'/vcal_server.php/type=vfb&email=%NAME%@%SERVER%');
}
else
{
  $xtpl->assign("CALENDAR_PUBLISH_URL", $sugar_config['site_url'].'/vcal_server.php/type=vfb&user_name='.$focus->user_name.'&source=outlook&key='.$focus->getPreference('calendar_publish_key' ));
  $xtpl->assign("CALENDAR_SEARCH_URL", $sugar_config['site_url'].'/vcal_server.php/type=vfb&email=%NAME%@%SERVER%');
}

$xtpl->parse("freebusy");

$user_max_tabs = intval($focus->getPreference('max_tabs'));
if(isset($user_max_tabs) && $user_max_tabs > 0)
    $xtpl->assign("MAX_TAB", $user_max_tabs);
elseif(isset($max_tabs) && $max_tabs > 0)
    $xtpl->assign("MAX_TAB", $max_tabs);
else
    $xtpl->assign("MAX_TAB", $GLOBALS['sugar_config']['default_max_tabs']);

$user_max_subtabs = intval($focus->getPreference('max_subtabs'));
if(isset($user_max_subtabs) && $user_max_subtabs > 0)
    $xtpl->assign("MAX_SUBTAB", $user_max_subtabs);
else
    $xtpl->assign("MAX_SUBTAB", $GLOBALS['sugar_config']['default_max_subtabs']);

$user_swap_last_viewed = $focus->getPreference('swap_last_viewed');
if(isset($user_swap_last_viewed)) {
    $xtpl->assign("SWAP_LAST_VIEWED", $user_swap_last_viewed?'checked':'');
} else {
    $xtpl->assign("SWAP_LAST_VIEWED", $GLOBALS['sugar_config']['default_swap_last_viewed']?'checked':'');
}

$user_swap_shortcuts = $focus->getPreference('swap_shortcuts');
if(isset($user_swap_shortcuts)) {
    $xtpl->assign("SWAP_SHORTCUT", $user_swap_shortcuts?'checked':'');
} else {
    $xtpl->assign("SWAP_SHORTCUT", $GLOBALS['sugar_config']['default_swap_shortcuts']?'checked':'');
}

$user_subpanel_tabs = $focus->getPreference('subpanel_tabs');
if(isset($user_subpanel_tabs)) {
    $xtpl->assign("SUBPANEL_TABS", $user_subpanel_tabs?'checked':'');
} else {
    $xtpl->assign("SUBPANEL_TABS", $GLOBALS['sugar_config']['default_subpanel_tabs']?'checked':'');
}

$user_subpanel_links = $focus->getPreference('subpanel_links');
$xtpl->assign("SUBPANEL_LINKS", $user_subpanel_links?'checked':'');
if(isset($user_subpanel_links)) {
    $xtpl->assign("SUBPANEL_LINKS", $user_subpanel_links?'checked':'');
} else {
    $xtpl->assign("SUBPANEL_LINKS", $GLOBALS['sugar_config']['default_subpanel_links']?'checked':'');
}

$user_navigation_paradigm = $focus->getPreference('navigation_paradigm');
if(isset($user_navigation_paradigm)&& $user_navigation_paradigm != '') {
    $xtpl->assign("NAVIGATION_PARADIGM", $app_list_strings['navigation_paradigms'][$user_navigation_paradigm]);
} else {
    $xtpl->assign("NAVIGATION_PARADIGM", $app_list_strings['navigation_paradigms'][$GLOBALS['sugar_config']['default_navigation_paradigm']]);
}

$user_module_favicon = $focus->getPreference('module_favicon');
if(isset($user_module_favicon)) {
    $xtpl->assign("MODULE_FAVICON", $user_module_favicon?'checked':'');
} else {
    $xtpl->assign("MODULE_FAVICON", isset($GLOBALS['sugar_config']['default_module_favicon']) && $GLOBALS['sugar_config']['default_module_favicon'] ?'checked':'');
}

$xtpl->parse("layoutopts");
$xtpl->parse("user_info");

// Email Options
$xtpl->assign("EMAIL_OPTIONS", $focus->emailAddress->getEmailAddressWidgetDetailView($focus));
$xtpl->assign('EMAIL_LINK_TYPE',$app_list_strings['dom_email_link_type'][$focus->getPreference('email_link_type')]);
$xtpl->parse("email_info");


//Name/Username/Status/
$xtpl->out("main");
//Email Options
$xtpl->out("email_info");
//User Information
//Address Information
$xtpl->out("user_info");
//User Settings
$xtpl->out("user_settings");
//Layout Options
$xtpl->out("layoutopts");
//Locale Settings
$xtpl->out('user_locale');



//Calendar Options
$xtpl->out("freebusy");
//Roles Grid [DetailView only]
//Roles subpanel [DetailView only]
//Teams subpanel [DetailView only]
// Roles Grid and Roles subpanel should not be displayed for group and portal users
if(!($focus->is_group=='1' || $focus->portal_only=='1')){
    require_once('modules/ACLRoles/DetailUserRole.php');
}






























echo "</td></tr>\n";


$savedSearch = new SavedSearch();
$json = getJSONobj();
$savedSearchSelects = $json->encode(array($GLOBALS['app_strings']['LBL_SAVED_SEARCH_SHORTCUT'] . '<br>' . $savedSearch->getSelect('Users')));
$str = "<script>
YAHOO.util.Event.addListener(window, 'load', SUGAR.util.fillShortcuts, $savedSearchSelects);
</script>";
echo $str;
echo "<script type='text/javascript'>user_status_display('$usertype') </script>";
?>
