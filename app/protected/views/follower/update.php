<?php
$this->breadcrumbs=array(
	'Followers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Follower','url'=>array('index')),
	array('label'=>'Create Follower','url'=>array('create')),
	array('label'=>'View Follower','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Follower','url'=>array('admin')),
);
?>

<h1>Update Follower <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>