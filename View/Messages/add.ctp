<div class="userMessages form">
<?php echo $this->Form->create('Message' , array('action'=>'add'));?>
	<fieldset>
	 <?php echo $this->Form->hidden('To.username', array('value'=>$to));?>
 		<legend><?php echo 'Send Message to '. $to; ?></legend>
	<?php
		
		echo $this->Form->input('title', array('label' => __('Subject', true)));
		echo $this->Form->input('body', array('label' => '', 'type' => 'richtext', 'ckeSettings' => array('buttons' => array('Bold','Italic','Underline','FontSize','TextColor','BGColor','-','NumberedList','BulletedList','Blockquote','JustifyLeft','JustifyCenter','JustifyRight','-','Link','Unlink','-', 'Image'))));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Send', true));?>
</div>