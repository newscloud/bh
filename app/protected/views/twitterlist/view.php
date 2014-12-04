<?php
$this->breadcrumbs=array(
	'Twitter Lists'=>array('index'),
	$model->name,
);
$this->menu=array(
	array('label'=>'Import members','url'=>Yii::app()->createUrl("listmember/import", array("id"=>$model->id))),	array('label'=>'Manage your lists','url'=>array('admin')),
);
?>

<h2>View List: <?php echo $model->name; ?></h2>
<?php 
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'listmember-grid',
	'dataProvider'=>$membership,
	'type'=>'striped',
	'columns'=>array(
    array(
        'name'=>'member_id',
            'header' => 'Member',
             'value' => array($model,'renderMember'), 
        ),
        array(
    		  'htmlOptions'=>array('width'=>'150px'),  		
    			'class'=>'bootstrap.widgets.TbButtonColumn',
    			'header'=>'Options',
          'template'=>'{delete}',
              'buttons'=>array
              (
                  'delete' => array
                  (
                  'options'=>array('title'=>'Delete'),
                    'label'=>'<i class="icon-list icon-large" style="margin:5px;"></i>',
                    'url'=>'Yii::app()->createUrl("listmember/delete/", array("id"=>$data->id))',
                  ),
              ),			
    		), // end button array	
        ),
)); ?>


