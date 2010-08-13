<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/**
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * *
 * $Id: $
 * @package categories
 * @subpackage class
 */

/*
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
   Portions of this file are modifiable

   Anything between the CUSTOM BEGIN: and CUSTOM END:
   comments will be preserved on regeneration of this
   file.
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/

define( 'LIBERTY_SERVICE_CATEGORIES', 'categorizing' );

global $gBitSystem;

$registerHash = array(
	'package_name' => 'categories',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'categories' ) && $gBitUser->hasPermission( 'p_categories_view' ) ) {
	$menuHash = array(
		'package_name'  => CATEGORIES_PKG_NAME,
		'index_url'     => CATEGORIES_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:categories/menu_categories.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

	// include service functions
	require_once( CATEGORIES_PKG_PATH.'BitCategory.php' );

    $gLibertySystem->registerService(
		LIBERTY_SERVICE_CATEGORIES,
		CATEGORIES_PKG_NAME,
        array(
			'content_preview_function' => 'categories_content_preview',
			'content_edit_function' => 'categories_content_edit',
			'content_store_function' => 'categories_content_store',
			'content_expunge_function' => 'categories_content_expunge',

			// templates
			'content_edit_mini_tpl' 	=> 'bitpackage:categories/service_edit_mini_inc.tpl',
			// 'content_view_tpl'          => 'bitpackage:categories/service_view_members_inc.tpl',
			// 'content_nav_tpl'           => 'bitpackage:categories/service_nav_path_inc.tpl',
        ),
        array(
			'description' => 'Content categorization.'
        )
    );

}
