<?php
/*
 * Main.php
 * This is the main script of ownCloud translation deployer.
 * Seong-ho Cho <shcho@gnome.org>, 2014.
 */
class Main
{
	private $poFilePrefix;
	private $coreList;
	private $appsList;

	function __construct( )
	{
		include_once( "OCTransDeployer/config/include.ini" );
		include_once( "OCTransDeployer/config/locale.ini" );

		global $LOCALE;

		// Specify input and output
		$this->poFilePrefix = dirname(__FILE__)."/../l10n/$LOCALE";
		$this->coreList = array( "lib", "core", "settings" );
		$this->appsList = array();

		$this->aggregateAppsName();
	}

	private function aggregateAppsName ( )
	{
		$dh = opendir ( $this->poFilePrefix );
		while ( $obj = readdir ( $dh ) )
		{
			if ( preg_match ( '/^[\\.]+$/', $obj ) )
				continue;
	
			$obj = preg_split ( '/\\./', $obj )[0];
	
			if ( !in_array ( $obj, $this->coreList ) )
				array_push ( $this->appsList, $obj );
		}
	}

	public function exec()
	{
		// core file
		foreach ( $this->coreList as $core )
		{
			$file = $this->poFilePrefix.$core.".po";
			$outputFile = dirname(__FILE__)."/../$core/l10n/$LOCALE.php";
			$conv = new TransConv( $file, $outputFile );
			$conv->exec();
		}

		// apps file
		foreach ( $this->appsList as $apps )
		{
			$file = $poFilePrefix.$apps.".po";
			$outputFile = dirname(__FILE__)."/../apps/$apps/l10n/$LOCALE.php";
			$conv = new TransConv( $file, $outputFile );
			$conv->exec();
		}
	}
}

$main = new Main ();
$main->exec();
?>
