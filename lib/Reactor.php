<?php

class Reactor {

	public $acceptor = null;

	public static function parseAction($url) 
	{
		// process URL
		$chunks = explode("?", $url);
		$params = explode("/", $chunks[0]);
		$action = $params[1];
		return $action;
	}

	public static function parseParams($url) 
	{
		// process URL
		$chunks = explode("?", $url);
		$params = explode("/", $chunks[0]);
		$action = $params[1];
		array_shift($params); // strip off empty element
		array_shift($params); // strip off action

		if (count($chunks) > 1) {
			// parse GET vars
			$more_params = array();
			parse_str($chunks[1], $more_params);
			$params = array_merge($params, $more_params);
		}

		// return array
		return $params;
	}

	public function shutdown()
	{
		socket_close($this->acceptor);
	}

	public function sig_handler()
	{
		echo "bailing",PHP_EOL;
		exit;
	}
	
	public function run($service, $port, $uid) 
	{
		// starts service

		declare(ticks = 1);
		$this->acceptor = socket_create(AF_INET, SOCK_STREAM, 6);
		socket_set_option($this->acceptor, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($this->acceptor, '127.0.0.1', $port);
		socket_listen($this->acceptor, 10);

		register_shutdown_function(array($this,'shutdown'));

		$pid = pcntl_fork();
		if($pid == 0):
			// ... inside child process
			$childpid = posix_getpid();

			printf("Child %d [%s] listening on 127.0.0.1:%d".PHP_EOL, $childpid, $uid, $port);
			while(1):
				$socket = socket_accept($this->acceptor);

				// try to parse headers
				$headers = socket_read($socket,2048);
				preg_match("/(GET|POST|HEAD|PUT|DELETE) ([^\s]+) HTTP/", $headers, $matches);
				if (count($matches) > 1) {
					// capture vars
					$method = $matches[1];
					$url		= $matches[2];
					$action = self::parseAction($url);
					$params = self::parseParams($url);
				} else {
					// TODO: provide safer defaults
					$method = "GET";
					$url = "/";
					$action = "";
					$params = array();
				}

				try {

					$result = $service->route($method, $url, $action, $params);

					if (count($result) > 2) {
						$status_code = $result[0];
						$content_type = $result[1];
						$body = $result[2];

						socket_write($socket, 
							"HTTP/1.1 $status_code OK\n".
							"Date: ".date("r")."\n".
							"Content-Type: $content_type\n".
							"Content-Length: ".strlen($body)."\n\n".
							$body);

					}
				} catch (Exception $e) {
					printf("failed $e");
				}

				socket_close($socket);

				$now	= new DateTime();
				$time = $now->format("[Y/m/d H:m:s]");
				$mem	= number_format(memory_get_usage()/1024.0,0) . "k";

				echo "$time - $mem - ($method) $url" . PHP_EOL;

			endwhile;
			exit($i);
		endif;
		pcntl_signal(SIGINT,array($this,'sig_handler'));

		// keep going ...
		while(pcntl_waitpid(0, $status) != -1):
			$status = pcntl_wexitstatus($status);
			printf("Child %s exited".PHP_EOL,$status);
		endwhile;

	}

}

