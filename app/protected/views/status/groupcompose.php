<?php
$this->breadcrumbs=array(
	'Statuses'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'Manage schedule','url'=>array('admin')),
	array('label'=>'Review log','url'=>array('statuslog/admin')),
);
*/
?>

<h1>Compose a Tweet for your Group</h1>
<?php
 echo $this->renderPartial('_groupform', array('model'=>$model,'maxCount'=>$maxCount,'groupType'=>$groupType)); ?>