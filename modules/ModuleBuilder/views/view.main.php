<?php
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
 class ViewMain extends SugarView{
 	
 	
 	function ViewMain(){
		$this->options['show_footer'] = false;
 		parent::SugarView();
 	
 	}
 	
 	function display(){
		global $app_strings, $current_user, $mod_strings, $theme;
                
		if (is_dir("themes/$theme/ext/resources/css")) {
			$cssDir = opendir("themes/$theme/ext/resources/css");
		}
 		$smarty = new Sugar_Smarty();
 		$type = (!empty($_REQUEST['type']))?$_REQUEST['type']:'main';
 		$mbt = false;
 		$admin = false;
 		$mb = strtolower($type);
 		$smarty->assign('TYPE', $type);
 		$smarty->assign('app_strings', $app_strings);
 		$smarty->assign('mod', $mod_strings);
 		//Replaced by javascript function "setMode"
 		switch($type){
 			case 'studio':
 				//$smarty->assign('ONLOAD','ModuleBuilder.getContent("module=ModuleBuilder&action=wizard")');
 				require_once('modules/ModuleBuilder/Module/StudioTree.php');
				$mbt = new StudioTree();
				break;
 			case 'mb':
 				//$smarty->assign('ONLOAD','ModuleBuilder.getContent("module=ModuleBuilder&action=package&package=")');
 				require_once('modules/ModuleBuilder/MB/MBPackageTree.php');
				$mbt = new MBPackageTree();
				break;
 			case 'sugarportal':
 			    require_once('modules/ModuleBuilder/Module/SugarPortalTree.php');
 			    $mbt = new SugarPortalTree();
 			    break;
 			case 'dropdowns':
 			   // $admin = is_admin($current_user);
 			    require_once('modules/ModuleBuilder/Module/DropDownTree.php');
 			    $mbt = new DropDownTree();
 			    break;
 			default:
 				//$smarty->assign('ONLOAD','ModuleBuilder.getContent("module=ModuleBuilder&action=home")');	
				require_once('modules/ModuleBuilder/Module/MainTree.php');
				$mbt = new MainTree();
 		}
 		$smarty->assign('TEST_STUDIO', displayStudioForCurrentUser());
 		$smarty->assign('ADMIN', is_admin($current_user));
 		$smarty->display('modules/ModuleBuilder/tpls/includes.tpl');
		if($mbt)
		{
			$smarty->assign('TREE',$mbt->fetch());
			$smarty->assign('TREElabel', $mbt->getName());
		}
		$userPref = $current_user->getPreference('mb_assist', 'Assistant');
		if(!$userPref) $userPref="na"; 
		$smarty->assign('userPref',$userPref);
		$smarty->display('modules/ModuleBuilder/tpls/index.tpl');
		
 	}
 	
 	
 }
?>
