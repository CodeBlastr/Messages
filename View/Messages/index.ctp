<div class="col-md-3">
	<?php echo $this->element('Messages.navigation'); ?>
</div>
<div class="col-md-9">
	<div class="panel panel-primary list-group">
		<div class="panel-heading">
			Unread
		</div>
		<?php if (!empty($messages)) : unset($message); ?>
			<?php foreach ($messages as $message) : ?>
			<div class="list-group-item clearfix">
				<?php echo $this->Html->link($message['Message']['subject'], array('action' => 'read', $message['Message']['id'])); ?>
				<span class="badge"><?php echo $this->Html->link('Reply', array('action' => 'reply', $message['Message']['id'])); ?></span>
				<span class="badge"><?php echo $this->Html->link('Mark as Read', array('action' => 'read', $message['Message']['id'])); ?></span>
				<span class="badge"><?php echo $this->Html->link('Archive', array('action' => 'archive', $message['Message']['id'])); ?></span>
			</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="list-group-item text-center clearfix">
				No unread messages
			</div>
		<?php endif; ?>
		<div class="panel-heading">
			Read
		</div>
		<?php if (!empty($readMessages)) : unset($message); ?>
			<?php foreach ($readMessages as $message) : ?>
			<div class="list-group-item clearfix">
				<?php echo $this->Html->link($message['Message']['subject'], array('action' => 'reply', $message['Message']['id'])); ?>
				<span class="badge"><?php echo $this->Html->link('Reply', array('action' => 'reply', $message['Message']['id'])); ?></span>
				<span class="badge"><?php echo $this->Html->link('Mark as Unread', array('action' => 'unread', $message['Message']['id'])); ?></span>
				<span class="badge"><?php echo $this->Html->link('Archive', array('action' => 'archive', $message['Message']['id'])); ?></span>
			</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="list-group-item text-center clearfix">
				No read messages
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Messages',
		'items' => array(
			$this->Html->link(__('Inbox'), array('action' => 'index', 'Inbox')),
			$this->Html->link(__('Sent'), array('action' => 'index', 'Sent')),
			$this->Html->link(__('Archived'), array('action' => 'index', 'Archived')),
			)
		),
	)));