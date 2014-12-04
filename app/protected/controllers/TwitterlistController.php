<?php

class TwitterlistController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('create','update','index','view','sync','admin','delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(''),
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
	  $model=$this->loadModel($id);
		$this->render('view',array(
			'model'=>$model,
			'membership'=> ListMember::model()->search($model->list_id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new TwitterList;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TwitterList']))
		{
			$model->attributes=$_POST['TwitterList'];
			// create remotely at Twitter
		  $account = Account::model()->findByPK($model->account_id);
		  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      // retrieve tweets up until that last stored one
      $new_list= $twitter->post("lists/create",array('name'=>$model->name,'description'=>$model->description,'mode'=>$model->getModeString($model->mode))); 
			if (TwitterList::model()->isError($new_list)) {
			  // set flash error
  			var_dump($new_list);
  			yexit();
			} else {
  			$model->owner_id =$account->twitter_id;
  			$model->list_id =$new_list->id_str;
  			$model->slug=$new_list->slug;
  			$model->full_name=$new_list->full_name;
        $model->created_at = date( 'Y-m-d H:i:s', strtotime($new_list->created_at) );
        $model->modified_at =new CDbExpression('NOW()');          			
  			if($model->save())
  				$this->redirect(array('admin'));			  
			}
		} else {
		  $model->account_id = Yii::app()->session['account_id'];  		
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

		if(isset($_POST['TwitterList']))
		{
			$model->attributes=$_POST['TwitterList'];
		  $account = Account::model()->findByPK($model->account_id);
		  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      // retrieve tweets up until that last stored one
      $update_list= $twitter->post("lists/update",array('list_id'=>$model->list_id,'name'=>$model->name,'description'=>$model->description,'mode'=>$model->getModeString($model->mode))); 

			if($model->save())
				$this->redirect(array('admin'));
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
	  $model = $this->loadModel($id);
		if(Yii::app()->request->isPostRequest)
		{
		  $account = Account::model()->findByPK($model->account_id);
		  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      $dl= $twitter->post("lists/destroy",array('list_id'=>$model->list_id)); 
			if (TwitterList::model()->isError($dl)) {
			  // to do - set flash error
  			var_dump($dl);
  			yexit();
  		}
			// we only allow deletion via POST request
			$model->delete();

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
		$dataProvider=new CActiveDataProvider('TwitterList');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new TwitterList('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TwitterList']))
			$model->attributes=$_GET['TwitterList'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=TwitterList::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='twitter-list-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSync()
	{
	  TwitterList::model()->sync();
	  //TwitterList::model()->syncOne(Yii::app()->session['account_id']);
		$this->redirect(array('admin'));
	}
	
}
