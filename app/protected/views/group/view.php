<?php
$this->breadcrumbs=array(
	'Twitter Lists'=>array('index'),
	$model->name,
);
$this->menu =array();

$this->menu[]=
	array('label'=>'Add a tweet','url'=>Yii::app()->createUrl("status/groupcompose", array("id"=>$model->id)));
if ($model->group_type ==Group::GROUP_TYPE_STORM) {
  $this->menu[]=array('label'=>'Publish Storm','url'=>Yii::app()->createUrl("group/publish", array("id"=>$model->id)));
  $this->menu[]=array('label'=>'Reset Storm','url'=>Yii::app()->createUrl("group/resetstorm", array("id"=>$model->id)));
  
} else {
  if ($model->status ==Group::STATUS_TERMINATED or $model->status ==Group::STATUS_PENDING )
  $this->menu[]=array('label'=>'Activate recurrence','url'=>Yii::app()->createUrl("group/activate", array("id"=>$model->id)));  
  else if ($model->status ==Group::STATUS_ACTIVE )
    $this->menu[]=array('label'=>'Deactivate recurrence','url'=>Yii::app()->createUrl("group/activate", array("id"=>$model->id)));  
  else if ($model->status ==Group::STATUS_COMPLETE )
    $this->menu[]=array('label'=>'Reset recurrence','url'=>Yii::app()->createUrl("group/reset", array("id"=>$model->id)));  

}
	$this->menu[]=	array('label'=>'Manage groups','url'=>array('index'));

  if(Yii::app()->user->hasFlash('success')) {
    $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'×', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
    	    'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
        ),
    ));
  }

?>

<h2>View Group: <?php echo $model->name; ?></h2>
<?php 
if ($model->group_type == Group::GROUP_TYPE_STORM) {
  $col_start = array('tweet_text','sequence');
} else {
  $col_start = array('tweet_text');  
}
$buttons = array(
  'htmlOptions'=>array('width'=>'150px'),  		
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'header'=>'Options',
  'template'=>'{update}{delete}',
      'buttons'=>array
      (
          'update' => array
          (
          'options'=>array('title'=>'Update'),
            'label'=>'<i class="icon-list icon-large" style="margin:5px;"></i>',
            'url'=>'Yii::app()->createUrl("status/groupupdate/", array("id"=>$data->id))',
          ),
          'delete' => array
          (
          'options'=>array('title'=>'Delete'),
            'label'=>'<i class="icon-list icon-large" style="margin:5px;"></i>',
            'url'=>'Yii::app()->createUrl("groupstatus/delete/", array("id"=>$data->id))',
          ),
      ),			
);  // end button array	

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'groupstatus-grid',
	'dataProvider'=>$statuses,
	'type'=>'striped',
	'columns'=>array_merge($col_start,array($buttons)),
)); ?>


