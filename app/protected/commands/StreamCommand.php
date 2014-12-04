<?php
class StreamCommand extends CConsoleCommand
{    
    // test with php ./app/stream.php Stream
    public function run($args)
    {
        // get Twitter user account keys
        $stream_name = Yii::app()->params['twitter_stream_name'];
        $result = Account::model()->findByAttributes(array('screen_name'=>$stream_name));
        if (empty($result)) {
          echo 'Error with .ini file stream name';
          yexit();
        }
        $c = new Consumer($result['oauth_token'],$result['oauth_token_secret'],Phirehose::METHOD_USER);
        // load Twitter App keys
        $app = UserSetting::model()->loadPrimarySettings();
        $c->consumerKey = $app['twitter_key'];
        $c->consumerSecret = $app['twitter_secret'];
        $c->consume();
    }
}
