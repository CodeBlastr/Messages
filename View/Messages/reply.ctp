<div class="messages reply">
  <div id="navigation">
    <div id="n1" class="info-block">
      <div class="viewRow">
        <ul class="metaData">
          <li><span class="metaDataLabel">
            <?php echo __('Subject: '); ?>
            </span><span class="metaDataDetail"><?php echo $message['Message']['title']; ?></span></li>
          <li><span class="metaDataLabel">
            <?php echo __('From: '); ?>
            </span><span class="metaDataDetail"><?php echo $message['Sender']['full_name'] . ' ('.$message['Sender']['username'].')'; ?></span></li>
          <li><span class="metaDataLabel">
            <?php echo __('To: '); ?>
            </span>
            <?php if(!empty($message['Recipient'])) : foreach ($message['Recipient'] as $recipient) : ?>
            <span class="metaDataDetail"><?php echo $recipient.', '; ?></span>
            <?php endforeach; endif; ?>
          </li>
        </ul>
        <div class="recordData">
          <div class="truncate">
          	<?php echo $message['Message']['body']; ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /info-block end -->
  </div>
</div>
<div class="messages index">
  <div class="messages form"> 
  <?php echo $this->Form->create('Message');?>
    <fieldset>
      <legend class="toggleClick"><?php echo __('Reply'); ?></legend>
      <?php
	  echo $this->Form->input('Message.title', array('label' => __('Subject'), 'value' => 'Re: '.$message['Message']['title']));
	  echo $this->Form->input('Message.body', array('label' => '', 'type' => 'richtext', 'ckeSettings' => array('buttons' => array('Bold','Italic','Underline','FontSize','TextColor','BGColor','-','NumberedList','BulletedList','Blockquote','JustifyLeft','JustifyCenter','JustifyRight'))));
	  echo $this->Form->input('User', array('multiple' => 'checkbox', 'label' => 'Recipients'));
	  
	  echo $this->Form->hidden('Message.sender_id', array('value' => $this->Session->read('Auth.User.id')));
	  echo $this->Form->hidden('Message.model', array('value' => $message['Message']['model']));
	  echo $this->Form->hidden('Message.parent_id', array('value' => $message['Message']['id']));
	  echo $this->Form->end(__('Send', true)); ?>
    </fieldset>
  </div>
</div>
