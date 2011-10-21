<?php echo $this->Element('scaffolds/index', array('data' => $messages)); ?>



<?php 
/*

<?php if(count($messages) != 0):?>
<?php if (isset($messages[0]['Sender']))
 		$model = 'Sender'; 
 		else $model = 'Recipient';?>

<table>
  <tr>
    <th><?php echo $this->Paginator->sort(isset($messages[0]['Sender'])? 'From' : 'To',
	    	$model.'.username' )?></th>
    <th><?php echo $this->Paginator->sort('Subject', 'Message.title')?></th>
    <th><?php echo $this->Paginator->sort('Time', 'Message.created');?></th>
    <th><?php echo $this->Paginator->sort('Status', 'Message.read');?></th>
    <th>Actions</th>
  </tr>
  <?php foreach($messages as $m):?>
  <tr>
    <td><?php $user_name = $m[$model]['username']; $user_id = $m[$model]['id'];?>
      <?php echo $this->Html->link($user_name, array('plugin'=>'users','controller'=>'users' , 'action'=>'view' , $user_id));?></td>
    <td><?php $msg = $this->Text->truncate($m['Message']['title'] , 75);
		    	 echo $this->Html->link($msg, array('plugin'=>'messages','controller'=>'messages' , 'action'=>'view' , $m["Message"]["id"]))?></td>
    <td><?php echo $m['Message']['created']?></td>
    <td><?php echo $m['Message']['is_archived'] ? 'Archived ' : '';?> <?php echo $m['Message']['is_read'] ? 'Read' : 'UnRead';?></td>
    <td><?php if ($m['Message']['is_archived'] == 0) {?>
      <?php echo $this->Html->link('Archive' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'archive' , $m["Message"]["id"]));
		    }?> <?php echo $this->Html->link('Delete' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'delete' , $m["Message"]["id"]))?>
      <?php if ($m['Message']['is_read'] == 1) {?>
      <?php echo $this->Html->link('Mark Unread' , array('plugin'=>'messages','controller'=>'messages' , 'action'=>'unread' , $m["Message"]["id"]));
}?></td>
  </tr>
  <?php endforeach;?>
</table>
<?php echo $this->element('paging');?>
<?php else:?>
<h2>You dont have any <?php echo !empty($type) ? $type : null; ?> messages</h2>
<?php endif;?>
<?php $this->log($messages)?>
<div class="actions">
  <ul>
    <?php foreach($boxes as $key => $box) {?>
    <li>
      <?php if ($box != $currentBox)
			echo $this->Html->link($box, array('plugin'=>'messages', 
			'controller'=>'messages','action'=>'index', $key));
			else echo $box;?>
    </li>
    <?php }?>
  </ul>
</div>


*/ 
?>
