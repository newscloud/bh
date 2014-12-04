<?php
$this->breadcrumbs=array(
	'Twitter Lists'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage your lists','url'=>array('admin')),
	array('label'=>'Synchronize lists','url'=>array('sync')),
);

?>

<h1>Create a List</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>