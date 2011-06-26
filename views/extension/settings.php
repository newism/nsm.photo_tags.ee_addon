<?php

/**
 * View for Control Panel Settings Form
 * This file is responsible for displaying the user-configurable settings for the NSM Multi Language extension in the ExpressionEngine control panel.
 *
 * !! All text and textarea settings must use form_prep($value); !!
 *
 * @package Nsm_photo_tags
 * @version 1.0.0
 * @author Leevi Graham <http://leevigraham.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 **/

$EE =& get_instance();

?>

<div class="mor">
	<?= form_open(
			'C=addons_extensions&M=extension_settings&file=' . $addon_id,
			array('id' => $addon_id . '_prefs'),
			array($input_prefix."[enabled]" => TRUE)
		)
	?>

	<!-- 
	===============================
	Alert Messages
	===============================
	-->

	<?php if($error) : ?>
		<div class="alert error"><?php print($error); ?></div>
	<?php endif; ?>

	<?php if($message) : ?>
		<div class="alert success"><?php print($message); ?></div>
	<?php endif; ?>
	
	<div class="tg">
		<h2>Enable?</h2>
		<table>
			<tbody>
				<tr>
					<th scope="row">Enable?</th>
					<td><?= $EE->nsm_photo_tags_helper->yesNoRadioGroup($input_prefix."[enabled]", $data["enabled"]); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- 
	===============================
	Channel Settings / Mapping
	===============================
	-->

	<?= $channel_data_map; ?>

	<!-- 
	===============================
	Member Settings / Mapping
	===============================
	-->

	<?= $member_data_map; ?>

	<!-- 
	===============================
	Submit Button
	===============================
	-->

	<div class="actions">
		<input type="submit" class="submit" value="<?php print lang('save_extension_settings') ?>" />
	</div>

	<?= form_close(); ?>
</div>