<?php
$this->breadcrumbs=array(
	'Status Logs',
);

$this->menu=array(
	array('label'=>'Create StatusLog','url'=>array('create')),
	array('label'=>'Manage StatusLog','url'=>array('admin')),
);
?>

<h1>Status Logs</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
