<?php
$this->breadcrumbs=array(
	'Statuses'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Compose a tweet','url'=>array('compose')),
	array('label'=>'Review log','url'=>array('statuslog/admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('status-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Scheduled Tweets</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'status-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(            
		          'header'=>'Name',
                'name'=>'account_id',
                'value'=>array(Account::model(),'renderAccount'), 
            ),
        		array(            
        		          'header'=>'Tweet',
                        'name'=>'tweet_text',
                        'value'=>array($model,'renderTweet'), 
                    ),
          array(
            'name'=>'next_publish_time',
                'header' => 'Next post',
                 'value' => array($model,'renderPublishTime'), 
            ),
            'stage',
            array(
              'name'=>'status_type',
                  'header' => 'Type',
                   'value' => array($model,'renderStatusType'), 
              ),
              array(
                'name'=>'interval_size',
                    'header' => 'Interval',
                     'value' => array($model,'renderStatusInterval'), 
                ),
                array(
                  'name'=>'pattern',
                      'header' => 'Pattern',
                       'value' => array($model,'renderPattern'), 
                  ),
                  array(
                    'name'=>'status',
                        'header' => 'Status',
                         'value' => array($model,'renderStatus'), 
                    ),
              array(
          			'class'=>'bootstrap.widgets.TbButtonColumn',
              	'header'=>'Options',
                'template'=>'{stop} {update} {delete}',
                'buttons'=>array
                (
  /*
                  'pause' => array
                  (
                    'options'=>array('title'=>'Pause action'),
                    'label'=>'<i class="icon-stop icon-large" ></i>',
                    'url'=>'Yii::app()->createUrl("action/pause", array("id"=>$data->id))',
                  ),
  */
                  'stop' => array
                  (
                    'options'=>array('title'=>'Terminate action'),
                    'label'=>'<i class="icon-stop icon-large" ></i>',
                    'url'=>'Yii::app()->createUrl("status/stop", array("id"=>$data->id))',
                  ),
                ),

          		),            
	),
)); ?>
