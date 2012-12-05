<?php

class Exporter 
{

	// ---------------------------------------------------------------------------
	public static function listTags() 
	{
		$tags = explode("\n",trim(shell_exec("git tag")));
    $tags[] = "HEAD";
    $tags[] = "DEV";
		return $tags;
	}

	// ---------------------------------------------------------------------------
	public static function exportTag($tag) 
	{
    if ($tag == "DEV") {
      $output = shell_exec("mkdir deploy/DEV && cp -r services deploy/DEV");
    } else if ($tag == "HEAD") {
      $output = shell_exec("mkdir deploy/HEAD && ".
        "git archive $(cat .git/refs/heads/master) services 2>&1 | ".
        "tar -x -C deploy/HEAD 2>&1");
    } else {
      $output = shell_exec("mkdir deploy/$tag && ".
        "git archive $(cat .git/refs/tags/$tag) services 2>&1 | ".
        "tar -x -C deploy/$tag 2>&1");
    }
		return $output;
	}
	
	// ---------------------------------------------------------------------------
	public static function deployServlets($allowedTags, $allowedServlets)
	{
		shell_exec("rm -rf deploy/*");
		$exported = array();
		foreach(self::listTags() as $tag) {
      if (empty($tag) || !in_array($tag, $allowedTags) ) continue;

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
