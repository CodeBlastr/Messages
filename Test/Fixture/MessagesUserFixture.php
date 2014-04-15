<?php
/**
 * MessagesUserFixture
 *
 */
class MessagesUserFixture extends CakeTestFixture {
	
/**
 * Import
 *
 * @var array
 */
	public $import = array('config' => 'Messages.MessagesUser');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '5077241d-9040-43c9-85b1-22d40000000',
			'message_id' => '1077241d-9040-43c9-85b1-22d40000000',
			'user_id' => '101',
			'label' => 'read'
		),
	);
	
	public function beforeSave($options = array()) {
		debug($this->data);
		exit;
	}
}
