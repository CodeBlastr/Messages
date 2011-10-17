<div class="messages view">
  <div id="navigation">
    <div id="n1" class="info-block">
      <div class="viewRow">
        <ul class="metaData">
          <li><span class="metaDataLabel">
            <?php __('Subject: '); ?>
            </span><span class="metaDataDetail"><?php echo $message['Message']['title']; ?></span></li>
          <li><span class="metaDataLabel">
            <?php __('From: '); ?>
            </span><span class="metaDataDetail"><?php echo $message['Sender']['username']; ?></span></li>
          <li><span class="metaDataLabel">
            <?php __('To: '); ?>
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
<a name="comments"></a>
<div id="post-comments">
	<?php $commentWidget->options(array('allowAnonymousComment' => false));?>
	<?php echo $commentWidget->display();?>
</div>






<?php /*

<ul>
<?php foreach($boxes as $key => $box) {?>
<li>
	<?php 
			echo $this->Html->link($box, array('plugin'=>'messages', 
			'controller'=>'messages','action'=>'index', $key));
			?>
</li>	
<?php }?>
</ul>

<hr></hr>
<h1>Message Details</h1>
<ul>
<li>
From: <?php echo $message['Sender']['username']?>
</li>
<li>
To: <?php echo $message['Recipient']['username']?>
</li>
<li>
Time: <?php echo $message['Message']['created']?>
</li>
<li>
Message :<?php echo $message['Message']['body']?>
</li>
</ul>

<?php echo $this->Html->link('Archive' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'archive' , $message["Message"]["id"]))?>
<br></br>
<?php echo $this->Html->link('Delete' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'delete' , $message["Message"]["id"]))?>
<?php if ($message['Message']['is_read'] == 1) {?>
<br></br>
<?php echo $this->Html->link('Mark it as Unread' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'unread' , $message["Message"]["id"]))?>
<?php }?>

	*/ ?>