<?php
/**
 * MessageFixture
 *
 */
class MessageFixture extends CakeTestFixture {
	
/**
 * Import
 *
 * @var array
 */
	public $import = array('config' => 'Messages.Message');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1077241d-9040-43c9-85b1-22d40000000',
			'subject' => 'My First Message',
			'body' => 'My first message body',
			'creator_id' => '3',
			'modifier_id' => '3',
			'created' => '0000-00-00 00:00:00',
			'modified' => '0000-00-00 00:00:00'
		)
	);
}
