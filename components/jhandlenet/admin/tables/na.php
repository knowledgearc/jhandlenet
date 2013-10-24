<?php 
/**
 * A class representing the "na" table.
 * 
 * @package		JHandleNet
 * @copyright	Copyright (C) 2013 KnowledgeARC Ltd. All rights reserved.
 * @license     This file is part of the JHandleNet component for Joomla!.

   The JHandleNet component for Joomla! is free software: you can redistribute it 
   and/or modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation, either version 3 of the License, 
   or (at your option) any later version.

   The JHandleNet component for Joomla! is distributed in the hope that it will be 
   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with the JHandleNet component for Joomla!.  If not, see 
   <http://www.gnu.org/licenses/>.

 * Contributors
 * Please feel free to add your name and email (optional) here if you have 
 * contributed any source code changes.
 * Name							Email 
 * 
 */
defined('_JEXEC') or die;

class JHandleNetTableNA extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('nas', 'na', $db);
	}
	
	public function store($updateNulls = false)
	{
		$this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		
		return true;
	}
	
	public function exists($keys)
	{
		if (!is_array($keys)) {
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys);
		}
		
		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
		$fields = array_keys($this->getProperties());
	
		foreach ($keys as $field => $value) {
			// Check that $field is in the table.
			if (!in_array($field, $fields)) {
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
			}
			// Add the search tuple to the query.
			$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);
	
		$row = $this->_db->loadAssoc();

		// Check that we have a result.
		if (empty($row)) {
			return false;
		}
	
		// Bind the object with the row and return.
		return true;
	}
}