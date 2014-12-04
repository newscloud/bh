<?php
$this->breadcrumbs=array(
	'Favorites',
);

$this->menu=array(
	array('label'=>'Create Favorite','url'=>array('create')),
	array('label'=>'Manage Favorite','url'=>array('admin')),
);
?>

<h1>Favorites</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
