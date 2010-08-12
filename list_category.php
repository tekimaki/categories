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


require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'categories' );

// Look up the content
require_once( CATEGORIES_PKG_PATH.'lookup_category_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// Remove category data if we don't want them anymore
if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_category_data" ) {

	// Now check permissions to remove the selected categories data
	$gContent->verifyUserPermission( 'p_category_expunge' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_category_data';
		foreach( $_REQUEST["checked"] as $del ) {
			$tmpInst = new BitCategory($del);
			if ( $tmpInst->load() && !empty( $tmpInst->mInfo['title'] )) {
				$info = $tmpInst->mInfo['title'];
			} else {
				$info = $del;
			}
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>'.$info;
		}
		$gBitSystem->confirmDialog( $formHash,
			array(
				'label' => 'Remove '.$gContent->getContentTypeName( count( $_REQUEST["checked"] )>1 ),
				'warning' => tra('Are you sure you want to delete '.count( $_REQUEST["checked"] ).' '.$gContent->getContentTypeName( count( $_REQUEST["checked"] )>1  ).' records?'),
				'error' => tra('This cannot be undone!')
			)
		);
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpInst = new BitCategory( $deleteId );
			if( !$tmpInst->load() || !$tmpInst->expunge() ) {
				array_merge( $errors, array_values( $tmpInst->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign_by_ref( 'errors', $errors );
		}
	}
}

// Create new BitCategory object
$obj = new BitCategory();
$list = $obj->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'categoryList', $list );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );


/* =-=- CUSTOM BEGIN: list -=-= */
/* =-=- CUSTOM END: list -=-= */


// Display the template
$gBitSystem->display( 'bitpackage:categories/list_category.tpl', tra( $gContent->getContentTypeName( TRUE ) ) , array( 'display_mode' => 'list' ));

