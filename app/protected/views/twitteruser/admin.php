<?php
$this->breadcrumbs=array(
	'Twitter Users'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List TwitterUser','url'=>array('index')),
	array('label'=>'Create TwitterUser','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('twitter-user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Twitter Users</h1>


<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'twitter-user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
    array(
               'name'=>'image',
               'type'=>'html',             'value'=>'(!empty($data->profile_image_url))?CHtml::image($data->profile_image_url,"",array("style"=>"width:40px;height:40px;")):"no image"',
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
