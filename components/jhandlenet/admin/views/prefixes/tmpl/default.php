<?php
/**
 * A template that lists homed prefixes in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$user = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_jhandlenet&view=prefixes'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container">
        <table class="table table-striped" id="prefixList">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
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
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center hidden-phone">
                        <?php echo JHtml::_('grid.id', $i, $item->na); ?>
                    </td>
                    <td width="10%">
                        <?php echo $item->na; ?>
                    </td>
                    <td><?php echo $item->url; ?></td>
                    <td width="10%"><span class="pull-right"><?php echo $item->count; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>
