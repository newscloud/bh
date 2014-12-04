<?php
$this->breadcrumbs=array(
	'Actions'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Add an Action','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('action-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Actions</h1>


<?php 

if(Yii::app()->user->hasFlash('warning')) {
  $this->widget('bootstrap.widgets.TbAlert', array(
      'block'=>true, // display a larger alert block?
      'fade'=>true, // use transitions?
      'closeText'=>'×', // close link text - if set to false, no close link is displayed
      'alerts'=>array( // configurations per alert type
  	    'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), 
      ),
  ));
}


  $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'action-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(            
		          'header'=>'Name',
                'name'=>'account_id',
                'value'=>array(Account::model(),'renderAccount'), 
            ),
		array(            
                'name'=>'action',
                'value'=>array($model,'renderActionName'), 
            ),
            'item_id',
      array(
          'name'=>'last_tweet_id',
              'header' => 'Last Tweet',
              'type'=>'raw',
               'value' => array(Tweet::model(),'renderLastTweet'), 
          ),
    array(
        'name'=>'last_checked',
            'header' => 'Last checked',
             'value' => array($model,'renderLastChecked'), 
        ),
        array(
            'name'=>'status',
                'header' => 'Status',
                 'value' => array($model,'renderStatus'), 
            ),
        		array(
        			'class'=>'bootstrap.widgets.TbButtonColumn',
            	'header'=>'Options',
              'template'=>'{stop} {update} {delete}',
              'buttons'=>array
              (
/*
                'pause' => array
                (
                  'options'=>array('title'=>'Pause action'),
                  'label'=>'<i class="icon-stop icon-large" ></i>',
                  'url'=>'Yii::app()->createUrl("action/pause", array("id"=>$data->id))',
                ),
*/
                'stop' => array
                (
                  'options'=>array('title'=>'Terminate action'),
                  'label'=>'<i class="icon-stop icon-large" ></i>',
                  'url'=>'Yii::app()->createUrl("action/stop", array("id"=>$data->id))',
                ),
              ),
              
        		),
),
	
)); ?>
