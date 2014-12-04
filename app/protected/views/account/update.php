<?php
$this->breadcrumbs=array(
	'Accounts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Manage Accounts','url'=>array('admin')),
	array('label'=>'Add an Account','url'=>array('create')),
);
?>

<h1>Account Settings: @<?php echo $model->screen_name; ?></h1>
<?php  echo $this->renderPartial('update_form',array('model'=>$model)); ?>