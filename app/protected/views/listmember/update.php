<?php
$this->breadcrumbs=array(
	'List Members'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ListMember','url'=>array('index')),
	array('label'=>'Create ListMember','url'=>array('create')),
	array('label'=>'View ListMember','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage ListMember','url'=>array('admin')),
);
?>

<h1>Update ListMember <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>