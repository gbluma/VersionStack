<?php

define("ROOT_DIR", dirname(__FILE__));

require(ROOT_DIR."/lib/Reactor.php");
require(ROOT_DIR."/lib/Exporter.php");

function sig_handler() {
	echo "bailing";
	exit();
}

$tags     = array("DEV", "HEAD", "v0.2");
$servlets = array("EchoService");

$deployed_tags = Exporter::deployServlets($tags, $servlets);
if (empty($deployed_tags)) {
  die("No tags can be exported. Exiting early.\n");
}
shell_exec("rm -rf ./run/*");

foreach($deployed_tags as $tag) {
	// start a new process for each version
	$pid = pcntl_fork();
	if($pid == 0) {
		// ... inside child process
		$childpid = posix_getpid();
    $name = "EchoService-".$tag;
		
		require_once("deploy/$tag/services/EchoService.php");

    // safe PID to file
    $f = fopen("./run/$name","w");
    fwrite($f, $childpid);
    fclose($f);
		
		// start service
		$r = new Reactor();
		$r->run(new EchoService(), $childpid, "EchoService-".$tag);
	}
}


pcntl_signal(SIGINT,'sig_handler');
while(pcntl_waitpid(0, $status) != -1):
	$status = pcntl_wexitstatus($status);
	printf("Child %s exited".PHP_EOL,$status);

  // clean up PIDs
  shell_exec("rm -rf ./run/*");
endwhile;

