<?php
defined('_DP') or die("Direct access not allowed!");
$document->flushContent();
$left = new Panel(1);
$right = new Panel(2);
$document->setPanels($left, $right);
$document->generatePage();
?>
