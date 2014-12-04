<?php
$this->breadcrumbs=array(
	'Friends',
);

$this->menu=array(
	array('label'=>'Create Friend','url'=>array('create')),
	array('label'=>'Manage Friend','url'=>array('admin')),
);
?>

<h1>Friends</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
