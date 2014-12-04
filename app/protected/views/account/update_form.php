<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'account-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

  <?php
    echo CHtml::activeLabel($model,'maximum_tweet_age',array('label'=>'Delete older tweets at Twitter?','style'=>'font-weight:bold;')); 
  ?>
  
  <?php echo $form->dropDownList($model,'maximum_tweet_age', $model->getMaximumAgeOptions()); ?>  
  <br />
  <p><strong>Archive Favorites to Pocket</strong><br />
    <em>Requires that you <a href="<?php echo Yii::app()->createUrl('/usersetting/update'); ?>">set up a Pocket key</a> and authenticate your account via Oauth via the Account menu.</em></p>
  <?php
  // echo CHtml::activeLabel($model,'archive_favorites',array('label'=>'Archive Favorites to Pocket?','style'=>'font-weight:bold;')); 
  ?>
  <?php echo $form->dropDownList($model,'archive_favorites', $model->getArchiveOptions()); ?>

  <br />

  <?php
   echo CHtml::activeLabel($model,'archive_with_delete',array('label'=>'Delete Favorites at Twitter?','style'=>'font-weight:bold;')); 
  ?>
  <?php echo $form->dropDownList($model,'archive_with_delete', $model->getArchiveDeleteOptions()); ?>
  <br />

  <?php
    echo CHtml::activeLabel($model,'archive_linked_urls',array('label'=>'Pocket Options','style'=>'font-weight:bold;')); 
  ?>
  <?php echo $form->dropDownList($model,'archive_linked_urls', $model->getArchiveLinkOptions()); ?>
  <br />

  <?php
   echo CHtml::activeLabel($model,'level',array('label'=>'Sync Activity','style'=>'font-weight:bold;')); 
  ?>
  <?php echo $form->dropDownList($model,'level', $model->getLevelOptions()); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
