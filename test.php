<?php

$class = "EchoService";

require_once("SourceHistory.php");

$versions = SourceHistory::listVersions();

foreach ($versions as $version) {
	$src = SourceHistory::getSource($version, $class);
	echo $src;
	eval($src);
}
