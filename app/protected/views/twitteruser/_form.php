<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'twitter-user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'twitter_id',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'is_placeholder',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'screen_name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'profile_image_url',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'location',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'url',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'followers_count',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'friends_count',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'statuses_count',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'klout_score',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'time_zone',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'created_at',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'modified_at',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
