<?php

class GroupController extends Controller
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
			array('allow',  // allow all users to perform 'storm' 
				'actions'=>array('lookup'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform these actions
				'actions'=>array('index','view','create','update','delete','publish','activate','resetstorm'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' actions
				'actions'=>array(''),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

  public function actionPublish($id) {
    // queues action to publish tweet storm 
    Group::model()->publish($id); 
    Yii::app()->user->setFlash('success','Background process to publish tweet storm created.');    
		$this->redirect(array('group/'.$id));
  }

  public function actionActivate($id) {
    // queues action to publish tweet storm 
    Group::model()->activate($id); 
		$this->redirect(array('group/'.$id));
  }

  public function actionResetstorm($id) {
    // queues action to publish tweet storm 
    Group::model()->resetStorm($id); 
		$this->redirect(array('group/'.$id));
  }
  
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
 	public function actionView($id)
 	{
 	  $model=$this->loadModel($id);
 	  $statuses = Status::model()->in_group()->in_specific_group($model->id)->search();
 	  $statuses->sort = array(
      'defaultOrder'=>'sequence ASC'
    );
 		$this->render('view',array(
 			'model'=>$model,
 			'statuses'=> $statuses,
 		));
 	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Group;
		$model->account_id = Yii::app()->session['account_id'];
		$model->max_repeats =100;
		$model->next_publish_time = date('M j, Y - H:i',time());
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['Group']))
		{
			if ($model->account_id=='' or $model->account_id==0) {
		    Yii::app()->user->setFlash('no_account','You must select an account for a group.'); 			  
			  $this->redirect(array('group/compose'));
			}
			$model->attributes=$_POST['Group'];
			$model->slug=Group::model()->slugify($model->name);
      $model->created_at = new CDbExpression('NOW()');          			
      $model->modified_at =new CDbExpression('NOW()');          			
			// convert to integer for database
			if (!is_int($_POST['Group']['next_publish_time'])) {
  			$dev_time_str = strtotime($_POST['Group']['next_publish_time']);
        // set one minute ahead if blank
        if ($dev_time_str<time()) $dev_time_str=time()+60;
        $model->next_publish_time = strtotime(date('M d, Y H:i',$dev_time_str));      			  
			}
			if($model->save())
				$this->redirect(array('index'));
		}
		// display as friendly date
		if (is_int($model->next_publish_time)) {
			$dev_time_str = $model->next_publish_time;
      // set one minute ahead if blank
      if ($dev_time_str<time()) $dev_time_str=time()+60;
      $model->next_publish_time = date('M d, Y H:i',$dev_time_str);      			  
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
    $current_account_id = $model->account_id;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Group']))
		{
			$model->attributes=$_POST['Group'];
			// convert to integer for database
			if (!is_int($_POST['Group']['next_publish_time'])) {
  			$dev_time_str = strtotime($_POST['Group']['next_publish_time']);
        // set one minute ahead if blank
        if ($dev_time_str<time()) $dev_time_str=time()+60;
        $model->next_publish_time = strtotime(date('M d, Y H:i',$dev_time_str));      			  
			}      
			if($model->save()) {
			  if ($model->account_id <> $current_account_id) {
			    // change account of status items
			    Group::model()->changeGroupStatusAccount($id,$current_account_id,$model->account_id);
			  }
				$this->redirect(array('index'));			  
			}
		}

		// display as friendly date
			$dev_time_str = $model->next_publish_time;
      // set one minute ahead if blank
      if ($dev_time_str<time()) $dev_time_str=time()+60;
      $model->next_publish_time = date('M d, Y H:i',$dev_time_str);      			  
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

	public function actionLookup($slug) {
	  $success = false;
	  $this->layout = 'storm';
    // try look up by slug
    $group = Group::model()->findByAttributes(array('slug'=>$slug));
    $embeds = Group::model()->fetchEmbeds($group->id);    
    if ($group !== null) {
      $success = true;
			$this->render('lookup',array(
			 'embeds'=>$embeds,
			 'name'=>$group->name,
			));
	  }
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
    
		$model=new Group('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Group']))
			$model->attributes=$_GET['Group'];

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
		$model=Group::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='group-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
