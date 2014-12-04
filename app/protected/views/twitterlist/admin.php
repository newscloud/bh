<?php
$this->breadcrumbs=array(
	'Twitter Lists'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Create a list','url'=>array('create')),
	array('label'=>'Synchronize lists','url'=>array('sync')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('twitter-list-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Twitter Lists</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'twitter-list-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(            
		          'header'=>'Owner',
                'name'=>'owner_id',
                'value'=>array(Account::model(),'renderAccount'), 
            ),
		'name',
		'slug',
		'subscriber_count',
		'member_count',
		'mode',
    array(
		  'htmlOptions'=>array('width'=>'150px'),  		
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'header'=>'Options',
      'template'=>'{manage}  {update}  {delete}',
          'buttons'=>array
          (
              'manage' => array
              (
              'options'=>array('title'=>'Manage'),
                'label'=>'<i class="icon-list icon-large" style="margin:5px;"></i>',
                'url'=>'Yii::app()->createUrl("twitterlist/view", array("id"=>$data->id))',
              ),
/*              'sync' => array
              (
              'options'=>array('title'=>'sync'),
                'label'=>'<i class="icon-refresh icon-large" style="margin:5px;"></i>',
                'url'=>'Yii::app()->createUrl("mglist/syncList", array("id"=>$data->id))',
              ),*/
          ),			
		), // end button array 
			),
)); ?>
