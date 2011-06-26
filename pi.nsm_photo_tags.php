<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_photo_tags/config.php';

/**
 * Nsm Photo Tags Plugin
 * 
 * Generally a module is better to use than a plugin if if it has not CP backend
 *
 * @package			NsmPhotoTags
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-photo-tags
 * @see 			http://expressionengine.com/public_beta/docs/development/plugins.html
 */

/**
 * Plugin Info
 *
 * @var array
 */
$plugin_info = array(
	'pi_name' => NSM_PHOTO_TAGS_NAME,
	'pi_version' => NSM_PHOTO_TAGS_VERSION,
	'pi_author' => 'Leevi Graham',
	'pi_author_url' => 'http://leevigraham.com/',
	'pi_description' => 'Plugin description',
	'pi_usage' => "Refer to the included README"
);

class Nsm_photo_tags{

	/**
	 * The return string
	 *
	 * @var string
	 */
	var $return_data = "";

	function Nsm_photo_tags() {
		$EE =& get_instance();
		$this->return_data = "Nsm Photo Tags Output";
	}

}