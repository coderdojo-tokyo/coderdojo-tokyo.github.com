<?php
date_default_timezone_set('Asia/Tokyo');

require_once 'library/Slim/Slim.php';
require_once 'library/fb/facebook.php';

$app = new Slim();
$facebook = new Facebook(array(
	'appId'=>$_SERVER['HTTP_FB_APPID'],
	'secret'=>$_SERVER['HTTP_FB_SECRET'],
));

/**
 * 今後のイベント情報
 */
$app->get('/fb/event/coming.json', function () use ($facebook) {
	$uid = $_SERVER['HTTP_FB_PAGEID'];
	$yesterday = time()-60*60*24;
	$fql = <<<____FQL
		SELECT description, eid, name, pic_small, start_time
		FROM event
		WHERE
			eid in (
				SELECT eid 
				FROM event_member 
				WHERE uid = $uid AND $yesterday < start_time)
			AND privacy = 'OPEN'
		ORDER BY start_time ASC
		LIMIT 0, 5
____FQL;
	$result = $facebook->api(array('method'=>'fql.query','query'=>$fql));
	$data = array();
	foreach ($result as $row) {
		$youbi = array('日','月','火','水','木','金','土');
		// UNIXタイムスタンプでなければ、つまりISO-8601で渡された場合は、UNIXタイムスタンプに変換
		$start_ts = preg_match('/^\d+$/', $row['start_time'])
			? $row['start_time']-0
			: strtotime($row['start_time']);
		$data[] = array(
			'description' => mb_strimwidth($row['description'], 0, 400, '...', 'UTF-8'),
			'eid' => $row['eid'],
			'name' => $row['name'],
			'pic_small' => $row['pic_small'],
			'date' => date('Y/m/d', $start_ts),
			'day' => $youbi[date('w', $start_ts)-0],
			'date_en' => date('D, M j', $start_ts),
		);
	}
	echo json_encode($data);
});

/**
 * グループのメンバー情報
 */
$app->get('/fb/group/:gid/members.json', function ($gid) use ($facebook) {
	$fql = <<<____FQL
		select uid, username, name, pic, pic_square, profile_url
		from user
		where uid in (select uid from group_member where gid = $gid)
____FQL;
	$result = $facebook->api(array('method'=>'fql.query','query'=>$fql));
	echo "TEST";
	//echo json_encode($result);
});

$app->run();
