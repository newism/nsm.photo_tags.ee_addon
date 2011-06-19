<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_interactive_gallery/config.php';

/**
 * Nsm Interactive Gallery Plugin
 * 
 * Generally a module is better to use than a plugin if if it has not CP backend
 *
 * @package			NsmInteractiveGallery
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 * @see 			http://expressionengine.com/public_beta/docs/development/plugins.html
 */

/**
 * Plugin Info
 *
 * @var array
 */
$plugin_info = array(
	'pi_name' => NSM_INTERACTIVE_GALLERY_NAME,
	'pi_version' => NSM_INTERACTIVE_GALLERY_VERSION,
	'pi_author' => 'Leevi Graham',
	'pi_author_url' => 'http://leevigraham.com/',
	'pi_description' => 'Plugin description',
	'pi_usage' => "Refer to the included README"
);

class Nsm_interactive_gallery{

	/**
	 * The return string
	 *
	 * @var string
	 */
	var $return_data = "";

	function Nsm_interactive_gallery() {
		$EE =& get_instance();
		$this->return_data = "Nsm Interactive Gallery Output";
	}

}