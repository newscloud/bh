<?php
$this->breadcrumbs=array(
	'Twitter Users'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List TwitterUser','url'=>array('index')),
	array('label'=>'Create TwitterUser','url'=>array('create')),
	array('label'=>'View TwitterUser','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage TwitterUser','url'=>array('admin')),
);
?>

<h1>Update TwitterUser <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>