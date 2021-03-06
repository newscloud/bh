
<?php /* @var $this Controller */ 
if (Yii::app()->params['env']=='live') {  Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/statcounter.js');  
} 
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/main.js'); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
  <link rel="icon" type="image/gif" href="http://cloud.geogram.com/favicon.gif" />
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	<!-- font-awesome -->
  <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
  <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" >

		<?php $this->widget('bootstrap.widgets.TbNavbar', array(
	'brand' => 'Birdhouse', // '<img src="http://cloud.geogram.com/images/geogram-logo.gif" style="height:30px;">',
	'brandUrl'=> array('/tweet/index'), // Yii::app()->getBaseUrl(true),
	'collapse' => true,
	'items' => array(
	  (!Yii::app()->user->isGuest ? array(	   
			'class' => 'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-left'),
			'items' => array(
    		array('label'=>'Me', 'items'=> array(
          array('url'=>array('/tweet/me'), 'label'=>'Your tweets'),
          array('url'=>array('/tweet/mentions'), 'label'=>'Mentions'),
          array('url'=>array('/tweet/favorites'), 'label'=>'Favorites'),
            array('url'=>array('/twitteruser/friends'), 'label'=>'Friends'),
            array('url'=>array('/twitteruser/followers'), 'label'=>'Followers'),
            array('url'=>array('/email/admin'), 'label'=>'Emails'),
          )),
				array('label'=>'Compose', 'items'=> array(
          array('url'=>array('/status/compose'), 'label'=>'Schedule a tweet'),
          array('url'=>array('/group'), 'label'=>'Group tweets'),
          array('url'=>array('/status/admin'), 'label'=>'Manage schedule'),
          array('url'=>array('/statuslog/admin'), 'label'=>'Review log'),
        )),
				array('label'=>'Lists', 'url'=>array('/twitterlist/admin'), ),
				array('label'=>'Reports', 'items'=> array(
          array('url'=>array('/errorlog/admin'), 'label'=>'Error log'),
        )),
				array('label'=>'Actions', 'url'=>array('/action/admin'), ),
				array('label'=>'Accounts', 'url'=>array('/account/admin'), ),
			)
  	  ) : array() ),

		array(
			'class' => 'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
			'items' => array(
        array('label'=>'About', 'items'=> array(
 array('url'=>'http://code.tutsplus.com/tutorials/building-with-the-twitter-api-getting-started--cms-22192', 'label'=>'Building with the Twitter API'), 
 array('url'=>'https://groups.google.com/forum/#!forum/lookahead_io', 'label'=>'Support forum'),
 array('url'=>'http://lookahead.uservoice.com/forums/267071-general','label'=>'Request a feature'),
 array('url'=>'http://lookahead.io/contact', 'label'=>'Contact us'),

				)),				
				array('label'=>'Account', 'items'=> array(
          array('label'=>'Hi '.getFirstName(), 'visible'=>!Yii::app()->user->isGuest),
array('url'=>Yii::app()->getModule('user')->loginUrl, 'label'=>Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>Yii::app()->getModule('user')->t("Sign up"), 'visible'=>Yii::app()->user->isGuest),
array('label'=>'Update streams', 'url'=>array('/daemon/index'), ),
array('url'=>array('/usersetting/update'), 'label'=>'Your settings', 'visible'=>!Yii::app()->user->isGuest),
array('url'=>array('/pocket/connect'), 'label'=>'Connect Pocket'),

array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>'Sign out', 'visible'=>!Yii::app()->user->isGuest),			

				)),
			),
		)
	)
));	 ?>
	<!-- mainmenu -->
	<?php //if(isset($this->breadcrumbs))     $this->widget('bootstrap.widgets.TbBreadcrumbs', array('links'=>array('Library'=>'#', 'Data'),)); ?>
	<div class="nav_spacer">&nbsp;</div>
	  
	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
	  <div class="right"><a href="https://twitter.com/intent/user?screen_name=reifman">Follow @reifman</a></div>  
  <div class="left">&copy; <?php echo date('Y'); ?> <a href="http://lookahead.io">Lookahead Consulting</a></div>
	</div><!-- footer -->

</div><!-- page -->
</body>
</html>
