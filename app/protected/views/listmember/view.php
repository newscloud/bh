<?php
$this->breadcrumbs=array(
	'List Members'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ListMember','url'=>array('index')),
	array('label'=>'Create ListMember','url'=>array('create')),
	array('label'=>'Update ListMember','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete ListMember','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ListMember','url'=>array('admin')),
);
?>

<h1>View ListMember #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'list_id',
		'member_id',
	),
)); ?>
