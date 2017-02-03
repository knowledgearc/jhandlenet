<?php
/**
 * A template for homing a prefix in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


JFactory::getDocument()->addScriptDeclaration('
Joomla.submitbutton = function(task) {
    if (task == "prefix.cancel" || document.formvalidator.isValid(document.id("prefix-form"))) {
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
        </fieldset>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
