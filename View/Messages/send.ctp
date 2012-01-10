<div class="messages index">
  <div class="messages form"> <?php echo $this->Form->create('Message');?>
    <fieldset>
      <legend><?php echo __('Send a message %s', 'to '.$users[1]); ?></legend>
      <?php
	  echo $this->Form->input('Message.title', array('label' => __('Subject', true)));
	  echo $this->Form->input('Message.body', array('label' => '', 'type' => 'richtext', 'ckeSettings' => array('buttons' => array('Bold','Italic','Underline','FontSize','TextColor','BGColor','-','NumberedList','BulletedList','Blockquote','JustifyLeft','JustifyCenter','JustifyRight'))));
	  echo $this->Form->input('User', array('multiple' => 'checkbox', 'label' => 'Recipients'));
	  
	  echo $this->Form->hidden('Message.sender_id', array('value' => $this->Session->read('Auth.User.id')));
	  echo $this->Form->input('Message.model', array('type' => 'hidden', 'value' => 'Message'));
	  echo $this->Form->end(__('Send', true)); ?>
    </fieldset>
  </div>
</div>
