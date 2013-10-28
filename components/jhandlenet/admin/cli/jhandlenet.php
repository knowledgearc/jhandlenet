#!/usr/bin/php
<?php
/**
 * @package JHandleNet
 * @copyright Copyright (C) 2013 KnowledgeARC Ltd. All rights reserved.
 */

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER)) die();

/**
 * This is a CRON script which should be called from the command-line, not the
 * web. For example something like:
 * /usr/bin/php /path/to/site/cli/jcrawl.php
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);

// Load system defines
if (file_exists(dirname(dirname(__FILE__)) . '/defines.php')) {
        require_once dirname(dirname(__FILE__)) . '/defines.php';
}

if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(dirname(__FILE__)));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
	require_once JPATH_LIBRARIES . '/import.legacy.php';	
else
	require_once JPATH_LIBRARIES . '/import.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';


if (version_compare(JVERSION, "3.0", "l")) {
	// Force library to be in JError legacy mode
	JError::$legacy = true;
	
	// Import necessary classes not handled by the autoloaders
	jimport('joomla.application.menu');
	jimport('joomla.environment.uri');
	jimport('joomla.event.dispatcher');
	jimport('joomla.utilities.utility');
	jimport('joomla.utilities.arrayhelper');	
}

// System configuration.
$config = new JConfig;

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

// Load Library language
$lang = JFactory::getLanguage();

// Try the finder_cli file in the current language (without allowing the loading of the file in the default language)
$lang->load('jhandlenet_cli', JPATH_SITE, null, false, false)
// Fallback to the finder_cli file in the default language
|| $lang->load('jhandlenet_cli', JPATH_SITE, null, true);

jimport('joomla.application.component.helper');
jimport('joomla.log.log');
 
/**
 * Simple command line interface application class.
 *
 * @package JHandleNet.CLI
 */
class JHandleNet extends JApplicationCli
{
	private $db;
	
	public function __construct($input = null, JRegistry $config = null, JEventDispatcher $dispatcher = null) 
	{
		parent::__construct($input, $config, $dispatcher);
		
		
		$params = JComponentHelper::getParams('com_jhandlenet');
		
		$option['driver']   = 'mysqli';
		$option['host']     = $params->get('host').':'.$params->get('port');
		$option['user']     = $params->get('username');
		$option['password'] = $params->get('password');
		$option['database'] = $params->get('database');
		$option['prefix']   = '';
		
		$db = JDatabaseDriver::getInstance($option);
		
		$this->setDbo($db);
	}
	
    public function doExecute()
    { 	
    	// fool the system into thinking we are running as JSite with JHandleNet as the active component
		JFactory::getApplication('site');
		$_SERVER['HTTP_HOST'] = 'domain.com';

		// Disable caching.
		$config = JFactory::getConfig();
		$config->set('caching', 0);
		$config->set('cache_handler', 'file');
		
		try {
			// home a prefix
		    if ($this->input->get('home')) {
		    	$username = $this->input->get('username', null, 'string');

		    	
		    	$password = $this->input->get('password', null, 'string');

    			$this->home(
    				$this->input->get('home', null, 'string'),
    				JArrayHelper::getValue($this->input->args, 0),
    				$this->input->get('archive', null, 'string'),
    				$username,
    				$password);
    			return;
	    	}
	    	
	    	// unhome a prefix
	    	if ($this->input->get('unhome')) {
	    		$this->unhome($this->input->get('unhome'));
	    		return;
	    	}
	    	
	    	// rebuild prefix handle index.
	    	if ($this->input->get('r') || $this->input->get('rebuild')) {
	    		$na = $this->input->get('r', $this->input->get('rebuild'));
	    		
	    		$this->rebuild($na);
	    		return;
	    	}
	    	
	    	// purge handles for prefix.
	    	if ($this->input->get('p') || $this->input->get('purge')) {
	    		$na = $this->input->get('p', $this->input->get('purge'));
	    		$this->purge($na);
	    		return;
	    	}
	    	
	    	// update handles for prefix, creating handles for new records.
	    	if ($this->input->get('u') || $this->input->get('update')) {
	    		$na = $this->input->get('u', $this->input->get('update'));
	    		$this->update($na);
	    		return;
	    	}
	    	
	    	
	    	// clean handles for prefix, clean handles for records that don't 
	    	//exist.
	    	if ($this->input->get('c') || $this->input->get('clean')) {
	    		$na = $this->input->get('c', $this->input->get('clean'));	    		 
	    		$this->update($na);
	    		return;
	    	}
	    	
			// build handles for prefix, creating handles for new records and 
			// cleaning non-existent handles.
	    	if ($this->input->get('b') || $this->input->get('build')) {
	    		$na = $this->input->get('b', $this->input->get('build'));	    		 
	    		$this->build($na);
	    		return;
	    	}
	    	
	    	// help/catchall
	    	$this->help();	    	
		} catch (Exception $e) {
			$this->out('ERROR: '.$e->getMessage());			
		}
    }
    
