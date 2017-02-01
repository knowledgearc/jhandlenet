<?php
/**
 * A view that lists homed prefixes in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * A view that lists homed prefixes in JHandleNet.
 *
 * @package  JHandleNet
 */
class JHandleNetViewPrefixes extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JHANDLENET_MANAGER_PREFIXES'), 'banners.png');

        $user = JFactory::getUser();

        $bar = JToolBar::getInstance('toolbar');

        $canDo = JHandleNetHelper::getActions();

        if (!count($errors = $this->get('Errors')) && $this->get('Dbo')->connected()) {
            if (count($user->authorise('core.create', 'com_jhandlenet')) > 0) {
                JToolbarHelper::addNew('prefix.home');
            }

            if (count($user->authorise('core.delete', 'com_jhandlenet')) > 0) {
                JToolbarHelper::deleteList(JText::_('COM_JHANDLENET_CONFIRM_DELETE'), 'prefixes.delete');
            }
        }

        if ($canDo->get('core.admin')) {
            JToolbarHelper::preferences('com_jhandlenet');
        }

    }
}
