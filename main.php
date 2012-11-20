<?php

define("ROOT_DIR", dirname(__FILE__));

require(ROOT_DIR."/lib/Reactor.php");
require(ROOT_DIR."/lib/Exporter.php");

function sig_handler() {
	echo "bailing";
	exit();
}

// TODO: get list of intended tags from DB
// TODO: get list of intended servlets from DB
$deployed_tags = Exporter::deployServlets();

foreach($deployed_tags as $tag) {
	// start a new process for each version
	$pid = pcntl_fork();
	if($pid == 0) {
		// ... inside child process
		$childpid = posix_getpid();
		
		require_once("deploy/$tag/services/EchoService.php");
		
		// start service
		$r = new Reactor();
		$r->run(new EchoService(), $childpid, "EchoService-".$tag);
	}
}


pcntl_signal(SIGINT,'sig_handler');
while(pcntl_waitpid(0, $status) != -1):
	$status = pcntl_wexitstatus($status);
	printf("Child %s exited".PHP_EOL,$status);
endwhile;

