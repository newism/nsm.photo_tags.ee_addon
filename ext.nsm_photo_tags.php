<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_photo_tags/config.php';

/**
 * Nsm Photo Tags Extension
 *
 * @package			NsmPhotoTags
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-photo-tags
 * @see 			http://expressionengine.com/public_beta/docs/development/extensions.html
 */

class Nsm_photo_tags_ext
{
	public $addon_id		= NSM_PHOTO_TAGS_ADDON_ID;
	public $version			= NSM_PHOTO_TAGS_VERSION;
	public $name			= NSM_PHOTO_TAGS_NAME;
	public $description		= 'NSM Photo Tags extension';
	public $docs_url		= '';
	public $settings_exist	= TRUE;
	public $settings		= array();

	// At leaset one hook is needed to install an extension
	// In some cases you may want settings but not actually use any hooks
	// In those cases we just use a dummy hook
	public $hooks = array('dummy_hook_function');

	public $default_site_settings = array(
		'enabled' => TRUE,
		'channels' => array(),
		'member_groups' => array(),
		'channel_data_map' => array(
			'channel_id' => false,
			'fields' => array(
				'attr_1' => false,
				'attr_2' => array(
					'sub_0' => false,
					'sub_1' => false,
					'sub_2' => false,
					'sub_3' => false,
					'sub_4' => false,
					'sub_5' => false,
					'sub_6' => false,
					'sub_7' => false,
				),
				'attr_3' => false
			),
			// key = field_id, value = col_id
			'cols' => array()
		),
		'member_data_map' => array(
			'attr_1' => false,
			'attr_2' => false,
			'attr_3' => false
		)
	);

	private $channel_data_map_config = array(
		'fields' => array(
			'attr_2' => array(
				'matrix' => true,
				'field_selector' => 'sub_0'
			)
		)
	);

	public $default_channel_settings = array();
	public $default_member_group_settings = array();


	// ====================================
	// = Delegate & Constructor Functions =
	// ====================================

	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 **/
	function __construct() {

		$EE =& get_instance();

		// define a constant for the current site_id rather than calling $PREFS->ini() all the time
		if (defined('SITE_ID') == FALSE) {
			define('SITE_ID', $EE->config->item('site_id'));
		}

		// Load the addons model and check if the the extension is installed
		// Get the settings if it's installed
		$EE->load->model('addons_model');
		if($EE->addons_model->extension_installed($this->addon_id)) {
			$this->settings = $this->_getSettings();
		}

		// Init the cache
		$this->_initCache();
	}

	/**
	 * Initialises a cache for the addon
	 * 
	 * @access private
	 * @return void
	 */
	private function _initCache() {

		$EE =& get_instance();

		// Sort out our cache
		// If the cache doesn't exist create it
		if (! isset($EE->session->cache[$this->addon_id])) {
			$EE->session->cache[$this->addon_id] = array();
		}

		// Assig the cache to a local class variable
		$this->cache =& $EE->session->cache[$this->addon_id];
	}






	// ===============================
	// = Hook Functions =
	// ===============================

	public function dummy_hook_function(){}






	// ===============================
	// = Setting Functions =
	// ===============================

	/**
	 * Render the custom settings form and processes post vars
	 *
	 * @access public
	 * @return The settings form HTML
	 */
	public	function settings_form() {

		$EE =& get_instance();
		$EE->lang->loadfile($this->addon_id);
		$EE->load->library($this->addon_id."_helper");
		$EE->load->helper('form');

		// Create the variable array
		$vars = array(
			'addon_id' => $this->addon_id,
			'error' => FALSE,
			'input_prefix' => __CLASS__,
			'message' => FALSE,
		);

		// Are there settings posted from the form?
		if($data = $EE->input->post(__CLASS__)) {

			if(!isset($data["enabled"])) {
				$data["enabled"] = TRUE;
			}

			// No errors ?
			if(! $vars['error'] = validation_errors()) {
				$this->settings = $this->_saveSettings($data);
				$EE->session->set_flashdata('message_success', $this->name . ": ". $EE->lang->line('alert.success.extension_settings_saved'));
				$EE->functions->redirect(BASE.AMP.'C=addons_extensions');
			}
		} else {
			// Sometimes we may need to parse the settings
			$data = $this->settings;
		}

		$vars["data"] = $data;
		$vars["channel_data_map"] = $this->_settings_form_channel_data_map($this->settings['channel_data_map']);
		$vars["member_data_map"] = $this->_settings_form_member_data_map($this->settings['member_data_map']);

		// Return the view.
		return $EE->load->view('extension/settings', $vars, TRUE);
	}

