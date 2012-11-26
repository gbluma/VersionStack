<?php

define("ROOT_DIR", dirname(__FILE__));

require(ROOT_DIR."/lib/Reactor.php");
require(ROOT_DIR."/lib/ProxyServlet.php");
		
// start service
$r = new Reactor();
$r->run(new ProxyServlet(), 9009, "ProxyServlet");