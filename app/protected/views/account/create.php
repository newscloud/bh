<?php
$this->breadcrumbs=array(
	'Accounts'=>array('index'),
	'Create',
);
?>

<h1>Create Account</h1>

<?php echo $this->renderPartial('create_form', array('model'=>$model)); ?>