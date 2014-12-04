<div class="view">
  <div class="tweet_block">
  <img class="tweet_img" src ="<?php echo $data->twitter->profile_image_url; ?>" title="<?php echo $data->twitter->name; ?>"/>
	<?php echo CHtml::link(CHtml::encode($data->twitter->name),"http://twitter.com/".CHtml::encode($data->twitter->screen_name)); 
	echo ' @'.CHtml::encode($data->twitter->screen_name);
	echo ' &middot '.CHtml::link(time_ago($data->created_at),"http://twitter.com/".CHtml::encode($data->twitter->screen_name)."/status/".$data->tweet_id);
	?>
	
	<br />
	<?php echo twitter_linkify(CHtml::encode($data->tweet_text)); ?>
	<br />
  </div>
</div>