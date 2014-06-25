<?php
App::uses('MessagesAppController', 'Messages.Controller');

class AppMessagesController extends MessagesAppController {

	public $name = 'Messages';
	
	public $uses = array('Messages.Message');
	
	public $allowedActions = array('count');

/**
 * Set the user id which you want to view the messages board
 * 
 * @param {int} uid ->the id of the user 
 * @param {int} received -> do you want to view the received messages
 */
	public function index($box = null, $foreignKey = null) {
		if (empty($box)) {
			$this->redirect(array('action' => 'index', 'Inbox')); // default box is Inbox, (rather than give the parameter a default, redirect so that navigation can get it's active class)
		}
        $index = method_exists($this, '_index' . ucfirst($box)) ? '_index' . ucfirst($box) : '_indexInbox';
		$this->set('boxes', $this->Message->boxes());
		$this->set('currentBox' , $box);
        return $this->$index($box, $foreignKey);
	}

/**
 * Inbox method
 */
 	private function _indexInbox() {
 		$readMessages = $this->Message->findbyLabel('all', array('label' => 'read', 'userId' => $this->Session->read('Auth.User.id')));
		$messages =  $this->Message->findbyLabel('all', array('contain' => array('User', 'Sender'), 'label' => 'unread', 'userId' => $this->Session->read('Auth.User.id')));
		$this->set(compact('messages', 'readMessages'));
 	}

/**
 * Inbox method
 */
 	private function _indexSent() {
 		$messages =  $this->Message->find('all', array('conditions' => array('Message.creator_id' => $this->Session->read('Auth.User.id'))));
		$this->set(compact('messages'));
 	}

/**
 * Archived method
 */
 	private function _indexArchived() {
 		$readMessages =  $this->Message->findbyLabel('all', array('contain' => array('User', 'Sender'), 'label' => 'read', 'archived' => true, 'userId' => $this->Session->read('Auth.User.id')));
		$messages =  $this->Message->findbyLabel('all', array('contain' => array('User', 'Sender'), 'label' => 'unread', 'archived' => true, 'userId' => $this->Session->read('Auth.User.id')));
		$this->set(compact('messages', 'readMessages'));
 	}

/**
 * Compose message
 * 
 * @param uuid
 */
 	public function add($userId = null) {
 		if ($this->request->is('post')) {
 			if ($this->Message->saveAll($this->request->data)) {
 				$this->Session->setFlash(__('Message sent'));
				$this->redirect(array('action' => 'index'));
 			} else {
 				$this->Session->setFlash(__('Problem sending message, please try again.'));
				$this->redirect($this->referer());
 			}
 		}
		// get a list of people we can send to
		if ($userId) {
			$this->set('recipients', $this->Message->User->find('list', array('conditions' => array('User.id' => $userId)))); 
		} else {
			$this->set('recipients', $this->Message->User->find('list')); 
		}
		
 	}	
	
/**
 * Reply to a message
 * 
 * @param {char} to : username of the receiver
 * @return void
 * @return move this Usable Behavior related stuff to the model.
 */
	public function reply($parentId = null) {
		$this->Message->id = $parentId;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		// data submitted save and send notification
		if ($this->request->is('post') || $this->request->is('put')) {	
			if ($this->Message->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Message sent'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be sent. Please, try again.', true), 'error');
			}
		}
		
		//  reply page viewVars
		$message = $this->Message->find('first', array(
			'conditions' => array(
				'Message.id' => $parentId,
				),
			'contain' => array(
				'Sender',
				'User'
				),
			));

		if ($this->Message->readMessage($message['Message']['id'], $this->Session->read('Auth.User.id'))) {
			$users = Set::combine($message['User'], '{n}.id', array('{0} ({1})', '{n}.full_name', '{n}.username'));
			$this->set('recipients', $users); 
			$this->request->data['User']['User'] = Set::extract('/User/id', $message);
			$this->set(compact('users', 'message'));
		} else {
			$this->Session->setFlash(__('The message could not be read. Please, try again.'));
			$this->redirect(array('action' => 'index'));
		}
	}
		

/**
 * Archives the message
 * 
 * @param unknown_type $id
 * @return unknown_type
 */
	public function archive($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		if ($this->Message->archive($id, $this->Session->read('Auth.User.id'))) {
			$this->Session->setFlash(__('The message has been Archived', true));
		} else {
			$this->Session->setFlash(__('The message could not be archived. Please, try again.', true));
		}
		$this->redirect($this->referer());
	}
	
/**
 * Changes status to read
 * @param unknown_type $id
 * @return unknown_type
 */
	public function read($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		$message =$this->Message->read(null, $id);
		if ($this->Message->readMessage($id, $this->Session->read('Auth.User.id'))) {
			$this->Session->setFlash(__('The message has been marked as read', true));
		} else {
			$this->Session->setFlash(__('The message could not be saved. Please, try again.', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * Changes status to unread
 * @param unknown_type $id
 * @return unknown_type
 */
	public function unread($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		if ($this->Message->unreadMessage($id, $this->Session->read('Auth.User.id'))) {
			$this->Session->setFlash(__('The message has been marked as unread', true));
		} else {
			$this->Session->setFlash(__('The message could not be saved. Please, try again.', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function edit($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		if (!empty($this->request->data)) {
			if ($this->Message->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Message->read(null, $id);
		}
	}

	public function delete($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Message not found'));
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__('User message deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User message was not deleted', true));
		$this->redirect($this->referer());
	}

}


if (!isset($refuseInit)) {
	class MessagesController extends AppMessagesController {
	}

}