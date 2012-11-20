<?php

$class = "EchoService";

require_once("SourceHistory.php");

$versions = SourceHistory::listVersions();


foreach ($versions as $version) {
	try {
		$src = SourceHistory::getSource($version, $class);
		echo $src;
		eval($src);
	} catch(Exception $e) {}
}
