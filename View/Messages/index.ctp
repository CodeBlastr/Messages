<?php
echo __('<h2>%s Messages</h2>', $currentBox); 

if (!empty($readMessages)) {
	echo '<h3 class="title">Unread Messages</h2>';
	echo $this->Element('scaffolds/index', array(
		'data' => $messages,
		'actions' => array(
			$this->Html->link('Reply', array('action' => 'reply', '{id}')),
			$this->Html->link('Mark as Read', array('action' => 'read', '{id}')),
			$this->Html->link('Archive', array('action' => 'archive', '{id}')),
			),
		));
}

if (!empty($readMessages)) {
	echo '<h3 class="title">Read Messages</h2>';
	echo $this->Element('scaffolds/index', array(
		'data' => $readMessages,
		'actions' => array(
			$this->Html->link('Reply', array('action' => 'reply', '{id}')),
			$this->Html->link('Mark as Unread', array('action' => 'unread', '{id}')),
			$this->Html->link('Archive', array('action' => 'archive', '{id}')),
			),
		));
}

echo $this->element('context_menu');