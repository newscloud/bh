<?php

class TwitteruserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
    public $account_id;

  protected function beforeAction($action) {          
    if (isset($_POST['TwitterUser']['account_id'])) {
      Yii::app()->session['account_id']=$_POST['TwitterUser']['account_id'];
    } else if (!isset(Yii::app()->session['account_id'])) {
      Yii::app()->session['account_id']=1;      
    }
    $this->account_id =  Yii::app()->session['account_id'];
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
				'actions'=>array('index','view','followers','friends','create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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
	public function actionCreate()
	{
		$model=new TwitterUser;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TwitterUser']))
		{
			$model->attributes=$_POST['TwitterUser'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['TwitterUser']))
		{
			$model->attributes=$_POST['TwitterUser'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
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
		$dataProvider=new CActiveDataProvider('TwitterUser');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new TwitterUser('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TwitterUser']))
			$model->attributes=$_GET['TwitterUser'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionFollowers()
	{
	  if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }	  		
		$model=new TwitterUser('search');
	  $model->account_id = $this->account_id;
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TwitterUser']))
			$model->attributes=$_GET['TwitterUser'];

		$this->render('follower',array(
			'model'=>$model->follows($this->account_id),
		));
	}

	public function actionFriends()
	{
	  if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }	  		
		$model=new TwitterUser('search');
	  $model->account_id = $this->account_id;
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TwitterUser']))
			$model->attributes=$_GET['TwitterUser'];

		$this->render('friend',array(
			'model'=>$model->friends($this->account_id),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=TwitterUser::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='twitter-user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
