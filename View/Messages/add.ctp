<div class="col-md-3">
	<?php echo $this->element('Messages.navigation'); ?>
</div>
<div class="col-md-9">
	<?php echo $this->Form->create('Message', array('url' => '/messages/messages/send')); ?>
	<fieldset>
		<?php echo $this->Form->input('Message.title', array('label' => __('Subject'))); ?>
		<?php echo $this->Form->input('Message.body', array('label' => '', 'type' => 'simpletext')); ?>
		<?php echo $this->Form->input('User', array('multiple' => 'checkbox', 'label' => 'Send to...')); ?>
		<?php echo $this->Form->input('Message.sender_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.id'))); ?>
		<?php echo $this->Form->hidden('Message.foreign_key'); ?>
		<?php echo $this->Form->hidden('Message.model', array('value' => 'Message')); ?>
		<?php echo $this->Form->end(__('Send')); ?>
	</fieldset>
</div>