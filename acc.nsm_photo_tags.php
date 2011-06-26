<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_photo_tags/config.php';

/**
 * Nsm Photo Tags Accessory
 *
 * @package			NsmPhotoTags
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-photo-tags
 * @see				http://expressionengine.com/public_beta/docs/development/accessories.html
 */

class Nsm_photo_tags_acc 
{
	public $id				= NSM_PHOTO_TAGS_ADDON_ID;
	public $version			= NSM_PHOTO_TAGS_VERSION;
	public $name			= NSM_PHOTO_TAGS_NAME;
	public $description		= 'Example accessory for Nsm Photo Tags.';
	public $sections		= array();

	function set_sections() {
		$this->id .= "_acc";
		$this->sections['Title'] = "Content";
	}
}