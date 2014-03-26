<ul>
	<li><?php echo $this->Html->link(__('Compose'), array('action' => 'add')); ?></li>
	<li><?php echo $this->Html->link(__('Inbox'), array('action' => 'index', 'Inbox')); ?></li>
	<li><?php echo $this->Html->link(__('Sent'), array('action' => 'index', 'Sent')); ?></li>
	<li><?php echo $this->Html->link(__('Archived'), array('action' => 'index', 'Archived', 'filter' => 'archived:1')); ?></li>
</ul>