	/**
	 * Data Mapping to custom fields
	 * 
	 * @access private
	 * @param $data_map array Multi-dimensional array
	 */
	private function _settings_form_channel_data_map(array $data_map) {

		$EE =& get_instance();
		$EE->load->library($this->addon_id."_helper");

		$sorted_fields = array();

		$vars = array(
			'input_id' => $this->addon_id."_channel_data_map",
			'input_prefix' => __CLASS__."[channel_data_map]",
			'data_map' => $data_map
		);

		$vars['channels'] = $EE->channel_model->get_channels()->result();
		$EE->load->model("field_model");
		$field_query = $EE->db->query("SELECT 
											w.channel_id as channel_id, 
											wf.group_id as group_id, 
											wf.field_id as field_id, 
											wf.field_label as field_label, 
											wf.field_type as field_type
										FROM `exp_channels` as w
										INNER JOIN `exp_channel_fields` as wf
										ON w.field_group = wf.group_id
										WHERE w.site_id = ".SITE_ID."
										ORDER BY w.channel_id, wf.group_id, wf.field_order"
									);

		if($field_query->num_rows > 0) {
			foreach ($field_query->result() as $field) {
				$sorted_fields[$field->channel_id][] = $field;
			}
		}

		$js = "$('#{$vars['input_id']}_channel_id').NSM_AttributeAssigner({prefix: '{$vars['input_id']}', cf_data: ". json_encode($sorted_fields) . "});";

		if(isset($data_map['channel_id'])) {
			foreach ($data_map['fields'] as $field_name => $mapped_field_id) {
				if(!is_array($mapped_field_id)) {
					$js .= "$('#{$vars['input_id']}_{$field_name}').val('{$mapped_field_id}');";
				} else {
					foreach($mapped_field_id as $sub_name => $sub_field_id) {
						$js .= "$('#{$vars['input_id']}_{$field_name}_{$sub_name}').val('{$sub_field_id}');";
					}
				}
			}
		}

		$EE->nsm_photo_tags_helper->addJS($js, array("file"=>FALSE));
		return $EE->load->view('extension/_settings_channel_data_map', $vars, TRUE);
	}

	/**
	 * Data Mapping to member fields
	 * 
	 * @access private
	* @param array $data_map The attributes we need to make, e.g array("mobile_phone_field_id" => "12", "limit" => "23")
	 */
	private function _settings_form_member_data_map(array $data_map) {

		$EE =& get_instance();
		$EE->load->library($this->addon_id."_helper");

		$vars = array(
			'input_id' => $this->addon_id."_member_data_map",
			'input_prefix' => __CLASS__."[member_data_map]",
			'data_map' => $data_map
		);

		$vars['member_fields'] = array();

		$EE->db->from('member_fields');
		$EE->db->order_by('m_field_order');
		$member_fields_query = $EE->db->get();

		if($member_fields_query->num_rows > 0) {
			foreach ($member_fields_query->result_array() as $field) {
				$vars['member_fields'][$field['m_field_id']] = $field;
			}
		}

		return $EE->load->view('extension/_settings_member_data_map', $vars, TRUE);
	}

	/**
	 * Builds default settings for the site
	 *
	 * @access private
	 * @param int $site_id The site id
	 * @param array The default site settings
	 */
	private function _buildDefaultSiteSettings($site_id = FALSE) {

		$EE =& get_instance();
		$default_settings = $this->default_site_settings;

		// No site id, use the current one.
		if(!$site_id) {
			$site_id = SITE_ID;
		}

		// Channel preferences (if required)
		if(isset($this->default_settings["channels"])) {
			$channels = $EE->channel_model->get_channels($site_id);
			if ($channels->num_rows() > 0) {
				foreach($channels->result() as $channel) {
					$default_settings['channels'][$channel->channel_id] = $this->_buildChannelSettings($channel->channel_id);
				}
			}
		}

		// Member group settings (if required)
		if(isset($this->default_settings["member_groups"])) {
			$member_groups = $EE->member_model->get_member_groups();
			if ($member_groups->num_rows() > 0) {
				foreach($member_groups->result() as $member_group) {
					$default_settings['member_groups'][$member_group->group_id] = $this->_buildMemberGroupSettings($member_group->group_id);
				}
			}
		}

		// return settings
		return $default_settings;
	}

