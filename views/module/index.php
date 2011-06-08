<?php
	/**
	 * Forms open and close tags should always be in the view
	 * which allows for multiple forms per view
	 */
?>
<?= form_open(
		Nsm_interactive_gallery_mcp::_route(
			'index',
			array('param' => 'some_value'),
			false
		), // Form submission URL
		array('form_param' => 'form_param_value'), // Form attributes
		array('hidden_field' => 'hidden_field_value') // Form hidden fields
	)
?>
	<div class="actions">
		<button type="submit" class="submit">Submit</button>
	</div>
<?= form_close(); ?>