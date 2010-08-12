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





require_once( CATEGORIES_PKG_PATH.'BitCategory.php' );

$formcategoryLists = array(
	"categories_list_category_id" => array(
		'label' => 'Id',
		'note' => 'Display the category id.',
	),
	"category_list_title" => array(
		'label' => 'Title',
		'note' => 'Display the title.',
	),
	"category_list_data" => array(
		'label' => 'Body Text',
		'note' => 'Display the body text.',
	),
);
$gBitSmarty->assign( 'formcategoryLists', $formcategoryLists );





// Process the form if we've made some changes
if( !empty( $_REQUEST['categories_settings'] ) ){




	$categoriesToggles = array_merge( 
		$formcategoryLists	);
	foreach( $categoriesToggles as $item => $data ) {
		simple_set_toggle( $item, CATEGORIES_PKG_NAME );
	}
}




