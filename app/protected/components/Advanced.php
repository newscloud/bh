<?php

class Advanced extends CComponent
{
  
  public function deleteTweets($account_id) {
    // back to 4000
    $limit = 4000;
    $stats = Tweet::model()->getAccountStats($action->account_id);
    echo $stats->cnt;
    // tweets to delete
    $cnt = $stats->cnt - $limit;
    if ($cnt <0) return;
    // fetch earliest tweets tweet_id ASC LIMIT $cnt
    // delete those    
  }
}

?>