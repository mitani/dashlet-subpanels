<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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




require_once('include/Dashlets/DashletGeneric.php');


class MyEmailsDashlet extends DashletGeneric {
    function MyEmailsDashlet($id, $def = null) {
        global $current_user, $app_strings, $dashletData;
		require('modules/Emails/Dashlets/MyEmailsDashlet/MyEmailsDashlet.data.php');

        parent::DashletGeneric($id, $def);

        if(empty($def['title']))
            $this->title = translate('LBL_LIST_TITLE_MY_INBOX', 'Emails').":".translate('LBL_UNREAD_HOME', 'Emails');

        $this->searchFields = $dashletData['MyEmailsDashlet']['searchFields'];
        $this->hasScript = true;  // dashlet has javascript attached to it

        $this->columns = $dashletData['MyEmailsDashlet']['columns'];

        $this->seedBean = new Email();
    }

    function process() {
        global $current_language, $app_list_strings, $image_path, $current_user;
        //$where = 'emails.deleted = 0 AND emails.assigned_user_id = \''.$current_user->id.'\' AND emails.type = \'inbound\' AND emails.status = \'unread\'';
        $mod_strings = return_module_language($current_language, 'Emails');

        $this->filters['assigned_user_id'] = $current_user->id;
        $this->filters['type'] = array("inbound");
        $this->filters['status'] = array("unread");

        $lvsParams = array();
        $lvsParams['custom_select'] = " ,emails_text.from_addr as from_addr ";
        $lvsParams['custom_from'] = " join emails_text on emails.id = emails_text.email_id ";
        parent::process($lvsParams);
    }

    function displayScript() {
        global $current_language;

        $mod_strings = return_module_language($current_language, 'Emails');
        $script = <<<EOQ
        <script>
        function quick_create_overlib(id, theme) {
            return overlib('<a style=\'width: 150px\' class=\'menuItem\' onmouseover=\'hiliteItem(this,"yes");\' onmouseout=\'unhiliteItem(this);\' href=\'index.php?module=Cases&action=EditView&inbound_email_id=' + id + '\'>' +
            "<img border='0' src='themes/" + theme + "/images/Cases.gif' style='margin-right:5px'>" + '{$mod_strings['LBL_LIST_CASE']}' + '</a>' +
            "<a style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' href='index.php?module=Leads&action=EditView&inbound_email_id=" + id + "'>" +
                    "<img border='0' src='themes/" + theme + "/images/Leads.gif' style='margin-right:5px'>"
                    + '{$mod_strings['LBL_LIST_LEAD']}' + "</a>" +
             "<a style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' href='index.php?module=Contacts&action=EditView&inbound_email_id=" + id + "'>" +
                    "<img border='0' src='themes/" + theme + "/images/Contacts.gif' style='margin-right:5px'>"
                    + '{$mod_strings['LBL_LIST_CONTACT']}' + "</a>" +
             "<a style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' href='index.php?module=Bugs&action=EditView&inbound_email_id=" + id + "'>"+
                    "<img border='0' src='themes/" + theme + "/images/Bugs.gif' style='margin-right:5px'>"
                    + '{$mod_strings['LBL_LIST_BUG']}' + "</a>" +
             "<a style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' href='index.php?module=Tasks&action=EditView&inbound_email_id=" + id + "'>" +
                    "<img border='0' src='themes/" + theme + "/images/Tasks.gif' style='margin-right:5px'>"
                   + '{$mod_strings['LBL_LIST_TASK']}' + "</a>"
            , CAPTION, '{$mod_strings['LBL_QUICK_CREATE']}'
            , STICKY, MOUSEOFF, 3000, CLOSETEXT, '<img border=0 src="themes/' + theme + '/images/close_inline.gif">', WIDTH, 150, CLOSETITLE, SUGAR.language.get('app_strings', 'LBL_ADDITIONAL_DETAILS_CLOSE_TITLE'), CLOSECLICK, FGCLASS, 'olOptionsFgClass',
            CGCLASS, 'olOptionsCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olOptionsCapFontClass', CLOSEFONTCLASS, 'olOptionsCloseFontClass');
        }
        </script>
EOQ;
        return $script;
    }
}

?>
