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
	public static function slugify($tag)
	{
		return preg_replace('/[^0-9A-Za-z]/','',$tag);
	}
	
	// ---------------------------------------------------------------------------
	public static function exportTag($tag) 
	{
		// make tag safer
		$tag = self::slugify($tag);
		
		$output = shell_exec("rm -rf deploy/* && mkdir deploy/$tag "
			."&& git archive master services | tar -x -C deploy/$tag");
		return $output;
	}
	
	// ---------------------------------------------------------------------------
	public static function exportTags()
	{
		$tags = self::listTags();
		$exported = array();
		foreach($tags as $tag) {
			$response = self::exportTag($tag);
			if (!strstr($response,'fatal')) {
				// save in list of successful tags
				$exported[] = $tag;
			}
		}
		return $exported;
	}

}
