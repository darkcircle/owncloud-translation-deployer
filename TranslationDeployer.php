<?php
/*
 * TranslationDeployer.php
 * This is the main script of ownCloud translation deployer.
 * Seong-ho Cho <shcho@gnome.org>, 2014.
 */
	include_once( "OCTransDeployer/config/include.ini" );
	include_once( "OCTransDeployer/config/locale.ini" );

	global $LOCALE;
	// Specify input and output
	$po_file_prefix = dirname(__FILE__)."/../l10n/$LOCALE/";
	$core_list = array( "lib", "core", "settings" );
	$apps_list = array();

	$dh = opendir ( $po_file_prefix );
	while ( $obj = readdir ( $dh ) )
	{
		if ( preg_match ( '^/[\\.]+$/', $obj ) )
			continue;

		$obj = preg_split ( '/\\./', $obj );

		if ( !in_array ( $obj, $core_list ) )
			array_push ( $apps_list, $obj );
	}

	// core file
	foreach ( $core_list as $core )
	{
		$file = $po_file_prefix.$core.".po";
		$output_file = dirname(__FILE__)."/../$core/l10n/$LOCALE.php";
		$main = new Main( $file, $output_file );
		$main->exec();
	}

	// apps file
	foreach ( $apps_list as $apps )
	{
		$file = $po_file_prefix.$apps.".po";
		$output_file = dirname(__FILE__)."/../apps/$apps/l10n/$LOCALE.php";
		$main = new Main( $file, $output_file );
		$main->exec();
	}
?>
