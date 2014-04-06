<?php
class MessagesController extends MessagesAppController {

	public $name = 'Messages';
	
	public $uses = array('Messages.Message');
	
	public $allowedActions = array('count');
	
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		$this->set('displayName', 'subject');
		$this->set('displayDescription', 'body'); 
		$this->set('showGallery', true); 
		$this->set('galleryModel', array('alias' => 'Sender', 'name' => 'User')); 
		$this->set('galleryForeignKey', 'id'); 
		$this->set('link', array('pluginName' => 'messages', 'controllerName' => 'messages', 'actionName' => 'reply'));
		
		if (in_array('Comments', CakePlugin::loaded())) {
			$this->components['Comments.Comments'] = array(
				'userModelClass' => 'Users.User', 
				);
		}
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->passedArgs['comment_view_type'] = 'threaded';
	}

/**
 * Set the user id which you want to view the messages board
 * @param {int} uid ->the id of the user 
 * @param {int} received -> do you want to view the received messages
 */
	public function index($box = 'Inbox', $foreignKey = null) {
		$userId = $this->Auth->user('id');
		switch ($box){
			case 'Inbox':
				$options = array(
					'conditions' => array('Message.is_archived <>' => 1),
					'contain' => array('Sender' => array('fields' => array('full_name'))));
				break;
			case 'Archived':
				$options = array(
					'conditions' => array('Message.is_archived' => 1),
					'contain' => array('Sender' => array('fields' => array('full_name'))));
				break;
			case 'Sent':
				$options = array(
					'conditions' => array('Message.sender_id' => $userId),
					'contain' => array('Recipient', 'Sender' => array('fields' => array('full_name'))));
				break;
			default : 
				$options = array(
					'conditions' => array('Message.foreign_key' => $foreignKey),
					'contain' => array('Recipient', 'Sender' => array('fields' => array('full_name'))));
				break;
			};
		$options = am($options, array('order' => array('Message.created' => 'DESC')));
		$this->paginate = $options;
		$allMessages = ZuhaSet::remove($this->paginate(), 'readers');
		foreach ($allMessages as $message) {
			if ($message['Message']['is_read'] == 0) {
				$messages[] = $message;
			} else {
				$readMessages[] = $message;
			}
		}			
		$this->set(compact('messages', 'readMessages'));
		$this->set('boxes', $this->Message->boxes());
		$this->set('currentBox' , $box);
		$this->set('pageActions', false);
	}

/**
 * Compose message
 * 
 */
 	public function add() {
 		if ($this->request->is('post')) {
 			debug($this->request->data);
			exit;
 		}
		// get a list of people we can send to
		$this->set('users', $this->Message->User->find('list')); 
 	}
	
/**
 * Sends a message to the specified user
 * @param {char} to : username of the receiver
 * @return void
 * @return move this Usable Behavior related stuff to the model.
 */
	public function send($recipientId = null) {
		// data submitted save and send notification
		if (!empty($this->request->data)) {	
			if ($this->_prepareMessage()) {
				$this->Session->setFlash(__('Message saved.'));
				$this->redirect(array('action' => 'index'), 'success');
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.', true), 'error');
			}
		}
		
		// display send page
		if (!empty($recipientId)) {
			$this->request->data['User']['User'] = $recipientId;
			$this->set('users', $this->Message->User->find('list', array('conditions' => array('User.id' => $recipientId))));	
		} else {
			$this->Session->setFlash(__('Recipient required'));
			$this->redirect($this->referer());
		}
	}
	
	
