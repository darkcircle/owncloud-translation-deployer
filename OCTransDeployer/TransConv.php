<?php
/*
 * TransConv.php
 * main code which converts from gnu gettext po file to php script
 * consists associative array and a single variable.
 * Seong-ho Cho <shcho@gnome.org>, 2014.
 */
class TransConv
{
	private $fileQueue;
	private $iFileHandler;
	private $outputFile;
	private $oFileHandler;

	private $pluralForm;
	private $msgIdStr;

	private static $MSGID_LINE = 2;
	private static $MSGID_PLURAL_LINE = 3;
	private static $MSGSTR_LINE = 4;
	private static $MSGSTR_PLURAL_LINE = 5;
	private static $GENSTR_LINE = 8;
	private static $HEADER_LINE = 10;
	private static $BLANK_LINE = 16;
	private static $PLURAL_LINE = 1;
	private static $OTHER_LINE = 0;

	function __construct( $file, $file_out )
	{
		$this->file = $file; // Queue = $file_arr;
		$this->outputFile = $file_out;

		$this->pluralForm = "";
		$this->msgIdStr = array();
	}

	public function exec()
	{
		// Phase 1 : sementic analysis
		$parseArray = $this->extractEssentialString( $this->parseFile ( $this->file ) );
	
		// Phase 2 : parse portable object data into the associative array	
		$this->parsePOText ( $parseArray );

		// Phase 3 : convert to php code
		$this->finalize( );	
	}

	public function parseFile ( $file )
	{
		$result = array();
		$this->iFileHandler = fopen ( $file, 'rb' );

		while ( $line = fgets ( $this->iFileHandler ) )
		{
			$tag = $this->classifyLine ( $line );

			if ( $tag != 0 )
			{
				$lo = new LineObject ( );
				$lo->setTag ( $tag );
				$lo->setStr ( $line );

				array_push ( $result, $lo );
			}
		}

		fclose ( $this->iFileHandler );
		return $result;
	}

	private function classifyLine ( $line )
	{
		$result = self::$OTHER_LINE;

		if ( preg_match( '/msgid\s\"[\s\S]*\"$/', $line ) )
			$result = self::$MSGID_LINE;
		else if ( preg_match ( '/msgid_plural\s\"[\s\S]+\"$/', $line ) )
			$result = self::$MSGID_PLURAL_LINE;
		else if ( preg_match( '/msgstr\s\"[\s\S]*\"$/', $line ) )
			$result = self::$MSGSTR_LINE;
		else if ( preg_match( '/msgstr\[[0-9]\]\s\"[\s\S]+\"$/', $line ) )
			$result = self::$MSGSTR_PLURAL_LINE;
		else if ( preg_match ( '/\"Plural\\-Forms\\:\s[\s\S]+\"$/', $line ) )
			$result = self::$PLURAL_LINE;
		else if ( preg_match ( '/\"[A-Z][A-Za-z]*(\\-[A-Z][a-z]*)*\\:\s[\s\S]+\"$/', $line ) )
			$result = self::$HEADER_LINE;
		else if ( preg_match ( '/\"[\s\S]+\"$/', $line ) )
			$result = self::$GENSTR_LINE;
		else if ( strcmp( "", $line ) == 0 )
			$result = self::$BLANK_LINE;
		else
			$result = self::$OTHER_LINE;

		return $result;
	}

	private function printParseArray ( $parseArray )
	{
		foreach ( $parseArray as $lo )
			print "[".$lo->getTag()."]: ".$lo->getStr();
	}

	private function extractEssentialString ( $parseArray )
	{
		$result = array();

		$asize = count ( $parseArray ); 
		$foundMsgId = false;
		for ( $i = 0 ; $i < $asize ; $i++ )
		{
			if ( $i < 2 ) continue;
			$lo = $parseArray[$i];
			$tag = $lo->getTag();
			if ( $tag == 1 || $tag == 2 || $tag == 3
				|| $tag == 4 || $tag == 5 || $tag == 8 )
			{
				if ( $tag == 2 || $tag == 3 )
				{
					if ( !$foundMsgId )
						$foundMsgId = true;
					array_push ( $result, $lo );
				}
				else if ( $tag == 8 )
				{
					if ( $foundMsgId )
						array_push ( $result, $lo );
				}
				else
				{
					array_push ( $result, $lo );
				}
			}
		}
		return $result;
	}

