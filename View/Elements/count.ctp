<?php
// App::uses('Message', 'Messages.Model');
// $Message = new Message();
$Message = ClassRegistry::init('Messages.Message');
if ($this->Session->read('Auth.User.id')) {
	echo $Message->findbyLabel('count', array(
		'label' => 'unread',
		'userId' => $this->Session->read('Auth.User.id')
		));
} else {
	echo 0;
}