    public function out($text = '', $nl = true)
    {
    	if (!($this->input->get('q', false) || $this->input->get('quiet', false))) {
    		parent::out($text, $nl);
    	}
    	
    	return $this;
    }
    
    public function home($na, $url, $endpoint = null, $username = null, $password = null)
    {
    	if (!$na) {
    		$this->out('No naming authority specified.');
    		return;
    	}
    	 
    	if (!$url) {
    		$this->out('No url specified');
    		return;
    	}
    
    	$table = $this->getTable();
    
    	if ($table->load($na)) {
    		$this->out(JText::sprintf('Cannot home handle prefix %s. Already exists.', $na));
    	} else {
    		$table->na = $na;
    		$table->url = $url;
    		$table->archive_endpoint = $endpoint;
    		$table->archive_username = $username;
    		$table->archive_password = $password;
    
    		if ($table->store()) {
    			if ($this->input->get('v') || $this->input->get('verbose')) {
    				$this->out(JText::sprintf('Handle prefix %s homed.', $na));
    			}
    		}
    	}
    }
    
    public function unhome($na)
    {
    	if (!$na) {
    		$this->out('No naming authority specified.');
    		return;
    	}

		$table = $this->getTable();
    	
    	if ($table->load($na)) {    		
    		if ($table->delete()) {
    			if ($this->input->get('v') || $this->input->get('verbose')) {
    				$this->out(JText::sprintf('Handle prefix %s unhomed.', $na));
    			}
    		}
    	} else {
    		$this->out(JText::sprintf("Cannot unhome handle prefix %s. Prefix doesn't exists.", $na));
    	}
    }
    
    public function rebuild($na)
    {
    	if (!$na) {
    		$this->out('Cannot rebuild handles using an empty prefix.');
    		return;
    	}
    	
    	$this->purge($na);

    	try {
    		$this->_fireEvent('onHandlesCreate', array($na));
    	} catch (Exception $e) {    		
    		$this->out($e->getMessage());
    	}
    }
    
    public function update($na)
    {
    	if (!$na) {
    		$this->out('Cannot update handles using an empty prefix.');
    		return;
    	}

    	try {
    		$this->_fireEvent('onHandlesUpdate', array($na));
    	} catch (Exception $e) {
    		$this->out($e->getMessage());
    	}
    }
    
    public function clean($na)
    {
    	if (!$na) {
    		$this->out('Cannot update clean using an empty prefix.');
    		return;
    	}

    	try {
    		$this->_fireEvent('onHandlesClean', array($na));
    	} catch (Exception $e) {
    		$this->out($e->getMessage());
    	}
    }    
    
    public function purge($na)
    {
    	if (!$na) {
    		$this->out('No prefix to purge.');
    		return;
    	}
    	
    	$db = $this->getDbo();
    	
    	$query = $db->getQuery(true);
    	
    	$query
    		->delete('handles')
    		->where('na='.$na);
    	
    	$db->setQuery($query);
    	$db->execute();
    }
    
    public function help()
    {
echo <<<EOT
Usage: jhandlenet [options] arg na
   jhandlenet [options] --home na [param]=[value] url

Manage handles within a universal handle.net database.
When homing a naming authority use the Homing parameters to specify additional 
homing-specific settings. 
   
  -b, --build         Equivalent of running --clean then --update.
  -c, --clean         Clean out orphaned handles from the handle.net database.
  -h, --help          Display this help and exit.
  --home              Home (create) a naming authority.
  -p, --purge         Purge all handles for a particular naming authority.
  -q, --quiet         Suppress all output.
  -r, --rebuild       Delete handles then re-create.
  --unhome            Unhome (delete) a naming authority.
  -u, --update        Create handles that don't exist.
  -v, --verbose       Display verbose information about the current action.
Homing:
  --archive           Specify an archive.
  --username          Specify the archive username (if applicable).
  --password          Specify the archive password (if applicable).

EOT;
    }
    
    public function setDbo($db)
    {
    	$this->db = $db;
    }
    
    public function getDbo()
    {
    	return $this->db;
    }
    
    private function _fireEvent($name, $args = array())
    {
    	$dispatcher = JDispatcher::getInstance();
    	 
    	JPluginHelper::importPlugin("jhandlenet", null, true, $dispatcher);

    	return $dispatcher->trigger($name, $args);
    }
    
    public function getTable()
    {
    	JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jhandlenet/tables');
    	return JTable::getInstance('NA', 'JHandleNetTable', array('dbo'=>$this->getDbo()));
    }
}
 
JApplicationCli::getInstance('JHandleNet')->execute();