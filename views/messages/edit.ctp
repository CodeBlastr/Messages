<div class="userMessages form">
<?php echo $this->Form->create('UserMessage');?>
	<fieldset>
 		<legend><?php __('Edit User Message'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('from_id');
		echo $this->Form->input('recipient_id');
		echo $this->Form->input('title');
		echo $this->Form->input('body');
		echo $this->Form->input('is_read');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('UserMessage.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('UserMessage.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List User Messages', true), array('action' => 'index'));?></li>
	</ul>
</div>