	/**
	 * Build the default channel settings
	 *
	 * @access private
	 * @param array $channel_id The target channel
	 * @return array The new channel settings
	 */
	private function _buildChannelSettings($channel_id) {
		return $this->default_channel_settings;
	}

	/**
	 * Build the default member group settings
	 *
	 * @access private
	 * @param array $group_id The target group
	 * @return array The new member group settings
	 */
	private function _buildMemberGroupSettings($group_id) {
		return $this->default_member_group_settings;
	}




	// ===============================
	// = Class and Private Functions =
	// ===============================

	/**
	 * Called by ExpressionEngine when the user activates the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function activate_extension() {
		$this->_createSettingsTable();
		$this->settings = $this->_getSettings();
		$this->_registerHooks();
	}

	/**
	 * Called by ExpressionEngine when the user disables the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function disable_extension() {
		$this->_unregisterHooks();
	}

	/**
	 * Called by ExpressionEngine updates the extension
	 *
	 * @access public
	 * @return void
	 **/
	public function update_extension($current=FALSE){}





	// ======================
	// = Settings Functions =
	// ======================

	/**
	 * The settings table
	 *
	 * @access		private
	 **/
	private static $settings_table = 'nsm_addon_settings';

	/**
	 * The settings table fields
	 *
	 * @access		private
	 **/
	private static $settings_table_fields = array(
		'id'						=> array(	'type'			 => 'int',
												'constraint'	 => '10',
												'unsigned'		 => TRUE,
												'auto_increment' => TRUE,
												'null'			 => FALSE),
		'site_id'					=> array(	'type'			 => 'int',
												'constraint'	 => '5',
												'unsigned'		 => TRUE,
												'default'		 => '1',
												'null'			 => FALSE),
		'addon_id'					=> array(	'type'			 => 'varchar',
												'constraint'	 => '255',
												'null'			 => FALSE),
		'settings'					=> array(	'type'			 => 'mediumtext',
												'null'			 => FALSE)
	);
	
