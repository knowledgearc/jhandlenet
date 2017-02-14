<?php
/**
 * JHandleNet master display controller.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * JHandleNet Component Controller
 *
 * @package  JHandleNet
 */
class JHandleNetController extends JControllerLegacy
{
    public function resolve()
    {
        $handle = JFactory::getApplication()->input->getString('handle');

        $item = $this->getModel('Handle')->getItem($handle);

        if (isset($item->data)) {
            $dispatcher = JDispatcher::getInstance();

            JPluginHelper::importPlugin("jhandlenet", null, true, $dispatcher);

            $return = $dispatcher->trigger('onHandleResolve', array($item));

            $url = array_pop($return);

            JFactory::getApplication()->redirect(JRoute::_($url));
        } else {
            JError::raiseWarning(404, JText::_('COM_JHANDLENET_ORPHANED_HANDLE'));
        }
    }

    /**
     * Method to display a view.
     *
     * @param   boolean      If true, the view output will be cached
     * @param   array        An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController  This object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        // At the moment we only have one task; resolve.
        $this->resolve();
    }
}
