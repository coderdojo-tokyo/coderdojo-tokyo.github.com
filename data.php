<?php
require_once 'php/fb/facebook.php';
require_once 'php/FacebookData.php';

date_default_timezone_set('Asia/Tokyo');

//外部から来る変数
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

$fb = new FacebookData(
	$_SERVER['HTTP_FB_APPID'],
	$_SERVER['HTTP_FB_SECRET'],
	$_SERVER['HTTP_FB_PAGEID']
);
echo $fb->get_json($type);