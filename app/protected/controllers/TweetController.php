<?php

class TweetController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
  public $account_id;


  protected function beforeAction($action) {       
    if (isset($_POST['Tweet']['account_id'])) {
      Yii::app()->session['account_id']=$_POST['Tweet']['account_id'];
    } else if (isset($_POST['AccountTweet']['account_id'])) {
      Yii::app()->session['account_id']=$_POST['AccountTweet']['account_id'];    
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
				'actions'=>array('favorites','update','index','view','me','mentions'),
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tweet']))
		{
			$model->attributes=$_POST['Tweet'];
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
		if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }	  
	  $this->layout='//layouts/column1';
	  // get account id
	  $model = new AccountTweet;
	  $model->account_id = $this->account_id;
		$dataProvider=new CActiveDataProvider('AccountTweet',array(
		'criteria'=>array(
            'order'=>'t.tweet_id DESC',
            'condition'=>'is_deleted = 0 and account_id = '.$this->account_id,
            'with'=>array('tweet'),
        )
      ));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,'model'=>$model,'timeline'=>'Your Timeline'
		));
	}
	
	public function actionFavorites()
	{
		if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }
	  
	  $this->layout='//layouts/column1';
	  // get account id
	  $model = new Tweet;  	
	  $model->account_id = $this->account_id;
  	$account = Account::model()->findByPK($this->account_id);
  	$result = Tweet::model()->favorites($account->twitter_id)->findAll();  	
		$dataProvider=new CActiveDataProvider(Tweet::model()->favorites($account->twitter_id),array(
		'criteria'=>array(
            'order'=>'t.tweet_id DESC',
        )
      ));
		$this->render('favorite',array(
			'dataProvider'=>$dataProvider,'model'=>$model
		));
	}	

	public function actionMe()
	{
		if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }
	  $this->layout='//layouts/column1';
	  // get account id
	  $model = new Tweet;
	  $model->account_id = $this->account_id;
		$account = Account::model()->findByPk($this->account_id);    	
		$dataProvider=new CActiveDataProvider('AccountTweet',array(
		'criteria'=>array(
		  'order'=>'t.tweet_id DESC',
		  'join'=>'LEFT JOIN tw_tweet ON t.tweet_id = tw_tweet.tweet_id',
      'condition'=>'is_deleted = 0 and account_id = '.$this->account_id.' and twitter_id='.$account->twitter_id,
        )
      ));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,'model'=>$model, 'timeline'=>'Your Tweets'
		));
	}

	public function actionMentions()
	{
		if (!UserSetting::model()->checkConfiguration(Yii::app()->user->id)) {
      Yii::app()->user->setFlash('warning','Please configure your Twitter settings.');
			$this->redirect(array('/usersetting/update'));					    
	  }
	  $this->layout='//layouts/column1';
	  // get account id
	  $model = new Tweet;
	  $model->account_id = $this->account_id;
		$account = Account::model()->findByPk($this->account_id);  
		$dataProvider=new CActiveDataProvider('Tweet',array(
		'criteria'=>array(
            'join' => 'LEFT  JOIN tw_mention ON tw_mention.tweet_id=t.tweet_id',
            'order'=>'t.created_at DESC',
            'condition'=>'tw_mention.target_id = '.$account['twitter_id'], 
            'with'=>array('twitter'),
        )
      ));
		$this->render('mention',array(
			'dataProvider'=>$dataProvider,'model'=>$model
		));
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Tweet('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tweet']))
			$model->attributes=$_GET['Tweet'];

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
		$model=Tweet::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='tweet-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
