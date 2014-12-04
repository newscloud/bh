<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('account_id')); ?>:</b>
	<?php echo CHtml::encode($data->account_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('twitter_id')); ?>:</b>
	<?php echo CHtml::encode($data->twitter_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tweet_id')); ?>:</b>
	<?php echo CHtml::encode($data->tweet_id); ?>
	<br />


</div>