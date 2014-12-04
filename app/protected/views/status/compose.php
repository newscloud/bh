<?php
$this->breadcrumbs=array(
	'Statuses'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage schedule','url'=>array('admin')),
	array('label'=>'Review log','url'=>array('statuslog/admin')),
);
?>

<h1>Compose a Tweet</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>