<?php
$this->breadcrumbs=array(
	'List Members',
);

$this->menu=array(
	array('label'=>'Create ListMember','url'=>array('create')),
	array('label'=>'Manage ListMember','url'=>array('admin')),
);
?>

<h1>List Members</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
