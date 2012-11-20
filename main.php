<?php

require("Reactor.php");
require("EchoService.php");

// start service
$r = new Reactor();
$r->run(new EchoService(), 9009);

