<?php

class PocketController extends Controller
{
  public function actionConnect()
  {
    $us = UserSetting::model()->findByPk(Yii::app()->user->id);
    if ($us['pocket_consumer_key']=='') {
      Yii::app()->user->setFlash('pocket_key','Please set up your Pocket consumer key below, then try the Connect Pocket menu option again. If you don\'t have a consumer key, <a href="http://getpocket.com/developer/apps/">create an app at Pocket</a> to get one.');
      $this->redirect(array('/usersetting/update'));
    }
    $params = array ( 'consumerKey'=>$us['pocket_consumer_key']);
    $pocket = new Pocket($params);

    if (isset($_GET['authorized'])) {
    	// Convert the requestToken into an accessToken
    	// Note that a requestToken can only be covnerted once
    	// Thus refreshing this page will generate an auth error
    	$user = $pocket->convertToken($_GET['authorized']);
    	/*
    		$user['access_token']	the user's access token for calls to Pocket
    		$user['username']	the user's pocket username
    	*/
    	// Set the user's access token to be used for all subsequent calls to the Pocket API
    	$pocket->setAccessToken($user['access_token']);
      $us->pocket_access_token = $user['access_token'];
      $us->save();
      Yii::app()->user->setFlash('pocket','You are now connected to Pocket!');
      $this->redirect(Yii::app()->homeUrl);
    } else {
    	// Attempt to detect the url of the current page to redirect back to
    	// Normally you wouldn't do this
    	$redirect = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http') . '://'  . $_SERVER['HTTP_HOST'] . '/pocket/connect' . '?authorized=';

    	// Request a token from Pocket
    	$result = $pocket->requestToken($redirect);
    	/*
    		$result['redirect_uri']		this is the URL to send the user to getpocket.com to authorize your app
    		$result['request_token']	this is the request_token which you will need to use to
    						obtain the user's access token after they have authorized your app
    	*/

    	/*
    	This is a hack to redirect back to us with the requestToken
    	Normally you should save the 'request_token' in a session so it can be
    	retrieved when the user is redirected back to you
    	*/
    	$result['redirect_uri'] = str_replace(
    		urlencode('?authorized='),
    		urlencode('?authorized=' . $result['request_token']),
    		$result['redirect_uri']
    	);
    	// END HACK
      Yii::app()->request->redirect($result['redirect_uri']);
    	//header('Location: ' . $result['redirect_uri']);
    }    

  }
}
?>