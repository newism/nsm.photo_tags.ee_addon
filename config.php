<?php

/**
 * Config file for Nsm Interactive Gallery
 *
 * @package			NsmInteractiveGallery
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 */

if(!defined('NSM_INTERACTIVE_GALLERY_VERSION')) {
	define('NSM_INTERACTIVE_GALLERY_VERSION', '0.0.1');
	define('NSM_INTERACTIVE_GALLERY_NAME', 'Nsm Interactive Gallery');
	define('NSM_INTERACTIVE_GALLERY_ADDON_ID', 'nsm_interactive_gallery');
}

$config['name'] 	= NSM_INTERACTIVE_GALLERY_NAME;
$config["version"] 	= NSM_INTERACTIVE_GALLERY_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://github.com/newism/nsm.example_addon.ee_addon/raw/master/versions.xml';
