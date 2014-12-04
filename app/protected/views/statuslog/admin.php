<?php
$this->breadcrumbs=array(
	'Status Logs'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Compose a tweet','url'=>array('status/compose')),
	array('label'=>'Manage schedule','url'=>array('status/admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('status-log-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Review Logs of Posted Tweets</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'status-log-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
    array(
        'name'=>'status_id',
            'header' => 'Account',
             'value' => array($model,'renderStatusAccount'), 
        ),
        array(
            'name'=>'status_id',
                'header' => 'Type',
                 'value' => array($model,'renderStatusType'), 
            ),
    array(
        'name'=>'status_id',
            'header' => 'Tweet',
             'value' => array($model,'renderStatusId'), 
        ),
    array(
        'name'=>'posted_at',
            'header' => 'Posted at',
             'value' => array($model,'renderPostedAt'), 
        ),
        array(
            'name'=>'status_id',
                'header' => 'Next Post At',
                 'value' => array($model,'renderNextPostAt'), 
            ),
            array(
                'name'=>'stage',
                    'header' => 'Stage',
                     'value' => array($model,'renderStage'), 
                ),
	),
)); ?>
