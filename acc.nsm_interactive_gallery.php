<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_interactive_gallery/config.php';

/**
 * Nsm Interactive Gallery Accessory
 *
 * @package			NsmInteractiveGallery
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 * @see				http://expressionengine.com/public_beta/docs/development/accessories.html
 */

class Nsm_interactive_gallery_acc 
{
	public $id				= NSM_INTERACTIVE_GALLERY_ADDON_ID;
	public $version			= NSM_INTERACTIVE_GALLERY_VERSION;
	public $name			= NSM_INTERACTIVE_GALLERY_NAME;
	public $description		= 'Example accessory for Nsm Interactive Gallery.';
	public $sections		= array();

	function set_sections() {
		$this->id .= "_acc";
		$this->sections['Title'] = "Content";
	}
}