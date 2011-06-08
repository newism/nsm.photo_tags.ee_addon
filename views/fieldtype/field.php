<div class="mor cf">
	<div class="tg">
		<h2><?= $title ?></h2>
		<table class="data">
			<thead>
				<tr>
					<th scope="col">Heading 1</th>
					<th scope="col">Heading 2</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row">Field 1</th>
					<td><input 
						type="text"
						name="<?= $input_prefix ?>[value_1]"
						value="<?= form_prep($data['value_1']) ?>"
					 /></td>
				</tr>
				<tr>
					<th scope="row">Field 2</th>
					<td><input 
						type="text"
						name="<?= $input_prefix ?>[value_2]"
						value="<?= form_prep($data['value_2']) ?>"
					 /></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>