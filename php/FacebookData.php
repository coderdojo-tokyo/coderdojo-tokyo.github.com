<?php
class FacebookData {
	private $connection;
	private $pageId;
	
	function __construct($appId, $secret, $pageId){
		$this->connection = new Facebook(array('appId'=>$appId, 'secret'=>$secret));
		$this->pageId = $pageId;
	}
	
	public function get_json($type){
		$data = array();
		switch ($type){
			case 'events':
			case 'mentors': $data = $this->$type(); break;
		}
		$json = json_encode($data);
		//file_put_contents($file, $json);
		header('Cache-Control: public,max-age=3600');
		return $json;
	}
	
	public function events(){
		$yesterday = time()-60*60*24;
		$fql = <<<________FQL
			SELECT creator, description, eid, end_time, location, name, pic, pic_big, pic_small, start_time
			FROM event
			WHERE
				eid in (
					SELECT eid 
					FROM event_member 
					WHERE uid = "{$this->pageId}" AND $yesterday < start_time)
				AND privacy = 'OPEN'
			ORDER BY start_time ASC
			LIMIT 0, 5
________FQL;
		$result = $this->connection->api(array('method'=>'fql.query','query'=>$fql));
		$data = array();
		foreach ($result as $row)
			$data[] = self::process_event($row);
		return $data;
	}
	
	private static function process_event($row){
		$youbi = array('日','月','火','水','木','金','土曜');
		$row['date'] = date('Y/m/d', $row['start_time']);
		$row['day'] = $youbi[date('w', $row['start_time'])-0];
		$row['description'] = mb_strimwidth($row['description'], 0, 400, '...', 'UTF-8');
		return $row;
	}
}