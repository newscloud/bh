
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/tweet.css');
$this->breadcrumbs=array(
	'Tweets',
);

/* $this->menu=array(
	array('label'=>'Create Tweet','url'=>array('create')),
	array('label'=>'Manage Tweet','url'=>array('admin')),
);*/

if(Yii::app()->user->hasFlash('pocket')) {
  $this->widget('bootstrap.widgets.TbAlert', array(
      'block'=>true, // display a larger alert block?
      'fade'=>true, // use transitions?
      'closeText'=>'×', // close link text - if set to false, no close link is displayed
      'alerts'=>array( // configurations per alert type
  	    'pocket'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
      ),
  ));
}
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
  <h1><?php echo $timeline; ?></h1>
</div> <!-- end float left -->
<br /><br /><br /><br />
<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
