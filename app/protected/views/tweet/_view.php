<div class="view">
  <div class="tweet_block">
  <img class="tweet_img" src ="<?php echo $data->tweet->twitter->profile_image_url; ?>" title="<?php echo $data->tweet->twitter->name; ?>"/>
	<?php echo CHtml::link(CHtml::encode($data->tweet->twitter->name),"http://twitter.com/".CHtml::encode($data->tweet->twitter->screen_name)); 
	echo ' @'.CHtml::encode($data->tweet->twitter->screen_name);
	echo ' &middot '.CHtml::link(time_ago($data->tweet->created_at),"http://twitter.com/".CHtml::encode($data->tweet->twitter->screen_name)."/status/".$data->tweet_id);
	?>
	
	<br />
	<?php echo twitter_linkify(CHtml::encode($data->tweet->tweet_text)); ?>
	<br />
  </div>
</div>