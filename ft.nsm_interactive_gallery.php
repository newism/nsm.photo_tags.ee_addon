<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_interactive_gallery/config.php';

/**
 * Nsm Interactive Gallery Fieldtype
 *
 * @package			NsmInteractiveGallery
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-example-addon
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 */

class Nsm_interactive_gallery_ft extends EE_Fieldtype
{
	/**
	 * Field info - Required
	 * 
	 * @access public
	 * @var array
	 */
	public $info = array(
		'version'		=> NSM_INTERACTIVE_GALLERY_VERSION,
		'name'			=> NSM_INTERACTIVE_GALLERY_NAME
	);

	public $addon_id		= NSM_INTERACTIVE_GALLERY_ADDON_ID;

	/**
	 * The fieldtype global settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	public $field_type = '';


	public $has_array_data = true;

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct() {
		parent::EE_Fieldtype();
	}

	public function _convertVectorsToDimensions($data)
	{
		$dimensions = array(
						'top' => 0,
						'left' => 0,
						'width' => 0,
						'height' => 0
					);
		$coords = json_decode($data, true);
		$dimensions['top'] = $coords[0][0];
		$dimensions['left'] = $coords[0][1];
		$dimensions['width'] = $coords[1][1]-$coords[0][1];
		$dimensions['height'] = $coords[3][0]-$coords[0][0];
		return $dimensions;
	}

	//----------------------------------------
	// DISPLAY FIELD / CELL / VARIABLE TAG
	//----------------------------------------

	/**
	 * Replaces the custom field tag
	 * 
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) FALSE for single tags
	 * @return string The HTML replacing the tag
	 * 
	 */
	public function replace_tag($data, $params = FALSE, $tagdata = FALSE) {
		$prefix = (isset($params['prefix']) ? $params['prefix'] : 'nsm_ig_');
		$data = $this->_prepData($data);
		$dimensions = $this->_convertVectorsToDimensions($data['coords']);
		foreach($dimensions as $key => $val){
			$variables[$prefix.$key] = $val;
		}
		$tagdata = $this->EE->TMPL->parse_variables_row($tagdata, $variables);
		$tagdata = $this->EE->functions->prep_conditionals($tagdata, $variables);
		
		return $tagdata;
	}

	//----------------------------------------
	// INSTALL FIELDTYPE
	//----------------------------------------

	/**
	 * Install the fieldtype
	 *
	 * @return array The default settings for the fieldtype
	 */
	public function install() {
		return array(
			"target_field" => false
		);
	}



	//----------------------------------------
	// DISPLAY FIELD / CELL / VARIABLE
	//----------------------------------------

	/**
	 * Takes db / post data and parses it so we have the same info to work with every time
	 *
	 * @access private 
	 * @param $data mixed The data we need to prep
	 * @return array The new array of data
	 */
	private function _prepData($data) {

		if ( ! function_exists('json_decode')) {
			$$EE->load->library('Services_json');
		}

		$default_data = array(
			"coords" => "[[10,10],[10,60],[60,60],[60,10]]"
		);

		if(empty($data)) {
			$data = array();
		} elseif(is_string($data)) {
			$data = json_decode($data, true);
		}
		
		$data = $this->_mergeRecursive($default_data, $data);
		return $data;
	}
	
	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @param $input_name String the input name prefix
	 * @param $field_id String The field id - Low variables
	 * @return String The custom field HTML
	 */
	public function display_field($data, $input_name = false, $field_id = false) {
		
		if(!$field_id) {
			$field_id = $this->field_name;
		}

		if(!$input_name) {
			$input_name = $this->field_name;
		}

		$data = $this->_prepData($data);

		$this->_loadResources();
		
		$field_id = $this->settings['field_id'];
		$target_field = $this->settings['target_field'];
		
		if(!isset($this->EE->cache[__CLASS__]['js_custom_field'][$field_id])) {
		
			$js_canvas = <<<JS
$("#field_id_{$field_id}").NSM_InteractiveGallery({
	src_image_field_id: "{$target_field}"
});

JS;
			$this->EE->cp->add_to_foot("<script type='text/javascript' charset='utf-8'>".$js_canvas."</script>");
			$this->EE->cache[__CLASS__]['js_custom_field'][$field_id] = true;
		}

		$output = '<div class="ft nsm_ig_fieldset"
						data-targetField="'.$target_field.'"
						data-thisField="'.$field_id.'"
					>'. 
						'<textarea class="nsm_ig_dataval nd" name="'.$input_name.'[coords]">'.$data['coords'].'</textarea>'.
						'<a href="#field_id_'.$field_id.'" class="nsm_ig_button">Select</a> '.
						'<a href="#field_id_'.$field_id.'" class="nsm_ig_button" data-action="_resetPos">Reset</a>'.
					'</div>';

		return $output;
		
	}

