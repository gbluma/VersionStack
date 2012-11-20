<?php

class EchoService
{

	public function __construct() 
	{
		// do expensive stuff here.
		$this->db = mysql_connect("localhost", "root", "........");
		if (!$this->db) { echo "unable to connect"; }
		if (!mysql_select_db("project")) { echo "unable to select db"; }
	}


	public function route($method, $action="", $params=array()) 
	{
		switch($action) {
			case "hello":	 return $this->hello($params); break;
			case "dbTest": return $this->dbTest($params); break;
			case "only_in_v012": return $this->testing($params); break;
			default			:	 return array(404, "text/html", "No action.");
		}
	}


	public function hello($params) 
	{
		$username = @$params['name'] or "unknown";
		return array(200, "text/html", 
			"<h1>Hello</h1><p>This is a paragraph</p><ul><li>$username<li>two</ul>".var_export($params, true)
		);
	}

	public function dbTest($params) 
	{
		$result = mysql_query("select * from jos_modules");
		$row = mysql_fetch_assoc($result);
		return array(200, "text/html", 
			var_export($row, true)
		);
	}

	public function testing($params) {
		return array(200, "text/html", "something noteworthy");
	}

}

