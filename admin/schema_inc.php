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


$tables = array(
    'category_data' => "
		category_id I4 PRIMARY,
		content_id I4 NOTNULL 
        CONSTRAINT '
        , CONSTRAINT `category_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
		'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CATEGORIES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CATEGORIES_PKG_NAME, array(
	'description' => "Provides nested tree categorization of content",
	));

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( CATEGORIES_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( CATEGORIES_PKG_NAME, array (
	'category_data_id_seq' => array( 'start' => 1 ),
));

// Schema defaults
$defaults = array(
);
if (count($defaults) > 0) {
	$gBitInstaller->registerSchemaDefault( CATEGORIES_PKG_NAME, $defaults);
}


// User Permissions
$gBitInstaller->registerUserPermissions( CATEGORIES_PKG_NAME, array(
	array ( 'p_categories_admin'  , 'Can admin the categories package', 'admin'      , CATEGORIES_PKG_NAME ),
	array ( 'p_categories_view'  , 'Can view the categories package', 'admin'      , CATEGORIES_PKG_NAME ),
	array ( 'p_categories_categorize'  , 'Can assign content to categories'  , 'editors'      , CATEGORIES_PKG_NAME ),
	array ( 'p_category_create' , 'Can create a category entry'   , 'editors' , CATEGORIES_PKG_NAME ),
	array ( 'p_category_view'   , 'Can view category entries'     , 'basic'      , CATEGORIES_PKG_NAME ),
	array ( 'p_category_update' , 'Can update any category entry' , 'editors'    , CATEGORIES_PKG_NAME ),
	array ( 'p_category_expunge', 'Can delete any category entry' , 'admin'      , CATEGORIES_PKG_NAME ),
	array ( 'p_category_admin'  , 'Can admin any category entry'  , 'admin'      , CATEGORIES_PKG_NAME ),
));

// Default Preferences
$gBitInstaller->registerPreferences( CATEGORIES_PKG_NAME, array(
	array ( CATEGORIES_PKG_NAME , 'category_default_ordering'      , 'category_id_desc' ),
	array ( CATEGORIES_PKG_NAME , 'category_list_title'            , 'y'              ),
));

// Requirements
$gBitInstaller->registerRequirements( CATEGORIES_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.5', ),
	'libertygraph' => array( 'min' => '0.0.0', ),

));
