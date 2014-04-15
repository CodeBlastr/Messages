<div class="col-md-3">
	<?php echo $this->element('Messages.navigation'); ?>
</div>
<div class="col-md-9">
	<?php echo $this->Form->create('Message', array('type' => 'file')); ?>
	<?php echo $this->Form->hidden('Message.model', array('value' => 'Message')); ?>
	<fieldset>
		<?php echo $this->Form->input('Message.subject'); ?>
		<?php echo $this->Form->input('Message.body', array('label' => '', 'type' => 'simpletext')); ?>
		<?php $i=0; foreach ($recipients as $id => $name) : ?>
			<?php echo $this->Form->input('MessagesUser.' . $i . '.user_id', array('type' => 'checkbox', 'value' => $id, 'label' => $name)); ?>
			<?php echo $this->Form->input('MessagesUser.' . $i . '.label', array('type' => 'hidden', 'value' => 'unread')); ?>
		<?php $i++; endforeach; ?>
		<?php echo $this->Form->end(__('Send')); ?>
	</fieldset>
</div>