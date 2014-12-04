<?php
$this->breadcrumbs=array(
	'Error Logs',
);

$this->menu=array(
	array('label'=>'Create ErrorLog','url'=>array('create')),
	array('label'=>'Manage ErrorLog','url'=>array('admin')),
);
?>

<h1>Error Logs</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
