<?php

class ProxyServlet
{

	public function __construct() 
	{
		// connect to index
		// get list of all services
		$pids = explode("\n", shell_exec("ls ./run/*"));
		$this->processes = array();
		foreach($pids as $pid) {
			$arr = explode('-', $pid);
			if (count($arr) > 1) 
				list($name, $version) = $arr;
				if (!empty($pid)) {
					$port = file_get_contents($pid);
					$this->processes[] = compact('name','version','port');
				}
		}
	}


	public function route($method, $url, $version="", $params=array()) 
	{
		var_dump($url);
		foreach ($this->processes as $p) {
			if ($version == $p['version']) {
				$url2 = preg_replace("#/".$version."/(.*)$#", '$1', $url);
				return $this->proxy($url2, $p['port']); 
				return;
			}
		}
		return array(404, "text/html", "not found");
	}


	public function proxy($url, $port) 
	{
		$url = "http://127.0.0.1:$port/$url";

		// curl to endpoint
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		
		
		// return content
		return array(200, "text/html", $result);
	}

}

