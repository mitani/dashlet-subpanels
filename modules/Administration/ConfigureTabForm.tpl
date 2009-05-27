{*

/**
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
 */



*}


<form name="EditView" id='EditView' method="POST" action="index.php">
<input type="hidden" name="module" value="Administration">
<input type="hidden" name="action">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
			

		<td><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="studiotabs.generateGroupForm('EditView');this.form.action.value='SaveTabs'; " type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " > <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="this.form.action.value='{$RETURN_ACTION}'; this.form.module.value='{$RETURN_MODULE}';" type="submit" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  "></td>
</tr><tr>		
		<td style="padding-bottom: 2px;" valign='top'><input type='checkbox' name='user_edit_tabs' value=1 class='checkbox' {if !empty($user_can_edit)}CHECKED{/if}>&nbsp;<b onclick='document.EditView.user_edit_tabs.checked= !document.EditView.user_edit_tabs.checked' style='cursor:default'>{$MOD.LBL_ALLOW_USER_TABS}</b>
	
</tr>
</table>
