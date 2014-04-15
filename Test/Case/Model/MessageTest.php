<?php
App::uses('Message', 'Messages.Model');

/**
 * Message Test Case
 *
 */
class MessageTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
        'plugin.Messages.Message',
        'plugin.Messages.MessagesUser',
        
		'plugin.Users.User',
		'plugin.Users.UserGroup',
		'plugin.Users.UsersUserGroup',
		'plugin.Users.Used',
		
		'plugin.Media.Media', // user dependency
		'plugin.Media.MediaAttachment', // user dependency
        'plugin.Categories.Category', // message dependency
        'plugin.Categories.Categorized', // message dependency
        'plugin.Activities.Activity' // message dependency
        );

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Message = ClassRegistry::init('Messages.Message');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Message);

		parent::tearDown();
	}
	
	public function testUnReadAndReadMessage() {
		$original = $this->Message->findbyLabel('all', array('contain' => array('User'), 'label' => 'read'));
		$count = count($original);
		
		// first unRead a message 
		$this->Message->unreadMessage($original[0]['Message']['id'], $original[0]['User'][0]['id']);
		$result = $this->Message->findbyLabel('all', array('contain' => array('User'), 'label' => 'read')); // now look for the read message again the same way  (should be gone)
		$this->assertTrue($count > count($result));
		
		// then read the message
		$this->Message->readMessage($original[0]['Message']['id'], $original[0]['User'][0]['id']);
		$result = $this->Message->findbyLabel('all', array('contain' => array('User'), 'label' => 'read')); // now look for the read message again
		$this->assertTrue($count == count($result));
		
	}
    
	public function testSave() {
		$userId = $this->Message->MessagesUser->User->field('id'); // just find the first user
		$data = array(
			'Message' => array(
				'subject' => 'My Second Message',
				'body' => 'My second message body',
				'creator_id' => $userId
				),
			'MessagesUser' => array( // these are the recipients
				array(
					'user_id' => 101,
					'label' => 'unread'
					)
				)
			);
		$this->Message->create();
		$this->Message->saveAll($data); // Have to saveAll()
		$result = $this->Message->find('first', array(
			'conditions' => array(
				'Message.id' => $this->Message->id
				),
			'contain' => array(
				'User'
				)
			));
		$this->assertTrue(count($result['User']) == 2);
	}
    
	public function testFindbyLabel() {
		$userId = $this->Message->MessagesUser->field('user_id', array('MessagesUser.label' => 'read')); // just find the first user
		$result = $this->Message->findbyLabel('all', array('contain' => array('User'), 'label' => 'read', 'userId' => $userId));

		$this->assertTrue(!empty($result[0]['Message']['id']) && !empty($result[0]['User'][0]['id']));
	}
}
