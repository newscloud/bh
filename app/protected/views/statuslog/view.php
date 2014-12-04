<?php
$this->breadcrumbs=array(
	'Status Logs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List StatusLog','url'=>array('index')),
	array('label'=>'Create StatusLog','url'=>array('create')),
	array('label'=>'Update StatusLog','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete StatusLog','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage StatusLog','url'=>array('admin')),
);
?>

<h1>View StatusLog #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'status_id',
		'posted_at',
	),
)); ?>
