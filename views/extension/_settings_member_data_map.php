<div class="tg">
	<h2><?= lang("{$input_id}_title"); ?></h2>
	<div class="alert info"><?= lang("{$input_id}_info"); ?></div>
	<table>
		<thead>
			<tr>
				<th scope='col'><?= lang('attribute'); ?></th>
				<th scope='col'><?= lang('member_field'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$count = 0; 
				foreach ($data_map as $attribute => $member_field_id):
				$count++;
			?>
			<tr class='<?= ($count % 2) ? 'odd' : 'even'; ?>'>
				<th scope="row">
					<?= lang("{$input_id}_{$attribute}_label"); ?>
					<?php if (lang("{$input_id}_{$attribute}_note") != "{$input_id}_{$attribute}_note") : ?>
					<div class='note'><?= lang("{$input_id}_{$attribute}_note"); ?></div>
					<?php endif; ?>
				</th>
				<td>
					<?php if(empty($member_fields)) : ?>
						<?= lang('alert.error.no_custom_member_fields'); ?>
						<input type='hidden' name='<?= "{$input_prefix}[{$attribute}]"; ?>' value='' />
					<?php else :
					?>
						<select name='<?= "{$input_prefix}[{$attribute}]"; ?>' id='<?= "{$input_id}_{$attribute}"; ?>'>
							<?php foreach ($member_fields as $field) :
								$selected = ($member_field_id == $field['m_field_id']) ? " selected='selected'" : "";
							?>
							<option<?= $selected?> value="<?= $field['m_field_id'] ?>"><?= $field['m_field_label'] ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
