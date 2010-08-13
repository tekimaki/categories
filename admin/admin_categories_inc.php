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

$gBitSystem->verifyPermission( 'p_admin' );

require_once( CATEGORIES_PKG_PATH.'BitCategory.php' );

// package settings
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

	$categoriesToggles = array_merge( $formcategoryLists );
	foreach( $categoriesToggles as $item => $data ) {
		simple_set_toggle( $item, CATEGORIES_PKG_NAME );
	}
}


// category settings
// requires LCConfig pkg to store category preferences
if( $gBitSystem->isPackageActive( 'lcconfig' ) ){
	// service preferences we want to configure
	$catRootContentIds = BitCategory::getRootContentIds();
	$gBitSmarty->assign( 'catRootContentIds', $catRootContentIds );
	// vd( $catRootContentIds );
	
	require_once( LCCONFIG_PKG_PATH.'LCConfig.php' );
	$LCConfig = LCConfig::getInstance();

	//vd( $_REQUEST );

	// deal with service preferences
	if( !empty( $_REQUEST['save_category_prefs'] )) {
		$gBitUser->verifyTicket();
		$LCConfig->mDb->StartTrans();

		foreach( array_keys( $gLibertySystem->mContentTypes ) as $ctype ) {
			foreach( $catRootContentIds as $index=>$data ) {
				$cid = $data['content_id'];
				// store pref for category root_content_ids
				if( empty( $_REQUEST['category_ids'][$cid][$ctype] ) || $_REQUEST['category_ids'][$cid][$ctype] == 'n' ){
					$LCConfig->expungeConfig( 'service_category_content_id_'.$cid, $ctype );
				}else{
					$LCConfig->storeConfig( 'service_category_content_id_'.$cid, $ctype, $_REQUEST['category_ids'][$cid][$ctype] );
				}
			}
		}

		if( empty( $feedback['error'] ) ){
			$LCConfig->mDb->CompleteTrans();
			$feedback['success'] = tra( "Services preferences were updated." );
			$LCConfig->reloadConfig();
		}
		else{
			$LCConfig->mDb->RollbackTrans();
			$LCConfig->reloadConfig();
		}
		//vd( $LCConfig->getAllConfig() );
	}
	$gBitSmarty->assign_by_ref( 'feedback', $feedback );

	// vd( $LCConfig->getAllConfig() );
	$gBitSmarty->assign_by_ref( 'LCConfigSettings', $LCConfig->getAllConfig() );
}
