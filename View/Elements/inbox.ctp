<?php
/**
 * @todo Maybe this should use a sub-element??  Are those legal?? ^JB
 */

$messages = $this->requestAction("/messages/messages/inboxElement/$model/$foreignKey");

if ( !empty($messages) ) {
	foreach ( $messages as $message ) {
		
		$output .= $this->Html->tag('div',
			$this->Html->tag('div', '<b class="muted">From: </b>' . $message['Sender']['full_name'], array('class' => 'inboxElement_sender'))
			. $this->Html->tag('div', '<b class="muted">Subject: </b>' . $message['Message']['title'], array('class' => 'inboxElement_title'))
			. $this->Html->tag('div', $message['Message']['body'], array('class' => 'inboxElement_body'))
			. $this->Html->tag('div',
				$this->Html->link(
					'<i class="icon-fullscreen"></i>',
					array('plugin' => 'messages', 'controller' => 'messages', 'action' => 'view', $message['Message']['id']),
					array('escape' => false, 'title' => 'view message')
				)
				. $this->Html->link(
					'<i class="icon-comment"></i>',
					array('plugin' => 'messages', 'controller' => 'messages', 'action' => 'reply', $message['Message']['id']),
					array('escape' => false, 'title' => 'reply to message')
				)
				. $this->Html->link(
					'<i class="icon-ok"></i>',
					array('plugin' => 'messages', 'controller' => 'messages', 'action' => 'read', $message['Message']['id']),
					array('escape' => false, 'title' => 'mark message as read')
				)
					, array('class' => 'inboxElement_actions')
			), array('class' => 'inboxElement_message')
		);
		
		if ( !empty($message['children']) ) {
			foreach ( $children as $child ) {
				$output .= $this->Html->tag('div',
					$this->Html->tag('div', 'From: ' . $child['Sender']['full_name'], array('class' => 'inboxElement_sender'))
					. $this->Html->tag('div', 'Subject: ' . $child['Message']['title'], array('class' => 'inboxElement_title'))
					. $this->Html->tag('div', $child['Message']['body'], array('class' => 'inboxElement_body'))
					. $this->Html->tag('div',
						$this->Html->link(
							'<i class="icon-share"></i>',
							array('plugin' => 'messages', 'controller' => 'messages', 'action' => 'reply', $message['Message']['id']),
							array('escape' => false, 'title' => 'view message')
						), array('class' => 'inboxElement_actions')
					), array('class' => 'inboxElement_childMessage')
				);
			}
		}
		
	}
} else {
	$output .= '<i>no new messages</i>';
}

echo $this->Html->tag('div',
	$output
	, array('id' => 'inboxElement')
);
