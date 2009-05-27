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

class BreadCrumbStack {

	/**
	 * Maintain an ordered list of items in the breadcrumbs
	 *
	 * @var unknown_type
	 */
   private $stack;
   /**
    * Maps an item_id to the position index in stack
    *
    * @var unknown_type
    */
   private $stackMap;
   
   /**
    * BreadCrumbStack
    * Constructor for BreadCrumbStack that builds list of breadcrumbs using tracker table
    * 
    * @param $user_id String value of user id to get bread crumb items for
    * @param $module_name String value of module name to provide extra filtering
    */
   public function BreadCrumbStack($user_id, $module_name='') {
      $this->stack = array();
      $this->stackMap = array();
      $db = DBManagerFactory::getInstance();
      if(!empty($module_name)) {
      	 $module_name = " AND module_name = '$module_name'";
      }
      $query = "SELECT distinct item_id AS item_id, id, item_summary, module_name, monitor_id, date_modified FROM tracker WHERE user_id = '$user_id' AND visible = 1 $module_name ORDER BY date_modified DESC";	
      $history_max_viewed = (!empty($GLOBALS['sugar_config']['history_max_viewed']))? $GLOBALS['sugar_config']['history_max_viewed'] : 10;
      $result = $db->limitQuery($query, 0, $history_max_viewed);
      $items = array();
      while(($row = $db->fetchByAssoc($result))) {	     
      		$items[] = $row;
      }
      $items = array_reverse($items);
      foreach($items as $item) {
      	  $this->push($item);
      }
   }
   
   /**
    * contains
    * Returns true if the stack contains the specified item_id, false otherwise.
    * 
    * @param item_id the item id to search for
    * @return id of the first item on the stack
    */
   public function contains($item_id) {
   	  	if(!empty($this->stackMap)){
   	  		return array_key_exists($item_id, $this->stackMap);
   	  	}else
   	  		return false;
   }
   
   /**
    * Push an element onto the stack.
    * This will only maintain a list of unique item_ids, if an item_id is found to 
    * already exist in the stack, we want to remove it and update the database to reflect it's
    * visibility.
    *
    * @param array $row - a trackable item to store in memory
    */
   public function push($row) {
   	  if(is_array($row) && !empty($row['item_id'])) {
	   	  if($this->contains($row['item_id'])) {
			//if this item already exists in the stack then update the found items
			//to visible = 0 and add our new item to the stack
			$item = $this->stack[$this->stackMap[$row['item_id']]];
	   	  	if(!empty($item['id']) && $row['id'] != $item['id']){
	   	  		$this->makeItemInvisible($item['id'], 0);
	   	  	}
	   	  	$this->popItem($item['item_id']);
	   	  }
	   	  //If we reach the max count, shift the first element off the stack
	   	  $history_max_viewed = (!empty($GLOBALS['sugar_config']['history_max_viewed']))? $GLOBALS['sugar_config']['history_max_viewed'] : 10;

	   	  if($this->length() >= $history_max_viewed) {
	   	  	$this->pop();
	   	  }
	   	  //Push the element into the stack
	   	  $this->addItem($row);
   	  }
   }
   
   /**
    * Pop an item off the stack
    *
    */
   public function pop(){
   		$item = array_shift($this->stack);
   		if(!empty($item['item_id']) && isset($this->stackMap[$item['item_id']])){
   			unset($this->stackMap[$item['item_id']]);
   			$this->heal();
   		}
   }
   
   /**
    * Change the visibility of an item
    *
    * @param int $id
    */
   private function makeItemInvisible($id){
   		$query = "UPDATE tracker SET visible = 0 WHERE id = $id";
        $GLOBALS['db']->query($query, true);
   }
   
   /**
    * Pop an Item off the stack. Call heal to reconstruct the indices properly
    *
    * @param string $item_id - the item id to remove from the stack
    */
   public function popItem($item_id){
   		if(isset($this->stackMap[$item_id])){
   			$idx = $this->stackMap[$item_id];
	   		unset($this->stack[$idx]);
	   		unset($this->stackMap[$item_id]);
	   		$this->heal();
   		}
   }
   
   /**
    * Add an item to the stack
    *
    * @param array $row - the row from the db query
    */
   private function addItem($row){
   		$this->stack[] = $row;
   		$this->stackMap[$row['item_id']] = ($this->length() - 1);
   }
   
   /**
    * Once we have removed an item from the stack we need to be sure to have the 
    * ids and indices match up properly.  Heal takes care of that.  This method should only 
    * be called when an item_id is already in the stack and needs to be removed
    *
    */
   private function heal(){
   		$vals = array_values($this->stack);
   		$this->stack = array();
   		$this->stackMap = array();
   		foreach($vals as $key => $val){
   			$this->addItem($val);
   		}
   }
   
   /**
    * Return the number of elements in the stack
    *
    * @return int - the number of elements in the stack
    */
   public function length(){
   		return count($this->stack);
   }
   
   /**
    * Return the list of breadcrubmbs currently in memory
    *
    * @return array of breadcrumbs
    */
   public function getBreadCrumbList() {
   	  $s = $this->stack;
      return array_reverse($s);
   }
}

?>
