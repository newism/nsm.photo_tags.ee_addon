<div class="tg">
	<h2><?= lang("{$input_id}_title"); ?></h2>
	<div class="alert info"><?= lang("{$input_id}_info"); ?></div>
	<table>
		<tbody>
			<tr class="even">
				<th scope="row"><?= lang("{$input_id}_channel_id_label"); ?></th>
				<td>
					<?php if (empty($channels)) : ?>
						<?= lang('no_channels_msg'); ?>
						<input type='hidden' name='<?= "{$input_prefix}[channel_id]"; ?>' value='' />
					<?php else: ?>
						<select name='<?= "{$input_prefix}[channel_id]"; ?>' id='<?= "{$input_id}_channel_id"; ?>'>
						<?php 
							foreach($channels as $channel) : 
							$selected = ($data_map["channel_id"] == $channel->channel_id) ? " selected='selected'" : "";
						?>
							<option value='<?= $channel->channel_id ?>'<?= $selected ?>><?= $channel->channel_title ?></option>
						<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table>
		<thead>
			<tr>
				<th scope='col' colspan="2"><?= lang('attribute'); ?></th>
				<th scope='col'><?= lang('channel_field'); ?></th>
				<th scope='col'><?= lang('matrix_col'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$attributeCount = 0;
				foreach ($data_map['fields'] as $attribute => $custom_field):
				$attributeCount++;
				$subRowRount = 0;
				$subElements = (is_array($custom_field)) ? count($custom_field) : false;
			?>
			<tr class='<?= ($attributeCount % 2) ? 'odd' : 'even'; ?>'>

				<?php /* Row Heading */ ?>
				<th scope="row" rowspan="<?= $subElements ?>" colspan="<?= ($subElements) ? 1 : 2 ?>">
					<?= lang("{$input_id}_{$attribute}_label"); ?>
					<?php if (lang("{$input_id}_{$attribute}_note") != "{$input_id}_{$attribute}_note") : ?>
					<div class='note'><?= lang("{$input_id}_{$attribute}_note"); ?></div>
					<?php endif; ?>
				</th>

				<?php 
					/* Standard row */
					if(!$subElements) :
				?>
					<td>
						<select name='<?= "{$input_prefix}[fields][{$attribute}]"; ?>' id='<?= "{$input_id}_{$attribute}"; ?>'></select>
						<span class='highlight no-custom-field-group-error'><?= lang('alert.error.no_custom_field_groups'); ?></span>
					</td>
					<td>
						<select name='<?= "{$input_prefix}[cols][{$attribute}]"; ?>'></select>
					</td>
				</tr>
				<?php 
					/* Sub rows */
					else :
				?>
					<?php foreach ($custom_field as $sub_key => $sub_value) : ?>
					<?php if($subRowRount) : ?><tr class='<?= ($attributeCount % 2) ? 'odd' : 'even'; ?>'><?php endif; ?>
						<th class="sub-heading"><?= lang("{$input_id}_{$attribute}_{$sub_key}_label"); ?></th>
						<td>
							<select name='<?= "{$input_prefix}[fields][{$attribute}][{$sub_key}]"; ?>' id='<?= "{$input_id}_{$attribute}_{$sub_key}"; ?>'></select>
							<span class='highlight no-custom-field-group-error'><?= lang('alert.error.no_custom_field_groups'); ?></span>
						</td>
						<td>
							<select name='<?= "{$input_prefix}[cols][{$attribute}]"; ?>'></select>
						</td>
					</tr>
					<?php $subRowRount++; endforeach; ?>
				
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>