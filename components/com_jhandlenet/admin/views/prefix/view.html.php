<?php
/**
 * A view that homes a prefix in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * A view that homes a prefix in JHandleNet.
 *
 * @package  JHandleNet
 */
class JHandleNetViewPrefix extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    protected $isNew;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Check for errors.
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
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $canDo = JHandleNetHelper::getActions(0);

        JToolbarHelper::title(JText::_('COM_JHANDLENET_MANAGER_PREFIX_NEW'), 'banners.png');

        // If not checked out, can save the item.
        if ($canDo->get('core.edit')) {
            JToolbarHelper::save('prefix.save');
        }

        if (empty($this->item->na)) {
            JToolbarHelper::cancel('prefix.cancel');
        } else {
            JToolbarHelper::cancel('prefix.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
