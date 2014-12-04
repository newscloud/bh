<?php

$baseUrl = Yii::app()->baseUrl; 
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/jquery.simplyCountable.js');
$cs->registerScriptFile($baseUrl.'/js/twitter-text.js');
$cs->registerScriptFile($baseUrl.'/js/twitter_count.js');

?>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'status-form',
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
    echo CHtml::activeLabel($model,'account_id',array('label'=>'Tweet with Account:')); 
    echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an Account'));
  ?>

  <br />
	<?php 
	echo $form->textAreaRow($model,'tweet_text',array('id'=>'tweet_text','rows'=>6, 'cols'=>50, 'class'=>'span8'));
   ?>
   <p class="right">Remaining: <span id="counter2">0</span></p>

   <?php 
       //echo CHtml::activeLabel($model,'status_type',array('label'=>'Choose a method:','style'=>'font-weight:bold;')); 
      ?> 
      <p><strong>Choose a method: </strong><em>e.g. now, scheduled, recurring or repeating</em></p>
      <?php 
       echo CHtml::activeDropDownList($model,'status_type',Status::model()->getTypeList(),array('empty'=>'Select an publication method'));      
   ?>

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

   <div id ="section_method">

   <p><strong>Choose Options for Your Repeating Method (optional):</strong><br />

     <div id ="section_recur">

   <?php 
       echo CHtml::activeLabel($model,'interval_size',array('label'=>'Recurring: choose an interval:')); 
       echo CHtml::activeDropDownList($model,'interval_size',Status::model()->getIntervalList(),array('empty'=>'Select an interval'));      
   ?>
   
   <?php 
       echo CHtml::activeLabel($model,'max_repeats',array('label'=>'Maximum number of repeated posts:')); 
       echo CHtml::activeDropDownList($model,'max_repeats',Status::model()->getMaxRepeatList(),array('empty'=>'Select a maximum number'));      
   ?>
   
 </div> <!-- end recur -->
   <div id ="section_echo">
   <?php 
       echo CHtml::activeLabel($model,'pattern',array('label'=>'Repeating: choose a pattern:')); 
       echo CHtml::activeDropDownList($model,'pattern',Status::model()->getPatternList(),array('empty'=>'Select a pattern'));      
   ?>
   </div> <!-- end echo -->

   <?php 
       echo CHtml::activeLabel($model,'interval_random',array('label'=>'Choose a randomization period for your intervals:')); 
       echo CHtml::activeDropDownList($model,'interval_random',Status::model()->getRandomList(),array('empty'=>'Select an interval'));      
   ?>

</div> <!-- end section method -->
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
	  $('#tweet_text').simplyCountable({
	    counter: '#counter2',
      maxCount: 140,
      countDirection: 'down'
	  });
	  $("#Status_status_type").change();
	});
	$("#Status_status_type").change(function () {
          var option = this.value;
          if (option ==0) {
            $('#section_schedule').hide();
        	  $('#section_method').hide();    
          } else if (option==10) {
            // schedule at a specific time
            $('#section_schedule').show();
        	  $('#section_method').hide();
          } else if (option == 50) { 
            // recurring
            $('#section_schedule').show();
        	  $('#section_method').show();    
        	  $('#section_echo').hide();
        	  $('#section_recur').show();          
          }   else if (option == 100) { 
              // recurring
              $('#section_schedule').show();
          	  $('#section_method').show();    
          	  $('#section_echo').show();
          	  $('#section_recur').hide();          
            }
      });
  
</script>
