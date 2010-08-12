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


// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'categories' );

$typeIds = array(
		"category_id"	);

// if a content type key id is requested load it up
$requestType = NULL;
foreach( $_REQUEST as $key => $val ) {
	if (in_array($key, $typeIds)) {
		$requestType = substr($key, 0, -3);
		break;
	}
}


// If there is an id to get, specified or default, then attempt to get it and display
if( !empty( $_REQUEST[$requestType.'_id'] ) ) {
	// Look up the content
	require_once( CATEGORIES_PKG_PATH.'lookup_'.$requestType.'_inc.php' );

	if( !$gContent->isValid() ) {
		// Check permissions to access this content in general
		$gContent->verifyViewPermission();

		// They are allowed to see that this does not exist.
		$gBitSystem->setHttpStatus( 404 );
		$gBitSystem->fatalError( tra( "The requested ".$gContent->getContentTypeName()." (id=".$_REQUEST[$requestType.'_id'].") could not be found." ) );
	}

	// Now check permissions to access this content
	$gContent->verifyViewPermission();

    // Call display services
    $displayHash = array( 'perm_name' => $gContent->mViewContentPerm );
    $gContent->invokeServices( 'content_display_function', $displayHash );

	// Add a hit to the counter
	$gContent->addHit();

	/* =-=- CUSTOM BEGIN: indexload -=-= */
	/* =-=- CUSTOM END: indexload -=-= */

	// Display the template
	$gBitSystem->display( 'bitpackage:categories/display_'.$requestType.'.tpl', htmlentities($gContent->getField('title', 'Categories '.ucfirst($requestType))) , array( 'display_mode' => 'display' ));

}else{

	/* =-=- CUSTOM BEGIN: index -=-= */
		header( 'Location: '.CATEGORIES_PKG_URL.'list_category.php' );
	/* =-=- CUSTOM END: index -=-= */

}
