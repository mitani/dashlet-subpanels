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
 * *******************************************************************************/
$viewdefs['Cases']['EditView'] = array(
    'templateMeta' => array('maxColumns' => '2', 
                            'widths' => array(
                                            array('label' => '10', 'field' => '30'), 
                                            array('label' => '10', 'field' => '30')
                                            ),
                           ),
    'panels' => array (

  'default' => 
  array(
	  array (
	    array('name'=>'case_number', 'type'=>'readonly', 'displayParams'=>array('required'=>true)) ,




	  ),
	  
	  array (
	    'priority',
	    'assigned_user_name',
	  ),
	  
	  array (
	    'status',
	    'account_name',
	  ),
	  
	  array (
	      'type',
	  ),
	  array (
	    array (
	      'name' => 'name',
	      'displayParams' => array ('size'=>75, 'required'=>true)
	    ),
	  ),
	  
	  array (
	    
	    array (
	      'name' => 'description',
	      'displayParams' => 
	      array (
	        'rows' => '8',
	        'cols' => '80',
	      ),
	      'nl2br' => true,
	    ),
	  ),
	  
	  array (
	    
	    array (
	      'name' => 'resolution',
	      'displayParams' => 
	      array (
	        'rows' => '5',
	        'cols' => '80',
	      ),
	      'nl2br' => true,
	    ),
	  ),
	
















	)
),

  
);
?>
