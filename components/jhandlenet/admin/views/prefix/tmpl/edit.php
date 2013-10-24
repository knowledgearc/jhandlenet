<?php
/**
 * A template for homing a prefix in JHandleNet.
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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


JFactory::getDocument()->addScriptDeclaration('
Joomla.submitbutton = function(task)
{
	if (task == "prefix.cancel" || document.formvalidator.isValid(document.id("prefix-form")))
	{
		Joomla.submitform(task, document.getElementById("prefix-form"));
    }
}	
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jhandlenet&layout=edit&na='.$this->item->na); ?>" method="post" name="adminForm" id="prefix-form" class="form-validate">
	<div class="span12 form-horizontal">
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('na'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('na'); ?>
				</div>
			</div>
            
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('url'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('url'); ?>
				</div>
			</div>
		</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>