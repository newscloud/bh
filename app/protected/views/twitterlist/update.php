<?php
$this->breadcrumbs=array(
	'Twitter Lists'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

?>

<h1>Update List: <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_update',array('model'=>$model)); ?>