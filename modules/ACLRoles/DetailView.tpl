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


<form action="index.php" method="post" name="DetailView" id="form">

			<input type="hidden" name="module" value="ACLRoles">
			<input type="hidden" name="user_id" value="">
			<input type="hidden" name="record" value="{$ROLE.id}">
			<input type="hidden" name="isDuplicate" value=''>
			<input type='hidden' name='return_record' value='{$RETURN.record}'>
			<input type='hidden' name='return_action' value='{$RETURN.action}'>
			<input type='hidden' name='return_module' value='{$RETURN.module}'>
			<input type="hidden" name="action">

			
		<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="button" onclick="this.form.action.value='EditView'" type="submit" name="button" value="  {$APP.LBL_EDIT_BUTTON} "> <input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="button" onclick="this.form.return_module.value='ACLRoles'; this.form.return_action.value='index'; this.form.isDuplicate.value='1'; this.form.action.value='EditView'" type="submit" name="button" value=" {$APP.LBL_DUPLICATE_BUTTON} "> <input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="button" onclick="this.form.return_module.value='ACLRoles'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="button" value=" {$APP.LBL_DELETE_BUTTON} ">
		</form>
		</p>
		<p>
		<TABLE width='100%' class='detail view' border='0' cellpadding=0 cellspacing = 1  >
		<TR>
<td valign='top' width='15%' align='right'><b>{$MOD.LBL_NAME}:</b></td><td width='85%' colspan='3'>{$ROLE.name}</td>
</tr
><TR>
<td valign='top'  width='15%' align='right'><b>{$MOD.LBL_DESCRIPTION}:</b></td><td colspan='3' valign='top'  width='85%' align='left'>{$ROLE.description | nl2br}</td>
</tr></table>
</p>
		<p>
		
{include file="modules/ACLRoles/EditViewBody.tpl" }
