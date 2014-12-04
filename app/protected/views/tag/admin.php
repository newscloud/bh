<?php
$this->breadcrumbs=array(
	'Tags'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Add a tag','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tag-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Tags</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'tag-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(            
		          'header'=>'Account',
                'name'=>'account_id',
                'value'=>array(Account::model(),'renderAccount'), 
            ),
		'name',
//		'last_tweet_id',
//		'last_sync',
array(
  'htmlOptions'=>array('width'=>'150px'),  		
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'header'=>'Options',
  'template'=>'{update}  {delete}',
), // end button array
	),
)); ?>
