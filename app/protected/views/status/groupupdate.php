<?php
$this->breadcrumbs=array(
	'Statuses'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

?>

<h1>Update Status <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_groupform',array('model'=>$model,'maxCount'=>$maxCount,'groupType'=>$groupType)); ?>