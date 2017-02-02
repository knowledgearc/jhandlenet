<?php
/**
 * JHandleNet master display controller.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * JHandleNet master display controller.
 *
 * @package  JHandleNet
 */
class JHandleNetController extends JControllerLegacy
{
    protected $default_view = 'prefixes';

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
        require_once JPATH_COMPONENT.'/helpers/jhandlenet.php';

        $view   = $this->input->get('view', 'prefixes');
        $layout = $this->input->get('layout', 'default');
        $na     = $this->input->getInt('na');

        // Check for edit form.
        if ($view == 'prefix' && $layout == 'edit' && !$this->checkEditId('com_jhandlenet.edit.prefix', $na)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('COM_JHANDLENET_ERROR_UNHELD_NA', $na));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_jhandlenet&view=prefixes', false));

            return false;
        }

        parent::display($cachable, $urlparams);
    }
}
