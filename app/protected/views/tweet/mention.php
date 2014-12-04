
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/tweet.css');
$this->breadcrumbs=array(
	'Tweets',
);

/* $this->menu=array(
	array('label'=>'Create Tweet','url'=>array('create')),
	array('label'=>'Manage Tweet','url'=>array('admin')),
);*/
?>

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
    <h1>Mentions</h1>
  </div> <!-- end float left -->
  <br /><br /><br /><br />

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_mention',
)); ?>
