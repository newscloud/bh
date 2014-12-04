<div class="view">
  <div class="tweet_block">
  <img class="tweet_img" src ="<?php echo $data['profile_image_url']; ?>" title="<?php echo $data['name']; ?>"/>
	<?php echo CHtml::link(CHtml::encode($data['name']),"http://twitter.com/".CHtml::encode($data['screen_name'])); 
	echo ' @'.CHtml::encode($data['screen_name']);
	?>
  </div>
</div>