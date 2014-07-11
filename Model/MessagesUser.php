<?php

class MessagesUser extends MessagesAppModel {

	public $name = 'MessagesUser';
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			)
		);

/**
 * Update label
 */
 	public function updateLabel($oldLabel = 'read', $newLabel = 'unread', $messageId = null, $userId = null) {
 		// return $this->MessagesUser->updateAll(
			// array('MessagesUser.is_archived' => 1),
			// array('MessagesUser.message_id' => $messageId, 'MessagesUser.user_id' => $userId)
			// );1
		return $this->updateAll(
			array('MessagesUser.label' => "'" . $newLabel . "'"),
			array('MessagesUser.label' => $oldLabel, 'MessagesUser.message_id' => $messageId, 'MessagesUser.user_id' => $userId)
			); 		
 	}

/**
 * Remove label
 */
 	public function removeLabel($label = 'unread', $messageId = null, $userId = null) {
		// $check = $this->find('count', array('conditions' => array('MessagesUser.label' => $label, 'MessagesUser.message_id' => $messageId, 'MessagesUser.user_id' => $userId)));
		// if ($check == 0) {
			// // it's already removed?
			// return true;
		// }
		$message['message_id'] = $messageId;
		$message['user_id'] = $userId;
		$message['label'] = $label;

		return $this->deleteAll($message); 		
 	}
	
/**
 * Add label
 */
 	public function addLabel($label = 'read', $messageId = null, $userId = null) {
		$check = $this->find('count', array('conditions' => array('MessagesUser.label' => $label, 'MessagesUser.message_id' => $messageId, 'MessagesUser.user_id' => $userId)));
		if ($check > 0) {
			// it's already marked as this label
			return true;
		}
		$message['MessagesUser']['message_id'] = $messageId;
		$message['MessagesUser']['user_id'] = $userId;
		$message['MessagesUser']['label'] = $label;
		$this->create();
		return $this->save($message, array('callbacks' => false)); 		
 	}


    public function newMessageCount($userId){
        return $this->find('count',array(
            'MessagesUser.user_id' => $userId,
            'label' =>'unread',
        ));


    }

}
