<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('email-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Emails</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'email-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
	  array(
	    'header'=>'Photo',
               'name'=>'twitter_id',
               'type'=>'html',             'value'=>'(!empty($data->twitter->profile_image_url))?CHtml::image($data->twitter->profile_image_url,"",array("style"=>"width:40px;height:40px;border:1px solid black;")):"no image"',
               'htmlOptions' => array('style' => 'width: 50px;'),               
           ),    

	  array(
    'name'=>'twitter_id',
        'header' => 'Name',
         'value' => array($model,'renderName'), 
    ),
		'email',
/*		'is_approved',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
		*/
	),
)); ?>