/**
 * Reply to a message
 * @param {char} to : username of the receiver
 * @return void
 * @return move this Usable Behavior related stuff to the model.
 */
	public function reply($parentId = null) {
		// data submitted save and send notification
		if (!empty($this->request->data)) {	
			if ($this->_prepareMessage()) {
				$this->Session->setFlash(__('Message saved.'));
				$this->redirect(array('action' => 'index'), 'success');
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.', true), 'error');
			}
		}
		
		// display send page
		if (!empty($parentId)) {
			$message = $this->Message->find('first', array(
				'conditions' => array(
					'Message.id' => $parentId,
					),
				'contain' => array(
					'User',
					'Sender',
					),
				));
			$message['Message']['reader_id'] = $this->Session->read('Auth.User.id');
			if ($this->Message->readMessage($message)) {	
				$users = Set::combine($message['User'], '{n}.id', array('{0} ({1})', '{n}.full_name', '{n}.username'));
				$message['Recipient'] = $users;
				$this->request->data['User']['User'] = Set::extract('/User/id', $message);
				$this->set(compact('users', 'message'));
			} else {
				$this->Session->setFlash(__('The message could not be read. Please, try again.'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Session->setFlash(__('Invalid reply to id.'));
			$this->redirect($this->referer());
		}
	}
	
	
/**
 * Get the body of the message setup, and then send it out.
 */
	private function _prepareMessage() {
		// find the users from the habtm users array
		if (!empty($this->request->data['User']['User'])) {
			$recipients = $this->Message->Recipient->find('all', array(
				'conditions' => array(
					'Recipient.id' => $this->request->data['User']['User'],
					),
				));
		}
		
		// add the sender into the users array so that they receive comments and/or view the message at all
		$this->request->data['User']['User'][] = $this->request->data['Message']['sender_id'];
		$this->request->data['User']['User'] = array_unique($this->request->data['User']['User']);
		if ($this->Message->save($this->request->data)) {
			// send the message via email
			if (!empty($recipients)) { 
				foreach ($recipients as $recipient) {
					$viewUrl = str_replace('{messageId}', $this->Message->id, 'http://'.$_SERVER['HTTP_HOST'].$this->request->data['Message']['viewPath']);
					$message = $this->request->data['Message']['body'];
					$message .= '<p>You can reply to this message here: <a href="'.$viewUrl.'">'.$viewUrl.'</a></p>';
					$this->__sendMail($recipient['Recipient']['email'], $this->request->data['Message']['title'], $message, $template = 'default');
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	
/**
 * @todo	This needs to check whether the message is actually sent, and make the message as not_sent if it fails.
 */
	private function _sendMessage() {
		$this->Message->create();
		if ($this->Message->save($this->request->data)) :
			$url =   Router::url(array(
				'plugin' => 'messages',
				'controller '=> 'messages',
				'action' => 'read', $this->Message->id), true);
			$msg = 'Hi,<br> You have received a messae from '.$this->Auth->user('username');
			$msg .= "<br><br>" . $this->request->data['Message']['body'];
			$msg .= "<br><br> <a href = '{$url}'>Click here to reply/view the message.</a>" ;
			echo $this->__sendMail(array($to['Recipient']['email'] => $to['Recipient']['username']),
			$this->request->data['Message']['title'],
				$msg );
			return true;
		else :
			return false;
		endif;
	}
		

/**
 * Archives the message
 * @param unknown_type $id
 * @return unknown_type
 */
	public function archive($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid message', true));
		}
		$message =$this->Message->read(null, $id);
		$message['Message']['is_archived'] = 1;
		if ($this->Message->save($message)) {
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
		if (!$id) {
			$this->Session->setFlash(__('Invalid message', true));
		}
		$message =$this->Message->read(null, $id);
		$message['Message']['reader_id'] = $this->Session->read('Auth.User.id');
		if ($this->Message->readMessage($message)) {
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
		if (!$id) {
			$this->Session->setFlash(__('Invalid message', true));
		}
		$message =$this->Message->read(null, $id);
		$message['Message']['unreader_id'] = $this->Session->read('Auth.User.id');
		if ($this->Message->unReadMessage($message)) {
			$this->Session->setFlash(__('The message has been marked as unread', true));
		} else {
			$this->Session->setFlash(__('The message could not be saved. Please, try again.', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid message', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Message->save($this->request->data)) {
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
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for message', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Message->delete($id)) {
			$this->Session->setFlash(__('User message deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User message was not deleted', true));
		$this->redirect($this->referer());
	}

	public function count($id = null) {
		if ($this->Session->read('Auth.User.id')) {
			return $this->Message->find('count', array(
				'conditions' => array(
					'Message.is_read' => 0,
					),
				));
		} else {
			return 0;
		}
	}

/**
 * This action returns data for a mini-inbox
 *
 */
	public function inboxElement ($model = null, $foreignKey = null) {
		// defaults
		$conditions['Message.is_read'] = 0;
		
		// options
		if ( !empty($model) ) {
			$conditions['Message.model'] = $model;
		}
		if ( !empty($foreignKey) ) {
			$conditions['Message.foreign_key'] = $foreignKey;
		}

		return $this->Message->find('threaded', array(
			'conditions' => $conditions,
			'contain' => array('Sender' => array('fields' => array('full_name'))),
			'fields' => array('id', 'sender_id', 'title', 'body', 'subject')
		));
	}

}
