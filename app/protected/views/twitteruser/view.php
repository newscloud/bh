<?php
$this->breadcrumbs=array(
	'Twitter Users'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List TwitterUser','url'=>array('index')),
	array('label'=>'Create TwitterUser','url'=>array('create')),
	array('label'=>'Update TwitterUser','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete TwitterUser','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TwitterUser','url'=>array('admin')),
);
?>

<h1>View TwitterUser #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'twitter_id',
		'is_placeholder',
		'screen_name',
		'name',
		'profile_image_url',
		'location',
		'url',
		'description',
		'followers_count',
		'friends_count',
		'statuses_count',
		'klout_score',
		'time_zone',
		'created_at',
		'modified_at',
	),
)); ?>
