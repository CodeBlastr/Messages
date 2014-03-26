<?php if(isset($userId) && !empty($userId)): ?>
<?php $this->Html->script('plugins/jquery.form.min.js', array('inline' => false)); ?>
<?php 
	$form = $this->Form->create($userId.'Message', array('id' => $userId.'SendMessage', 'url' => '/messages/messages/send'))
			. $this->Form ->input('Message.title', array('label' => 'Subject'))
			. $this->Form->input('Message.body', array('label' => 'Message'))
			. $this->Form->hidden('Message.sender_id', array('value' => $userId))
			. $this->Form->hidden('Message.recipient_id', array('value' => $this->Session->read('Auth.User.id')))
			. $this->Form->submit('Send Message', array('class' => 'btn btn-primary'))
			. $this->Form->end()
			. $this->Html->link('Cancel', '#', array('class' => 'closepopover'));
?>

<i class="icon-bullhorn quickmessage"></i>

<script type="text/javascript">
<!--
	$(function() {

			$('.quickmessage').popover({
					title: 'Send a message <?php echo isset($name) ? ' to '.$name : '' ?>',
					content: '<?php echo $form ?>',
					html: true,
					placement: 'top'
				});

			$(document).on('click', '.closepopover', function(e) {
				e.preventDefault();
				$('.quickmessage').popover('hide');
			});

			$(document).on('submit', '#<?php echo $userId; ?>SendMessage', function(e) {
				var that = this;
				$(this).ajaxSubmit({
						success: function(data) {
							$(that).clearForm();
							$('.quickmessage').popover('hide');
							alert('Message Sent <?php isset($name) ? 'to '.$name : ''; ?> Succesfully');
						},
						error: function() {
							alert('Error: Message Not Sent');
						}
					});
				return false;
			});
		
		});
//-->
</script>

<?php endif;?>
