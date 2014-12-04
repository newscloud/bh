<?php
$this->breadcrumbs=array(
	'Status Logs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List StatusLog','url'=>array('index')),
	array('label'=>'Create StatusLog','url'=>array('create')),
	array('label'=>'View StatusLog','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage StatusLog','url'=>array('admin')),
);
?>

<h1>Update StatusLog <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>