	/**
	 * Displays the cell - MATRIX COMPATIBILITY
	 * 
	 * @access public
	 * @param $data The cell data
	 * @return string The cell HTML
	 */
	public function display_cell($data) {
		return $this->display_field($data, $this->cell_name);
	}

	/**
	 * Displays the Low Variable field
	 * 
	 * @access public
	 * @param $var_data The variable data
	 * @return string The cell HTML
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function display_var_field($var_data) {
		return "Variable content";
	}



	//----------------------------------------
	// DISPLAY FIELD / CELL / VARIABLE SETTINGS
	//----------------------------------------

	/**
	 * Display a global settings page. The current available global settings are in $this->settings.
	 *
	 * @access public
	 * @return string The global settings form HTML
	 */
	public function display_global_settings() {
		return "Global settings";
	}
	
	/**
	 * Default settngs
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _defaultFieldSettings() {
		return array(
			"target_field" => false
		);
	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $settings mixed Not sure what this data is yet :S
	 * @param $field_name mixed The field name="" prefix
	 * @return array Labels and fields
	 */
	private function _displayFieldSettings($settings, $field_name = false) {

		if(!$field_name) {
			$field_name = __CLASS__;
		}
		
		$this->_loadResources();
		
		$EE =& get_instance();
		
		$site_id = $EE->config->item('site_id');
		$group_id = $EE->input->get('group_id');
		
		$get_fields = $EE->db->query("SELECT `field_id`,
			`field_label`,
			`field_name`
		FROM `exp_channel_fields`
		WHERE `field_type` = 'file'
			AND `site_id` = '{$site_id}'
			AND `group_id` = '{$group_id}'
		");
		
		$target_fields = array();
		foreach($get_fields->result_array() as $row){
			$target_fields[ $row['field_id'] ] = $row['field_label'].' ('.$row['field_name'].')';
		}
		
		/* Field Layout */
		$setting_1 = form_dropdown(
							$field_name . "[target_field]", 
							$target_fields,
							$settings['target_field']
						);

		$r[] = array("Target Field", $setting_1);
		return $r;
	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $field_settings array The field settings
	 */
	public function display_settings($field_settings) {
		$field_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $field_settings);
		$rows = $this->_displayFieldSettings($field_settings);

		// add the rows
		foreach ($rows as $row) {
			$this->EE->table->add_row($row[0], $row[1]);
		}
	}

	/**
	 * Display Cell Settings - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings($cell_settings) {
		$cell_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $cell_settings);
		return $this->_displayFieldSettings($cell_settings, $this->addon_id);
	}

	/**
	 * Display Variable Settings - Low Variables
	 * 
	 * @access public
	 * @param $var_settings array The variable settings
	 * @return array Label and form inputs
	 */
	public function display_var_settings($var_settings) {
		$var_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $var_settings);
		return $this->_displayFieldSettings($var_settings);
	}


	//----------------------------------------
	// SAVE FIELD / CELL / VARIABLE SETTINGS
	//----------------------------------------

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array The submitted post data.
	 * @return array Field settings
	 */
	public function save_settings($data) {
		return $field_settings = $this->EE->input->post(__CLASS__);
	}

	/**
	 * Process the cell settings before saving - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The settings for the cell
	 * @return array The new settings
	 */
	public function save_cell_settings($cell_settings) {
		return $cell_settings = $cell_settings[$this->addon_id];
	}

