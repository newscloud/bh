<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'group-form',
	'enableAjaxValidation'=>false,
)); ?>

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

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<?php 
    echo CHtml::activeLabel($model,'account_id',array('label'=>'Create group with which account:')); 
    echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an Account'));
  ?>
  
	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php
    echo CHtml::activeLabel($model,'group_type',array('label'=>'Group Type:')); 
  ?>
  
  <?php echo $form->dropDownList($model,'group_type', $model->getTypeOptions()); ?>


     <div id ="section_schedule">
     <p><strong>Schedule Post or Start Time:</strong><br />
     <em>Click the field below to set date and time</em></p>
     <?php
     $this->widget(
         'ext.jui.EJuiDateTimePicker',
         array(
             'model'     => $model,
             'attribute' => 'next_publish_time',
             'language'=> 'en',
             'mode'    => 'datetime', //'datetime' or 'time' ('datetime' default)
             'options'   => array(
                 'dateFormat' => 'M d, yy',
                 'timeFormat' => 'hh:mm tt',//'hh:mm tt' default
                 'alwaysSetTime'=> true,
             ),
         )
     );
     ?>
     </div> <!-- end section schedule -->

     <div id ="section_recur">

     <p><strong>Choose Options for Your Recurring Method (optional):</strong><br />


     <?php 
         echo CHtml::activeLabel($model,'interval_size',array('label'=>'Recurring: choose an interval:')); 
         echo CHtml::activeDropDownList($model,'interval_size',Status::model()->getIntervalList(false),array('empty'=>'Select an interval'));      
     ?>

     <?php 
         echo CHtml::activeLabel($model,'max_repeats',array('label'=>'Maximum number of repeated posts:')); 
         echo CHtml::activeDropDownList($model,'max_repeats',Status::model()->getMaxRepeatList(),array('empty'=>'Select a maximum number'));      
     ?>

     <?php 
         echo CHtml::activeLabel($model,'interval_random',array('label'=>'Choose a randomization period for your intervals:')); 
         echo CHtml::activeDropDownList($model,'interval_random',Status::model()->getRandomList(),array('empty'=>'Select an interval'));      
     ?>

   </div> <!-- end recur -->

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function()
	{
	  $('#section_schedule').hide();
	  $('#section_method').hide();
  	  $("#Group_group_type").change();
  	});
  	$("#Group_group_type").change(function () {
            var option = this.value;
            if (option ==0) {
              // tweet storm
              $('#section_schedule').hide();
          	  $('#section_recur').hide();    
            } else if (option==10) {
              // recurring
              $('#section_schedule').show();
          	  $('#section_recur').show();
            }
        });  
</script>
