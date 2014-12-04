<?php
$this->breadcrumbs=array(
	'Twitter Lists',
);

$this->menu=array(
	array('label'=>'Create TwitterList','url'=>array('create')),
	array('label'=>'Manage TwitterList','url'=>array('admin')),
);
?>

<h1>Twitter Lists</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
