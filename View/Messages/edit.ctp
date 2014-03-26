<div class="userMessages form">
<?php echo $this->Form->create('UserMessage');?>
	<fieldset>
 		<legend><?php echo __('Edit User Message'); ?></legend>
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
<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Messages',
		'items' => array(
			$this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('UserMessage.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('UserMessage.id'))),
			$this->Html->link(__('List User Messages', true), array('action' => 'index')),
			)
		),
	)));