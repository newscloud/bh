<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'action-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

  <?php 
      echo CHtml::activeLabel($model,'account_id',array('label'=>'Choose an account:')); 
      echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an account'));      
  ?>

  <?php 
      echo CHtml::activeLabel($model,'action',array('label'=>'Choose an action:')); 
      echo CHtml::activeDropDownList($model,'action',Action::model()->getActionList(),array('empty'=>'Select an action'));      
  ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