	private function parsePOText ( $parseArray )
	{
		$str = "";
		$keystr = "";
		$valstr = "";

		$mergeTarget = ""; // "key" or "val"

		foreach ( $parseArray as $lo )
		{
			switch ( $lo->getTag() )
			{
				case 1: // plural form
					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = str_replace ( "\\n", "", $str );
					$str = substr ( $str, 1, strlen ( $str ) - 2 );
					$str = preg_split( '/\\:\s/', $str )[1];
					$this->pluralForm = $str;
					break;

				case 2: // msgid
					if ( strcmp ( $valstr, "" ) != 0 )
					{
						if ( strpos ( $keystr, '::' ) !== false )
						{
							$this->msgIdStr[$keystr] = array();
							array_push ( $this->msgIdStr[$keystr], $valstr );
						}
						else
							$this->msgIdStr[$keystr] = $valstr;
						$keystr = "";
						$valstr = "";
					}

					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = preg_split ( '/\s\"/', $str)[1];
					$str = substr ( $str, 0, strlen ( $str ) - 1 );

					$keystr .= $str;

					$mergeTarget = "key";
					break;

				case 3: // msgid_plural
					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = preg_split ( '/\s\"/', $str)[1];
					$str = substr ( $str, 0, strlen ( $str ) - 1 );

					$keystr .= "::".$str;
					$mergeTarget = "key";
					break;

				case 4: // msgstr
					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = preg_split ( '/\s\"/', $str )[1];
					$str = substr ( $str, 0, strlen ( $str ) - 1 );

					$valstr .= $str;

					$mergeTarget = "val";
					break;

				case 5: // msgstr[0] ( plural )
					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = preg_split ( '/\s\"/', $str )[1];
					$str = substr ( $str, 0, strlen ( $str ) - 1 );

					$valstr .= $str;
					
					$mergeTarget = "val";
					break;

				case 8: // general string
					$str = $lo->getStr();
					$str = str_replace ( "\n", "", $str );
					$str = substr ( $str, 1, strlen ( $str ) - 2 );

					if ( strcmp ( $mergeTarget, "key" ) == 0 )
						$keystr .= $str;
					else if ( strcmp ( $mergeTarget, "val" ) == 0 )
						$valstr .= $str;
					
					break;
			}	
		}

		if ( strpos ( $keystr, '::' ) !== false )
		{
			$this->msgIdStr[$keystr] = array();
			array_push ( $this->msgIdStr[$keystr], $valstr );
		}
		else	
			$this->msgIdStr[$keystr] = $valstr;
	}

	private function finalize ( )
	{
		$firstlineWrote = 2;
		$str = "";

		$this->oFileHandler = fopen ( $this->outputFile, 'wb' );
		// beginning of php script
		fwrite ( $this->oFileHandler, "<?php\n", strlen ( "<?php\n" ) );

		// beginning of translations
		$translations = "\$TRANSLATIONS = array(\n";
		fwrite ( $this->oFileHandler, $translations, strlen ( $translations ) );

		// translations content
		foreach ( $this->msgIdStr as $key => $val )
		{
			if ( $firstlineWrote == 1 )
				$str = ",\n";
			else
				$firstlineWrote = 1;

			if ( is_array ( $val ) )
				$str .= "\"".$key."\" => array ( \"".$val[0]."\" )";
			else
				$str .= "\"".$key."\" => \"".$val."\"";

			fwrite ( $this->oFileHandler, $str, strlen ( $str ) );

			$str = "";
		}


		// end of translations
		fwrite ( $this->oFileHandler, "\n);\n" , strlen ( "\n);\n" ) );

		// plural form
		$plural = "\$PLURAL_FORMS = \"".$this->pluralForm."\";\n";
		fwrite ( $this->oFileHandler, $plural, strlen ( $plural ) );

		// end of php script
		fwrite ( $this->oFileHandler, "?>\n", strlen ( "?>\n" ) );

		fclose ( $this->oFileHandler );
	}
}
?>
