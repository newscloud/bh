<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('twitter_id')); ?>:</b>
	<?php echo CHtml::encode($data->twitter_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_placeholder')); ?>:</b>
	<?php echo CHtml::encode($data->is_placeholder); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('screen_name')); ?>:</b>
	<?php echo CHtml::encode($data->screen_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('profile_image_url')); ?>:</b>
	<?php echo CHtml::encode($data->profile_image_url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('location')); ?>:</b>
	<?php echo CHtml::encode($data->location); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('url')); ?>:</b>
	<?php echo CHtml::encode($data->url); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('followers_count')); ?>:</b>
	<?php echo CHtml::encode($data->followers_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('friends_count')); ?>:</b>
	<?php echo CHtml::encode($data->friends_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('statuses_count')); ?>:</b>
	<?php echo CHtml::encode($data->statuses_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('klout_score')); ?>:</b>
	<?php echo CHtml::encode($data->klout_score); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('time_zone')); ?>:</b>
	<?php echo CHtml::encode($data->time_zone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modified_at')); ?>:</b>
	<?php echo CHtml::encode($data->modified_at); ?>
	<br />

	*/ ?>

</div>