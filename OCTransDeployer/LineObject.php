<?php
/*
 * LineObject.php
 * data model of the line distinction brought from po file. 
 * Seong-ho Cho <shcho@gnome.org>, 2014.
 */
class LineObject
{
	private $tag;
	private $str;

	function __construct ( )
	{
		$tag = 0;
		$str = "";
	}

	public function getTag ( )
	{
		return $this->tag;
	}

	public function setTag ( $newTag )
	{
		$this->tag = $newTag;
	}

	public function getStr ( )
	{
		return $this->str;
	}

	public function setStr ( $newStr )
	{
		$this->str = $newStr;
	}
}
?>