	/**
	 * Creates the settings table table if it doesn't already exist.
	 *
	 * @access		protected
	 * @return		void
	 **/
	protected function _createSettingsTable() {
		$EE =& get_instance();
		$EE->load->dbforge();
		$EE->dbforge->add_field(self::$settings_table_fields);
		$EE->dbforge->add_key('id', TRUE);

		if (!$EE->dbforge->create_table(self::$settings_table, TRUE)) {
			show_error("Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$settings_table);
			log_message('error', "Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$settings_table);
		}
	}

	/**
	 * Get the addon settings
	 *
	 * 1. Load settings from the session
	 * 2. Load settings from the DB
	 * 3. Create new settings and save them to the DB
	 * 
	 * @access private
	 * @param boolean $refresh Load the settings from the DB not the session
	 * @return mixed The addon settings 
	 */
	private function _getSettings($refresh = FALSE) {

		$EE =& get_instance();
		$settings = FALSE;

		if (
			// if there are settings in the settings cache
			isset($this->cache[SITE_ID]['settings']) === TRUE 
			// and we are not forcing a refresh
			AND $refresh != TRUE
		) {
			// get the settings from the session cache
			$settings = $this->cache[SITE_ID]['settings'];
		} else {
			$settings_query = $EE->db->get_where(
									self::$settings_table,
									array(
										'addon_id' => $this->addon_id,
										'site_id' => SITE_ID
									)
								);
			// there are settings in the DB
			if ($settings_query->num_rows()) {

				if ( ! function_exists('json_decode')) {
					$$EE->load->library('Services_json');
				}

				$settings = json_decode($settings_query->row()->settings, TRUE);
				$this->_saveSettingsToSession($settings);
				log_message('info', __CLASS__ . " : " . __METHOD__ . ' getting settings from session');
			}
			// no settings for the site
			else {
				$settings = $this->_buildDefaultSiteSettings(SITE_ID);
				$this->_saveSettings($settings);
				log_message('info', __CLASS__ . " : " . __METHOD__ . ' creating new site settings');
			}
			
		}

		// Merge config settings
		foreach ($settings as $key => $value) {
			if($EE->config->item($this->addon_id . "_" . $key)) {
				$settings[$key] = $EE->config->item($this->addon_id . "_" . $key);
			}
		}

		return $settings;
	}

	/**
	 * Get the channel settings if the exist or load defaults
	 *
	 * @access private
	 * @param int $channel_id The channel id
	 * @return array the channel settings
	 */
	private function _channelSettings($channel_id){
		return (isset($this->settings["channels"][$channel_id]))
					? $this->settings["channels"][$channel_id]
					: $this->_buildChannelSettings($channel_id);
	}

	/**
	 * Get the member group settings if the exist or load defaults
	 *
	 * @access private
	 * @param int $group_id The member group id
	 * @return array the member group settings
	 */
	private function _memberGroupSettings($group_id){
		return (isset($this->settings["member_groups"][$group_id]))
					? $this->settings["member_groups"][$group_id]
					: $this->_buildMemberGroupSettings($group_id);
	}

	/**
	 * Save settings to DB and to the session
	 *
	 * @access private
	 * @param array $settings
	 */
	private function _saveSettings($settings) {
		$this->_saveSettingsToDatabase($settings);
		$this->_saveSettingsToSession($settings);
		return $settings;
	}

	/**
	 * Save settings to DB
	 *
	 * @access private
	 * @param array $settings
	 * @return array The settings
	 */
	private function _saveSettingsToDatabase($settings) {

		$EE =& get_instance();
		$EE->load->library('javascript');

		$data = array(
			'settings'	=> $EE->javascript->generate_json($settings, true),
			'addon_id'	=> $this->addon_id,
			'site_id'	=> SITE_ID
		);
		$settings_query = $EE->db->get_where(
							'nsm_addon_settings',
							array(
								'addon_id' =>  $this->addon_id,
								'site_id' => SITE_ID
							), 1);

		if ($settings_query->num_rows() == 0) {
			$query = $EE->db->insert('exp_nsm_addon_settings', $data);
			log_message('info', __METHOD__ . ' Inserting settings: $query => ' . $query);
		} else {
			$query = $EE->db->update(
							'exp_nsm_addon_settings',
							$data,
							array(
								'addon_id' => $this->addon_id,
								'site_id' => SITE_ID
							));
			log_message('info', __METHOD__ . ' Updating settings: $query => ' . $query);
		}
		return $settings;
	}

	/**
	 * Save the settings to the session
	 *
	 * @access private
	 * @param array $settings The settings to push to the session
	 * @return array the settings unmodified
	 */
	private function _saveSettingsToSession($settings) {
		$this->cache[SITE_ID]['settings'] = $settings;
		return $settings;
	}




	// ======================
	// = Hook Functions     =
	// ======================

	/**
	 * Sets up and subscribes to the hooks specified by the $hooks array.
	 *
	 * @access private
	 * @param array $hooks A flat array containing the names of any hooks that this extension subscribes to. By default, this parameter is set to FALSE.
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _registerHooks($hooks = FALSE) {

		$EE =& get_instance();

		if($hooks == FALSE && isset($this->hooks) == FALSE) {
			return;
		}

		if (!$hooks) {
			$hooks = $this->hooks;
		}

		$hook_template = array(
			'class'    => __CLASS__,
			'settings' => "a:0:{}",
			'version'  => $this->version,
		);

		foreach ($hooks as $key => $hook) {
			if (is_array($hook)) {
				$data['hook'] = $key;
				$data['method'] = (isset($hook['method']) === TRUE) ? $hook['method'] : $key;
				$data = array_merge($data, $hook);
			} else {
				$data['hook'] = $data['method'] = $hook;
			}

			$hook = array_merge($hook_template, $data);
			$EE->db->insert('exp_extensions', $hook);
		}
	}

	/**
	 * Removes all subscribed hooks for the current extension.
	 * 
	 * @access private
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _unregisterHooks() {
		$EE =& get_instance();
		$EE->db->where('class', __CLASS__);
		$EE->db->delete('exp_extensions'); 
	}
}