<?php if (! defined('BASEPATH')) die('No direct script access allowed');

require PATH_THIRD.'nsm_photo_tags/config.php';

/**
 * Nsm Photo Tags Tag methods
 *
 * @package			NsmPhotoTags
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-photo-tags
 * @see				http://expressionengine.com/public_beta/docs/development/modules.html#control_panel_file
 */

class Nsm_photo_tags {

	private $addon_id = NSM_PHOTO_TAGS_ADDON_ID;

	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 **/
	function __construct() {
		// set the addon id
		$this->addon_id = strtolower(substr(__CLASS__));
	
		// Create a singleton reference
		$EE =& get_instance();

		// define a constant for the current site_id rather than calling $PREFS->ini() all the time
		if (defined('SITE_ID') == FALSE)
			define('SITE_ID', $EE->config->item('site_id'));

		// Init the cache
		// If the cache doesn't exist create it
		if (! isset($EE->session->cache[$this->addon_id]))
			$EE->session->cache[$this->addon_id] = array();

		// Assig the cache to a local class variable
		$this->cache =& $EE->session->cache[$this->addon_id];
	}

}