<table class="table table-striped" id="prefixList">
	<thead>
		<tr>
			<th width="10%" class="nowrap">
				<?php echo JText::_('Prefix'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('URL'); ?>
			</th>
			<th width="10%" class="nowrap">
				<span class="pull-right"><?php echo JText::_('Stored Handles'); ?></span>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
		<tr>
			<td width="10%"><?php echo $item->na; ?></td>
			<td><?php echo $item->url; ?></td>
			<td width="10%"><span class="pull-right"><?php echo $item->count; ?></span></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
	
	