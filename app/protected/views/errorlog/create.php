<?php
$this->breadcrumbs=array(
	'Error Logs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ErrorLog','url'=>array('index')),
	array('label'=>'Manage ErrorLog','url'=>array('admin')),
);
?>

<h1>Create ErrorLog</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>