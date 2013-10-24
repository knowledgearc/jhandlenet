<?php
/**
 * A template that lists homed prefixes in JHandleNet.
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

defined('_JEXEC') or die('Restricted access');
?>
<table class="table table-striped" id="prefixList">
	<thead>
		<tr>
			<th width="10%" class="nowrap">
				<?php echo JText::_('Prefix'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('URL'); ?>
			</th>
			<th width="10%" class="nowrap">
				<span class="pull-right"><?php echo JText::_('Stored Handles'); ?></span>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
		<tr>
			<td width="10%"><?php echo $item->na; ?></td>
			<td><?php echo $item->url; ?></td>
			<td width="10%"><span class="pull-right"><?php echo $item->count; ?></span></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
	
	