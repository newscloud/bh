<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('account_id')); ?>:</b>
	<?php echo CHtml::encode($data->account_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('slug')); ?>:</b>
	<?php echo CHtml::encode($data->slug); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('group_type')); ?>:</b>
	<?php echo CHtml::encode($data->group_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('stage')); ?>:</b>
	<?php echo CHtml::encode($data->stage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('modified_at')); ?>:</b>
	<?php echo CHtml::encode($data->modified_at); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('next_publish_time')); ?>:</b>
	<?php echo CHtml::encode($data->next_publish_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('interval_size')); ?>:</b>
	<?php echo CHtml::encode($data->interval_size); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('interval_random')); ?>:</b>
	<?php echo CHtml::encode($data->interval_random); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('max_repeats')); ?>:</b>
	<?php echo CHtml::encode($data->max_repeats); ?>
	<br />

	*/ ?>

</div>