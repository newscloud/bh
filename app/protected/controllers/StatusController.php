<?php

class StatusController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

  protected function beforeAction($action) {          
    if (isset($_POST['Status']['account_id'])) {
      Yii::app()->session['account_id']=$_POST['Status']['account_id'];
    } else if (!isset(Yii::app()->session['account_id'])) {
      Yii::app()->session['account_id']=1;      
    }
    return parent::beforeAction($action);
  }

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('compose','admin','delete','view','stop','update','groupcompose','groupupdate'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCompose()
	{	  
	  if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }
		
		$model=new Status;
		$model->account_id = Yii::app()->session['account_id'];
		$model->max_repeats =100;
		$model->next_publish_time = date('M j, Y - H:i',time());
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		if(isset($_POST['Status']))
		{
			$model->attributes=$_POST['Status'];
			if ($model->account_id=='' or $model->account_id==0) {
		    Yii::app()->user->setFlash('no_account','You must select an account before tweeting.'); 			  
			  $this->redirect(array('status/compose'));
			}
			// convert to integer for database
			if (!is_int($_POST['Status']['next_publish_time'])) {
  			$dev_time_str = strtotime($_POST['Status']['next_publish_time']);
        // set one minute ahead if blank
        if ($dev_time_str<time()) $dev_time_str=time()+60;
        $model->next_publish_time = strtotime(date('M d, Y H:i',$dev_time_str));      			  
			}
      $model->created_at =new CDbExpression('NOW()'); 
      $model->modified_at =new CDbExpression('NOW()');

			if($model->save()) {
				$this->redirect(array('admin'));        
			}
		}
		// display as friendly date
		if (is_int($model->next_publish_time)) {
			$dev_time_str = $model->next_publish_time;
      // set one minute ahead if blank
      if ($dev_time_str<time()) $dev_time_str=time()+60;
      $model->next_publish_time = date('M d, Y H:i',$dev_time_str);      			  
		}
		$this->render('compose',array(
			'model'=>$model,
		));
	}

	public function actionGroupcompose($id)
	{	  
	  if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }
		
		$model=new Status;
		// get account id by loading group id
		$group = Group::model()->findByPk($id);
		$model->account_id = $group->account_id;
		if ($group->group_type == Group::GROUP_TYPE_STORM)
		  $maxCount = 136; // less characters for tweets due to storm count prefix
		else
		  $maxCount = 140;
		$group_type = $group->group_type;
    $model->status_type = Status::STATUS_TYPE_IN_GROUP;
		$model->next_publish_time = date('M j, Y - H:i',time());
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		if(isset($_POST['Status']))
		{
			$model->attributes=$_POST['Status'];
			if ($model->account_id=='' or $model->account_id==0) {
		    Yii::app()->user->setFlash('no_account','You must select an account before tweeting.'); 			  
			  $this->redirect(array('status/compose'));
			}
			// convert to integer for database
      $model->next_publish_time = strtotime(date('M d, Y H:i',time()));      			  
  		$model->max_repeats =100;
      $model->created_at =new CDbExpression('NOW()'); 
      $model->modified_at =new CDbExpression('NOW()');
			if($model->save()) {
			  // add groupstatus entry
    	  $gs = new GroupStatus;
    	  $gs->group_id=$id;
    	  $gs->status_id=$model->id;
    	  $gs->save();			  
				$this->redirect(array('/group/'.$id));        
			}
		}
		$this->render('groupcompose',array(
			'model'=>$model,
			'maxCount'=>$maxCount,
			'groupType'=>$group_type,
		));
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Status']))
		{
			$model->attributes=$_POST['Status'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionGroupupdate($id)
	{
		$model=$this->loadModel($id);
		$gs = GroupStatus::model()->findByAttributes(array('status_id'=>$id));
		$group = Group::model()->findByPk($gs->group_id);
		if ($group->group_type == Group::GROUP_TYPE_STORM) {
		  $maxCount = 136; // less characters for tweets due to storm count prefix		  
		} else {
		  $maxCount = 140;
		}
	  $group_type = $group->group_type;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Status']))
		{
			$model->attributes=$_POST['Status'];
			if($model->save())
				$this->redirect(array('/group/'.$gs->group_id));
		}

		$this->render('groupupdate',array(
			'model'=>$model,
			'maxCount'=>$maxCount,			
			'groupType'=>$group_type,			
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Status');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	
  public function actionStop($id) {
		$model=$this->loadModel($id);
    $model->status = Status::STATUS_TERMINATED;
    $model->save();
    $this->redirect(array('admin'));
  }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Status('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Status']))
			$model->attributes=$_GET['Status'];

		$this->render('admin',array(
			'model'=>$model->not_in_group(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Status::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='status-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	  
}
