<?php

class SourceHistory 
{

  // ---------------------------------------------------------------------------
  public static function getTagID($version) 
  {
    $tag_id = trim(shell_exec("git cat-file tag \"$version\" | grep object | cut -d\" \" -f2"));

    // sanity check
    if (empty($tag_id)) {
      throw new Exception("Invalid Tag ID");
    }
    return $tag_id;
  }
  
  // ---------------------------------------------------------------------------
  public static function getTreeID($tag_id) 
  {
    $tree_id = trim(shell_exec("git cat-file -p \"$tag_id\" | grep tree | cut -d\" \" -f2"));

    // sanity check
    if (empty($tree_id)) {
      throw new Exception("Unable to inspect source version.");
    }

    return $tree_id;
  }

  // ---------------------------------------------------------------------------
  public static function getFileID($tree_id, $class) 
  {
    $file_id = trim(shell_exec("git cat-file -p $tree_id | grep \"{$class}.php\" | cut -d\" \" -f3 | cut -d\"\t\" -f1"));

    // sanity check
    if (empty($file_id)) {
      throw new Exception("Unable to inspect source tree.");
    }

    return $file_id;
  }

  // ---------------------------------------------------------------------------
  public static function getSourceObject($file_id)
  {
    $content = shell_exec("git cat-file -p $file_id");

    // sanity check
    if (empty($content)) {
      throw new Exception("Unable to retrive source code.");
    }

    return $content;
  }

  // ---------------------------------------------------------------------------
  public static function getSource($version, $class)
  {
    if ($version == "latest") {
      $f = fopen("$class.php",'r');
      $c = fread($f, filesize("$class.php"));
      fclose($f);
      return $c;
    }
    $tag_guid = self::getTagID($version);
    $tree_guid = self::getTreeID($tag_guid);
    $file_guid = self::getFileId($tree_guid, $class);
    $source = self::getSourceObject($file_guid);
		return  substr(self::renameClass($source, $class, $version), 5);
  }

  // ---------------------------------------------------------------------------
  public static function renameClass($source, $class, $version)
  {
    $version = preg_replace('/[^0-9A-Za-z]/', '', $version);
    return preg_replace("/($class)/", "$1_$version", $source);

  }

  // ---------------------------------------------------------------------------
  public static function listVersions() 
  {
		$tags = explode("\n",trim(shell_exec("git tag")));
		return $tags;
  }


}
