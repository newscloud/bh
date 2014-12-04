<?php
$this->breadcrumbs=array(
	'Error Logs'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('error-log-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Twitter API Error Log</h1>
<p>For more information, visit the <a href="https://dev.twitter.com/overview/api/response-codes">Twitter API response code documentation</a>.</p>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'error-log-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'method',
		'account_id',
		'item_id',
		'message',
		'code',
		'created_at',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
