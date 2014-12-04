<?php
$this->breadcrumbs=array(
	'Twitter Users',
);

$this->menu=array(
	array('label'=>'Create TwitterUser','url'=>array('create')),
	array('label'=>'Manage TwitterUser','url'=>array('admin')),
);
?>

<h1>Twitter Users</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
