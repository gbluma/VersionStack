<?php

class ProxyServlet
{

	public function __construct() 
	{
		// connect to index
		// get list of all services
	}


	public function route($method, $version="", $params=array()) 
	{
		switch($version) {
			case "v0.1.3": 
				$params['port'] = 28438;
				return $this->proxy($params); 
				break;
			case "v0.1.4": 
				$params['port'] = 28439;
				return $this->proxy($params); 
				break;
			default:
				return array(404, "text/html", "not found");
		}
	}


	public function proxy($params) 
	{
		$port = $params['port'];
		$url = "http://127.0.0.1:$port/something_new";
		
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

