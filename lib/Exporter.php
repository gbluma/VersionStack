<?php

class Exporter 
{

	// ---------------------------------------------------------------------------
	public static function listTags() 
	{
		$tags = explode("\n",trim(shell_exec("git tag")));
		return $tags;
	}

	// ---------------------------------------------------------------------------
	public static function exportTag($tag) 
	{
		$output = shell_exec("mkdir deploy/$tag && ".
			"git archive $(cat .git/refs/tags/$tag) services 2>&1 | ".
			"tar -x -C deploy/$tag 2>&1");
		return $output;
	}
	
	// ---------------------------------------------------------------------------
	public static function deployServlets()
	{
		shell_exec("rm -rf deploy/*");
		$tags = self::listTags();
		$exported = array();
		foreach($tags as $tag) {
      if (empty($tag)) continue;

			$response = self::exportTag($tag);
			//echo $response;
			if (!strstr($response,'failure')) {
				// save in list of successful tags
				$exported[] = $tag;
			} else {
				// clean up
				shell_exec("rm -rf deploy/$tag");
			}
		}
		return $exported;
	}

}
