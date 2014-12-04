<?php
$this->breadcrumbs=array(
	'Groups'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Add a Group','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('group-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Groups of Tweets</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'group-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(            
		          'header'=>'Account',
                'name'=>'account_id',
                'value'=>array(Account::model(),'renderAccount'), 
            ),
		'name',
		'slug',
		array(            
		          'header'=>'Type',
                'name'=>'group_type',
                'value'=>array(Group::model(),'renderGroupType'), 
            ),
            array(
              'name'=>'status',
                  'header' => 'Status',
                   'value' => array($model,'renderStatus'), 
              ),
		'stage',
    array(
		  'htmlOptions'=>array('width'=>'150px'),  		
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'header'=>'Options',
      'template'=>'{manage}  {update}  {delete}',
          'buttons'=>array
          (
              'manage' => array
              (
              'options'=>array('title'=>'Manage'),
                'label'=>'<i class="icon-list icon-large" style="margin:5px;"></i>',
                'url'=>'Yii::app()->createUrl("group/view", array("id"=>$data->id))',
              ),
          ),			
		), // end button array
	),
)); ?>
