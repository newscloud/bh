<?php
$this->breadcrumbs=array(
	'Groups',
);

?>

<h1><?php echo $name; ?></h1>
<br />
<?php
  foreach ($embeds as $e) {
    echo $e;
  }

?>
