<?php
$this->breadcrumbs=array(
	'Status Logs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List StatusLog','url'=>array('index')),
	array('label'=>'Manage StatusLog','url'=>array('admin')),
);
?>

<h1>Create StatusLog</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>