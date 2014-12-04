<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'tag-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

  <?php 
    if(Yii::app()->user->hasFlash('no_account')
      ) {
    $this->widget('bootstrap.widgets.TbAlert', array(
        'alerts'=>array( // configurations per alert type
    	    'no_account'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), 
        ),
    ));
  }
  ?>

	<?php echo $form->errorSummary($model); ?>

	<?php 
    echo CHtml::activeLabel($model,'account_id',array('label'=>'Create tag with which account:')); 
    echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an Account'));
  ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
