<?php
$this->breadcrumbs=array(
	'Actions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Your Actions','url'=>array('admin')),
);
?>

<h1>Create Action</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>