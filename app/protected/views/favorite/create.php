<?php
$this->breadcrumbs=array(
	'Favorites'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Favorite','url'=>array('index')),
	array('label'=>'Manage Favorite','url'=>array('admin')),
);
?>

<h1>Create Favorite</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>