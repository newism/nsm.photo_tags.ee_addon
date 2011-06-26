<?php

/**
 * Config file for Nsm Photo Tags
 *
 * @package			NsmPhotoTags
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-photo-tags
 */

if(!defined('NSM_PHOTO_TAGS_VERSION')) {
	define('NSM_PHOTO_TAGS_VERSION', '0.0.1');
	define('NSM_PHOTO_TAGS_NAME', 'Nsm Photo Tags');
	define('NSM_PHOTO_TAGS_ADDON_ID', 'nsm_photo_tags');
}

$config['name'] 	= NSM_PHOTO_TAGS_NAME;
$config["version"] 	= NSM_PHOTO_TAGS_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://github.com/newism/nsm.photo_tags.ee_addon/raw/master/versions.xml';
