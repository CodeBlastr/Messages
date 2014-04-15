<?php

class Message extends MessagesAppModel {

	public $name = 'Message';

	public $displayField = 'subject';

	public $actsAs = array(
		'Tree',
		);

	public $belongsTo = array(
		'Sender' => array(
			'className' => 'Users.User',
			'foreignKey' => 'creator_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			),
		'Parent' => array(
			'className' => 'Messages.Message',
			'foreignKey' => 'parent_id',
			'order' => array('Parent.created' => 'DESC')
			)
		);

	public $hasMany = array(
		'Child' => array(
			'className' => 'Messages.Message',
			'foreignKey' => 'parent_id',
			'dependent' => false,
			'order' => array('Child.created' => 'DESC')
			),
		'MessagesUser' => array(
			'className' => 'Messages.MessagesUser',
			'foreignKey' => 'message_id',
			'dependent' => false
			)
		);

	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'Users.User',
			'joinTable' => 'messages_users',
			'foreignKey' => 'message_id',
			'associationForeignKey' => 'user_id'
			)
		);

	public function __construct($id = false, $table = null, $ds = null) {
		
		if (CakePlugin::loaded('Activities')) {
			$this->actsAs['Activities.Loggable'] = array(
				'nameField' => 'subject',
				'descriptionField' => 'body',
				'actionDescription' => 'Posted by',
				'userField' => 'creator_id',
				'parentForeignKey' => 'foreign_key'
			);
		}

		if (CakePlugin::loaded('Tags')) {
			$this->actsAs['Tags.Taggable'] = array('automaticTagging' => true, 'taggedCounter' => true);
			$this->hasAndBelongsToMany['Tag'] = array(
				'className' => 'Tags.Tag',
				'joinTable' => 'tagged',
				'foreignKey' => 'foreign_key',
				'associationForeignKey' => 'tag_id',
				'conditions' => 'Tagged.model = "Message"',
					// 'unique' => true,
			);
		}

		if (CakePlugin::loaded('Categories')) {
			$this->hasAndBelongsToMany['Category'] = array(
	            'className' => 'Categories.Category',
	       		'joinTable' => 'categorized',
	            'foreignKey' => 'foreign_key',
	            'associationForeignKey' => 'category_id',
	    		'conditions' => array('Categorized.model' => 'Product'),
	    		// 'unique' => true,
	            );
			$this->actsAs['Categories.Categorizable'] = array('modelAlias' => 'Message');
		}
		parent::__construct($id, $table, $ds); // order matters
		
		// default ordering is newest first
		$this->order = array($this->alias . '.created' => 'DESC');
	}
	
	public function afterSave($created, $options = array()) {
		if ($created) {
			if (!in_array($this->data[$this->alias]['creator_id'], Set::extract('/MessagesUser/user_id', $this->data['MessagesUser']))) {
				// make the sender a user attached to the message (with a status of read) if they're not sending to themselves
				$this->MessagesUser->addLabel('read', $this->id, $this->data[$this->alias]['creator_id']);
			}
			// now deal with everyone else on the message 
			// hmm.. this should probably be moved to the messages user model
			if (!empty($this->data['MessagesUser'])) {				
				foreach ($this->data['MessagesUser'] as $recipient) {						
					// send the message via email
					$email = $this->User->field('email', array('User.id' => $recipient['MessagesUser']['user_id']));
					$viewUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/messages';
					$message = $this->data['Message']['body'];
					$message .= '<p>You can reply to this message here: <br><a href="' . $viewUrl . '">' . $viewUrl . '</a></p>';
					$this->__sendMail($email, $this->data['Message']['subject'], $message, $template = 'default');
				}
			} else {
				// creating a message but it's not getting sent to anyone.  Maybe, just deal with it here.
				debug($this->data);
				exit;
			}
		}
	}

/**
 * 
 * @return array
 */
	public function boxes() {
		return array('Inbox' => 'Inbox', 'Sent' => 'Sent Items', 'Archived' => 'Archived');
	}

/**
 * 
 * @return array
 */
	public function labels() {
		return array('unread' => 'Unread', 'read' => 'Read', 'archived' => 'Archive');
	}
	
/**
 * Find by Label
 * 
 * Created so that you don't have to type out the joins thing every time.
 */
 	public function findbyLabel($type = 'all', $options = array()) {
 		// remove helper fields
 		$label = !empty($options['label']) ? $options['label'] : 'read'; // default find by label
		$userId = $options['userId'];
		$archived = $options['archived'];
		unset($options['label']);
		unset($options['userId']);
		unset($options['archived']);
		$conditions = array();
		// remove archived unless you specifically asked for archived
		if (!empty($archived)) {
			$conditions = array('MessagesUser.is_archived' => 1);
		} else {
			$conditions = array('MessagesUser.is_archived' => 0);
		}
		
		if (!empty($label) && !empty($userId)) {
			$conditions = array_merge($conditions, array(
				'MessagesUser.label' => $label,
				'MessagesUser.user_id' => $userId
				));
		} else {
			$conditions = array_merge($conditions, array(
				'MessagesUser.label' => $label
				));
		}
 		return $this->find($type, $options + array(
			'joins' => array(
				array(
					'table' => 'messages_users',
					'alias' => 'MessagesUser',
					'type' => 'INNER',
					'conditions' => array_merge($conditions, array(
						'MessagesUser.message_id = Message.id'
						))
					)
				)
			));
 	}
	
/**
 * Find by Labels
 */
 	public function findbyLabels($type = 'all', $options = array()) {
 		// remove helper fields
 		$labels = $options['label'];
		$userId = $options['userId'];
		$archived = $options['archived'];
		unset($options['label']);
		unset($options['userId']);
		unset($options['archived']);
		$messages = array();
		foreach ($labels as $label) {
			$messages = array_merge($messages, $this->findbyLabel($type, array_merge($options, array('archived' => $archived, 'label' => $label, 'userId' => $userId))));
		}
 		return $messages;
 	}

/**
 * Mark a message as read
 *
 * @param uuid
 * @param uuid
 * @return boolean
 */
	public function readMessage($messageId = null, $userId = null) {
		if (empty($userId) || empty($messageId)) {
			return false;
		}
		return $this->MessagesUser->updateLabel('unread', 'read', $messageId, $userId); 
	}

/**
 * Mark a message as archived
 *
 * @param uuid
 * @param uuid
 * @return boolean
 */
	public function archive($messageId = null, $userId = null) {
		if (empty($userId) || empty($messageId)) {
			return false;
		}
		return $this->MessagesUser->updateAll(
			array('MessagesUser.is_archived' => 1),
			array('MessagesUser.message_id' => $messageId, 'MessagesUser.user_id' => $userId)
			);
	}

/**
 * Mark a message as unread
 *
 * @param uuid
 * @param uuid
 * @return boolean
 */
	public function unreadMessage($messageId = null, $userId = null) {
		if (empty($userId) || empty($messageId)) {
			return false;
		}
		return $this->MessagesUser->updateLabel('read', 'unread', $messageId, $userId); 
	}

/**
 * Fix up the data so that its ready for saving.
 *
 * @param array $data
 * @return array
 */
	protected function _cleanData($data) {
		return $data;
	}

}
