<?php
class Message extends MessagesAppModel {
	var $name = 'Message';
	var $displayField = 'title';
	var $actsAs = array(
		'Tree', 
		'Users.Usable', 
		'Activities.Loggable' => array(
			'nameField' => 'title', 
			'descriptionField' => 'body', 
			'actionDescription' => 'Posted by', 
			'userField' => 'sender_id', 
			'parentForeignKey' => 'foreign_key'
			)
		);
	var $fullName = "Messages.Message"; //for the sake of comments plugin
	
	var $belongsTo = array(
		'Sender' => array(
			'className' => 'Users.User',
			'foreignKey' => 'sender_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Recipient' => array(
			'className' => 'Users.User',
			'foreignKey' => 'recipient_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $hasAndBelongsToMany = array(
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
	);
	
	function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
	   # $this->virtualFields['subject'] = sprintf('CONCAT(%s.title, " <small>from ", Sender.full_name, "</small>")', $this->alias);
	    $this->virtualFields['subject'] = sprintf('CONCAT(%s.title)', $this->alias);
    }
	
	function boxes() {
		return array('Inbox' => 'Inbox', 'Sent' => 'Sent Items', 'Archived' =>'Archived');
	}
}
?>