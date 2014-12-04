<?php
$this->breadcrumbs=array(
	'Twitter Users'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List TwitterUser','url'=>array('index')),
	array('label'=>'Manage TwitterUser','url'=>array('admin')),
);
?>

<h1>Create TwitterUser</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>