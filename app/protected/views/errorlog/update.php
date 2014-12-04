<?php
$this->breadcrumbs=array(
	'Error Logs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ErrorLog','url'=>array('index')),
	array('label'=>'Create ErrorLog','url'=>array('create')),
	array('label'=>'View ErrorLog','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage ErrorLog','url'=>array('admin')),
);
?>

<h1>Update ErrorLog <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>