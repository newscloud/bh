<?php
$this->breadcrumbs=array(
	'Error Logs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ErrorLog','url'=>array('index')),
	array('label'=>'Create ErrorLog','url'=>array('create')),
	array('label'=>'Update ErrorLog','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete ErrorLog','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ErrorLog','url'=>array('admin')),
);
?>

<h1>View ErrorLog #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'method',
		'account_id',
		'item_id',
		'message',
		'code',
		'created_at',
		'modified_at',
	),
)); ?>
