<div class="right"><?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'tweet-form',
	'enableAjaxValidation'=>false,
)); ?>

  <?php 
      echo CHtml::activeLabel($model,'account_id',array('label'=>'Choose an account:'));
      echo CHtml::activeDropDownList($model,'account_id',Account::model()->getList(),array('empty'=>'Select an Account')).' ';

 $this->widget('bootstrap.widgets.TbButton',array(
    'buttonType' => 'submit',
  	'label' => 'Go!',
  	'size' => 'small',
  	'type'=> 'primary',
  	'url' => array('index')
  )); 
  ?>

<?php $this->endWidget(); ?>
</div> <!-- end float right -->
<div class="left">
  <h1>Your Friends</h1>
</div> <!-- end float left -->
<br /><br /><br /><br />

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'twitter-user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
	  array(
	    'header'=>'Photo',
               'name'=>'profile_image_url',
               'type'=>'html',             'value'=>'(!empty($data->profile_image_url))?CHtml::image($data->profile_image_url,"",array("style"=>"width:40px;height:40px;border: 1px solid black;")):"no image"',
               'htmlOptions' => array('style' => 'width: 50px;'),               
           ),
		'name',
		'screen_name',
		'followers_count',
		'friends_count',
		'statuses_count',
		'klout_score',
		/*
		'location',
		'url',
		'description',
		'time_zone',
		'created_at',
		'modified_at',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