	/**
	 * Save variable settings = LOW Variables
	 * 
	 * @access public
	 * @param $var_settings The variable settings
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function save_var_settings($var_settings) {
		return $this->EE->input->post(__CLASS__);
	}

	//----------------------------------------
	// SAVE FIELD / CELL / VARIABLE
	//----------------------------------------

	/**
	 * Publish form validation
	 * 
	 * @access public
	 * @param $data array Contains the submitted field data.
	 * @return mixed TRUE or an error message
	 */
	public function validate($data) {
		return TRUE;
	}

	/**
	 * Saves the field
	 */
	public function save($data) {
		if(empty($data)) {
			$data = false;
		} elseif(is_array($data)) {
			$data = json_encode($data);
		}

		return $data;
	}

	/**
	 * Save cell data
	 */
	public function save_cell($data) {
		return $this->save($data);
	}


	//----------------------------------------
	// PRIVATE HELPER METHODS
	//----------------------------------------

	/**
	 * Merges any number of arrays / parameters recursively, replacing 
	 * entries with string keys with values from latter arrays. 
	 * If the entry or the next value to be assigned is an array, then it 
	 * automagically treats both arguments as an array.
	 * Numeric entries are appended, not replaced, but only if they are 
	 * unique
	 *
	 * PHP's array_mergeRecursive does indeed merge arrays, but it converts
	 * values with duplicate keys to arrays rather than overwriting the value 
	 * in the first array with the duplicate value in the second array, as 
	 * array_merge does. e.g., with array_mergeRecursive, this happens 
	 * (documented behavior):
	 * array_mergeRecursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     returns: array('key' => array('org value', 'new value'));
	 * 
	 * calling: result = array_mergeRecursive_distinct(a1, a2, ... aN)
	 *
	 * @author <mark dot roduner at gmail dot com>
	 * @link http://www.php.net/manual/en/function.array-merge-recursive.php#96201
	 * @access private
	 * @param $array1, [$array2, $array3, ...]
	 * @return array Resulting array, once all have been merged
	 */
	 private function _mergeRecursive () {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		if(!is_array($base)) $base = empty($base) ? array() : array($base);
	
		foreach($arrays as $append) {
	
			if(!is_array($append)) {
				$append = array($append);
			}
	
			foreach($append as $key => $value) {
				if(!array_key_exists($key, $base) and !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if(is_array($value) or is_array($base[$key])) {
					$base[$key] = $this->_mergeRecursive($base[$key], $append[$key]);
				} else if(is_numeric($key)) {
					if(!in_array($value, $base)) $base[] = $value;
				} else {
					$base[$key] = $value;
				}
			}
		}
	
		return $base;
	}

	/**
	 * Get the current themes URL from the theme folder + / + the addon id
	 * 
	 * @access private
	 * @return string The theme URL
	 */
	private function _getThemeUrl() {
		$EE =& get_instance();
		if(!isset($EE->session->cache[$this->addon_id]['theme_url'])) {
			$theme_url = $EE->config->item('theme_folder_url');
			if (substr($theme_url, -1) != '/') {
				$theme_url .= '/';
			}
			$theme_url .= "third_party/" . $this->addon_id;
			$EE->session->cache[$this->addon_id]['theme_url'] = $theme_url;
		}
		return $EE->session->cache[$this->addon_id]['theme_url'];
	}
	
	/**
	 * Load CSS and JS resources for the fieldtype
	 */
	private function _loadResources() {
		if(!isset($this->EE->cache[__CLASS__]['resources_loaded'])) {
			$theme_url = $this->_getThemeUrl();
			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/custom_field.css' type='text/css' media='screen' charset='utf-8' />");
			// $this->EE->cp->add_to_foot("<script src='//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js' type='text/javascript' charset='utf-8'></script>");
			// $this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/custom_field.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/jquery-nsmInteractiveGallery.js' type='text/javascript' charset='utf-8'></script>");
			//$this->EE->cp->add_to_foot("<script type='text/javascript' charset='utf-8'> $(function(){ {$this->addon_id}.init(); }); </script>");
			
			
			$this->EE->cache[__CLASS__]['resources_loaded'] = true;
		}
	}

}
//END CLASS