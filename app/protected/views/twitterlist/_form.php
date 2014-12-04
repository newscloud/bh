<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'twitter-list-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

  <?php 
    echo CHtml::activeLabel($model,'account_id',array('label'=>'Create list with which account:')); 
    echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an Account'));
  ?>
	
	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>
	<?php
    echo CHtml::activeLabel($model,'mode',array('label'=>'List Privacy:')); 
  ?>
  
  <?php echo $form->dropDownList($model,'mode', $model->getModeOptions()); ?>


	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
