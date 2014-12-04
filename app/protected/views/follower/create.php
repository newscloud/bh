<?php
$this->breadcrumbs=array(
	'Followers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Follower','url'=>array('index')),
	array('label'=>'Manage Follower','url'=>array('admin')),
);
?>

<h1>Create Follower</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>