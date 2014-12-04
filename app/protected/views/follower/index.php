<?php
$this->breadcrumbs=array(
	'Followers',
);

$this->menu=array(
	array('label'=>'Create Follower','url'=>array('create')),
	array('label'=>'Manage Follower','url'=>array('admin')),
);
?>

<h1>Followers</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
