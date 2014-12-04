<?php
$this->breadcrumbs=array(
	'List Members'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ListMember','url'=>array('index')),
	array('label'=>'Manage ListMember','url'=>array('admin')),
);
?>

<h1>Create ListMember</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>