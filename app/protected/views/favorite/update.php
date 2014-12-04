<?php
$this->breadcrumbs=array(
	'Favorites'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Favorite','url'=>array('index')),
	array('label'=>'Create Favorite','url'=>array('create')),
	array('label'=>'View Favorite','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Favorite','url'=>array('admin')),
);
?>

<h1>Update Favorite <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>