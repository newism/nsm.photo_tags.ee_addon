<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_interactive_gallery/config.php';

/**
 * Nsm Interactive Gallery CP 
 *
 * @package			NsmInteractiveGallery
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 * @see				http://expressionengine.com/public_beta/docs/development/modules.html#control_panel_file
 */

class Nsm_interactive_gallery_mcp{

	public static $addon_id = NSM_INTERACTIVE_GALLERY_ADDON_ID;

	private $pages = array(
		"index"
	);

	public function __construct() {
		$this->EE =& get_instance();
	}

	public function index(){
		$out = $this->EE->load->view("module/index", array(), TRUE);
		return $this->_renderLayout("index", $out);
	}

	public function _renderLayout($page, $out = FALSE) {
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line("{$page}_page_title"));
		$this->EE->cp->set_breadcrumb(self::_route(), $this->EE->lang->line('nsm_exmple_addon_module_name'));

		$nav = array();
		foreach ($this->pages as $page) {
			$nav[lang("{$page}_nav_title")] = self::_route($page);
		}
		$this->EE->cp->set_right_nav($nav);
		return "<div class='mor'>{$out}</div>";
	}

	/**
	 * Build a CP route URL based on params and method
	 *
	 * @access public
	 * @param $method string The method called in the CP
	 * @param $params array Key value pair that will be turned into query params
	 * @param $add_base boolean Add the CP base URL. This can include a session string / subfolders
	 * @retutn string The route URL
	 */
	public static function _route($method = 'index', $params = array(), $add_base = true) {
		$base = ($add_base) ? BASE . AMP : '';
		$params = array_merge(array(
			'C' =>  'addons_modules',
			'M' => 'show_module_cp',
			'module' => NSM_INTERACTIVE_GALLERY_ADDON_ID,
			'method' => $method
		), $params);
		return $base . http_build_query($params);
	}

}