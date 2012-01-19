<?php
class Message extends MessagesAppModel {
	
	public $name = 'Message';
	
	public $displayField = 'title';
	
	public $actsAs = array(
		'Tree', 
		'Users.Usable' => array(
			'defaultRole' => 'reader'
			), 
		);
	public $fullName = "Messages.Message"; //for the sake of comments plugin
	
	public $belongsTo = array(
		'Sender' => array(
			'className' => 'Users.User',
			'foreignKey' => 'sender_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	
	public $hasMany = array(
		'Used' => array(
			'className' => 'Users.Used',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Used.model' => 'Message'),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'Users.User',
			'joinTable' => 'used',
			'foreignKey' => 'foreign_key',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Recipient' => array(
			'className' => 'Users.User',
			'joinTable' => 'used',
			'foreignKey' => 'foreign_key',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
	);
	
	
	public function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
	    $this->virtualFields['subject'] = sprintf('CONCAT(%s.title)', $this->alias);
		
		if (in_array('Activities', CakePlugin::loaded())) {
			$this->actsAs['Activities.Loggable'] = array(
				'nameField' => 'title', 
				'descriptionField' => 'body', 
				'actionDescription' => 'Posted by', 
				'userField' => 'sender_id', 
				'parentForeignKey' => 'foreign_key'
				);
		}
    }
	
	
	public function beforeSave($options) {
		$this->data = $this->_cleanData($this->data);
		
		return true;
	}
	
	
	public function afterFind($results, $primary) {
		if (!empty($results[0]['Message'])) {
			$i=0; foreach($results as $result) {
				$results[$i]['Message']['is_read'] = $this->_handleReaders($result);
				$i++;
			} 
		}
		return $results;
	}
	
	
	public function boxes() {
		return array('Inbox' => 'Inbox', 'Sent' => 'Sent Items', 'Archived' =>'Archived');
	}


/**
 * Mark a message as read
 * 
 * @param {int} 	The user id who has read the message.
 */
	public function readMessage($data) {
		if (!empty($data['Message']['reader_id'])) {
			if ($this->save($data)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
/**
 * Mark a message as read
 * 
 * @param {int} 	The user id who has read the message.
 */
	public function unReadMessage($data) {
		if (!empty($data['Message']['unreader_id'])) {
			if ($this->save($data)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
/**
 * Fix up the data so that its ready for saving.
 *
 * @param {array}
 */
	private function _cleanData($data) {
		
		# add a reader to the serialized readers field
		if (!empty($data['Message']['readers']) && !empty($data['Message']['reader_id'])) {
			$readers = unserialize($data['Message']['readers']);
			if (in_array($data['Message']['reader_id'], $readers)) {
				# do nothing the reader is already there
			} else {
				$readers[] = $data['Message']['reader_id'];
				$data['Message']['readers'] = serialize($readers);
			}
		} else if (!empty($data['Message']['reader_id'])) {
			$data['Message']['readers'] = serialize(array($data['Message']['reader_id']));
		}
		
		
		# remove a reader from the serialized readers field
		if (!empty($data['Message']['readers']) && !empty($data['Message']['unreader_id'])) {
			$readers = unserialize($data['Message']['readers']);
			if (in_array($data['Message']['unreader_id'], $readers)) {
				$readers = array_diff($readers, array($data['Message']['unreader_id']));				
				$data['Message']['readers'] = !empty($readers) ? serialize(array_values($readers)) : null;
			} else {
				# do nothing the reader isn't there
			}
		} else if (!empty($data['Message']['unreader_id'])) {
			$data['Message']['readers'] = null;
		}
		
		return $data;
	}
	
	
	
/**
 * Decide if is_read should be 1 or 0
 *
 * @param {array}
 */
	private function _handleReaders($data) {
		$userId = CakeSession::read('Auth.User.id');
		
		if (!empty($userId) && !empty($data['Message']['readers'])) {
			$readers = unserialize($data['Message']['readers']);
			if (in_array($userId, $readers)) {
				return 1;
			} else {
				return 0;
			}
		}
		return 0;
	}
}
?>