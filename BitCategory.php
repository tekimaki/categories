<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/* vim: :set fdm=marker : */
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

/**
* BitCategory class
* A category label.
*
* @version $Revision: $
* @class BitCategory
*/

require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */


/**
* This is used to uniquely identify the object
*/
define( 'BITCATEGORY_CONTENT_TYPE_GUID', 'bitcategory' );

class BitCategory extends LibertyMime {
	/**
	 * mCategoryId Primary key for our Category class object & table
	 *
	 * @var array
	 * @access public
	 */
	var $mCategoryId;

	var $mVerification;

	var $mSchema;

	/**
	 * BitCategory During initialisation, be sure to call our base constructors
	 *
	 * @param numeric $pCategoryId
	 * @param numeric $pContentId
	 * @access public
	 * @return void
	 */
	function BitCategory( $pCategoryId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->mCategoryId = $pCategoryId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITCATEGORY_CONTENT_TYPE_GUID;
		$this->registerContentType( BITCATEGORY_CONTENT_TYPE_GUID, array(
			'content_type_guid'	  => BITCATEGORY_CONTENT_TYPE_GUID,
			'content_name' => 'Category',
			'content_name_plural' => 'Categories',
			'handler_class'		  => 'BitCategory',
			'handler_package'	  => 'categories',
			'handler_file'		  => 'BitCategory.php',
			'maintainer_url'	  => 'http://www.bitweaver.org'
		));
		// Permission setup
		$this->mCreateContentPerm  = 'p_category_create';
		$this->mViewContentPerm	   = 'p_category_view';
		$this->mUpdateContentPerm  = 'p_category_update';
		$this->mExpungeContentPerm = 'p_category_expunge';
		$this->mAdminContentPerm   = 'p_categories_admin';
	}

	/**
	 * load Load the data from the database
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->verifyId( $this->mCategoryId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mCategoryId ) ? 'category_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mCategoryId ) ? $this->mCategoryId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT category.*, lc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name,
				lch.`hits`,
				lf.`storage_path` as avatar,
				lfp.storage_path AS `primary_attachment_path`
				$selectSql
				FROM `".BIT_DB_PREFIX."category_data` category
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = category.`content_id` ) $joinSql
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uue.`user_id` = a.`user_id` AND uue.`avatar_attachment_id`=a.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )
				WHERE category.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mCategoryId = $result->fields['category_id'];

				$this->mInfo['creator'] = ( !empty( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = ( !empty( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_name'] = BitUser::getTitle( $this->mInfo );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();

				/* =-=- CUSTOM BEGIN: load -=-= */

				// Implement LibertyGraph
				require_once( LIBERTYGRAPH_PKG_PATH.'LibertyGraph.php' );
				$LCGraph = new LibertyGraph( $this->mContentId );
				$this->mInfo['graph'] = $LCGraph->getHeadGraphHash();

				/* =-=- CUSTOM END: load -=-= */

				LibertyMime::load();
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	* Deal with text and images, modify them apprpriately that they can be returned to the form.
	* @param $pParamHash data submitted by form - generally $_REQUEST
	* @return array of data compatible with edit form
	* @access public
	**/
	function preparePreview( &$pParamHash ){
		global $gBitSystem, $gBitUser;

		if( empty( $this->mInfo['user_id'] ) ) {
			$this->mInfo['user_id'] = $gBitUser->mUserId;
			$this->mInfo['creator_user'] = $gBitUser->getField( 'login' );
			$this->mInfo['creator_real_name'] = $gBitUser->getField( 'real_name' );
		}

		$this->mInfo['creator_user_id'] = $this->mInfo['user_id'];

		if( empty( $this->mInfo['created'] ) ){
			$this->mInfo['created'] = $gBitSystem->getUTCTime();
		}

		$this->previewFields($pParamHash);


		// Liberty should really have a preview function that handles these
		// But it doesn't so we handle them here.
		if( isset( $pParamHash['category']["title"] ) ) {
			$this->mInfo["title"] = $pParamHash['category']["title"];
		}

		if( isset( $pParamHash['category']["summary"] ) ) {
			$this->mInfo["summary"] = $pParamHash['category']["summary"];
		}

		if( isset( $pParamHash['category']["format_guid"] ) ) {
			$this->mInfo['format_guid'] = $pParamHash['category']["format_guid"];
		}

		if( isset( $pParamHash['category']["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash['category']["edit"];
			$this->mInfo['parsed_data'] = $this->parseData();
		}
	}

	/**
	 * store Any method named Store inherently implies data will be written to the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This is the ONLY method that should be called in order to store( create or update ) an category!
	 * It is very smart and will figure out what to do for you. It should be considered a black box.
	 *
	 * @param array $pParamHash hash of values that will be used to store the data
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( &$pParamHash ) {
		// Don't allow uses to cut off an abort in the middle.
		// This is particularly important for classes which will
		// touch the filesystem in some way.
		$abort = ignore_user_abort(FALSE);
		if( $this->verify( $pParamHash )
			&& LibertyMime::store( $pParamHash['category'] ) ) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."category_data";
			if( $this->mCategoryId ) {
				$locId = array( "category_id" => $pParamHash['category']['category_id'] );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['category_store'], $locId );
			} else {
				$pParamHash['category_store']['content_id'] = $pParamHash['category']['content_id'];
				if( @$this->verifyId( $pParamHash['category_id'] ) ) {
					// if pParamHash['category']['category_id'] is set, some is requesting a particular category_id. Use with caution!
					$pParamHash['category_store']['category_id'] = $pParamHash['category']['category_id'];
				} else {
					$pParamHash['category_store']['category_id'] = $this->mDb->GenID( 'category_data_id_seq' );
				}
				$this->mCategoryId = $pParamHash['category_store']['category_id'];

				$result = $this->mDb->associateInsert( $table, $pParamHash['category_store'] );
			}


			/* =-=- CUSTOM BEGIN: store -=-= */

			// Implement LibertyGraph Edge
			require_once( LIBERTYGRAPH_PKG_PATH.'LibertyEdge.php' );
			$LCEdge = new LibertyEdge();
			$pParamHash['liberty_edge']['head_content_id'] = $this->mContentId;
			$LCEdge->store( $pParamHash );

			/* =-=- CUSTOM END: store -=-= */


			$this->mDb->CompleteTrans();
			$this->load();
		} else {
			$this->mErrors['store'] = tra('Failed to save this').' category.';
		}
		// Restore previous state for user abort
		ignore_user_abort($abort);
		return( count( $this->mErrors )== 0 );
	}

	/**
	 * verify Make sure the data is safe to store
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	 * NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	 *
	 * @param array $pParamHash reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @access private
	 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
	 */
	function verify( &$pParamHash ) {
		// make sure we're all loaded up of we have a mCategoryId
		if( $this->verifyId( $this->mCategoryId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['category']['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['category']['content_type_guid'] ) ) {
			$pParamHash['category']['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['category']['content_id'] ) ) {
			$pParamHash['category']['category_store']['content_id'] = $pParamHash['category']['content_id'];
		}

		// Use $pParamHash here since it handles validation right
		// @TODO bypass when there are no fields except ids in pkgmkr 
		// $this->validateFields($pParamHash);

		if( !empty( $pParamHash['category']['data'] ) ) {
			$pParamHash['category']['edit'] = $pParamHash['category']['data'];
		}

		// If title specified truncate to make sure not too long
		// TODO: This shouldn't be required. LC should validate this.
		if( !empty( $pParamHash['category']['title'] ) ) {
			$pParamHash['category']['content_store']['title'] = substr( $pParamHash['category']['title'], 0, 160 );
		} else if( empty( $pParamHash['category']['title'] ) ) { // else is error as must have title
			$this->mErrors['title'] = tra('You must enter a title for this').' $this->getContentTypeName().';
		}

		// collapse the hash that is passed to parent class so that service data is passed through properly - need to do so before verify service call below
		$hashCopy = $pParamHash;
		$pParamHash['category'] = array_merge( $hashCopy, $pParamHash['category'] );


		/* =-=- CUSTOM BEGIN: verify -=-= */

		/* =-=- CUSTOM END: verify -=-= */


		// if we have an error we get them all by checking parent classes for additional errors and the typeMaps if there are any
		if( count( $this->mErrors ) > 0 ){
			// check errors of base class so we get them all in one go
			LibertyMime::verify( $pParamHash['category'] );
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * expunge
	 *
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function expunge() {
		global $gBitSystem;
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();


			/* =-=- CUSTOM BEGIN: expunge -=-= */

			// Implement LibertyGraph Edge
			require_once( LIBERTYGRAPH_PKG_PATH.'LibertyEdge.php' );
			$LCEdge = new LibertyEdge( $this->mContentId );
			$LCEdge->expunge();

			/* =-=- CUSTOM END: expunge -=-= */


			$query = "DELETE FROM `".BIT_DB_PREFIX."category_data` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
			}
			$this->mDb->CompleteTrans();
			// If deleting the default/home category record then unset this.
			if( $ret && $gBitSystem->getConfig( 'category_home_id' ) == $this->mCategoryId ) {
				$gBitSystem->storeConfig( 'category_home_id', 0, CATEGORY_PKG_NAME );
			}
		}
		return $ret;
	}




	/**
	 * isValid Make sure category is loaded and valid
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mCategoryId ) && @BitBase::verifyId( $this->mContentId ));
	}

	/**
	 * getList This function generates a list of records from the liberty_content database for use in a list page
	 *
	 * @param array $pParamHash
	 * @access public
	 * @return array List of category data
	 */
	function getList( &$pParamHash ) {
		global $gBitSystem;
		// this makes sure parameters used later on are set
		LibertyContent::prepGetList( $pParamHash );

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pParamHash );


		/* =-=- CUSTOM BEGIN: getList -=-= */

		// default: limit to only top level categories, e.g. categories with no tails
		if( empty( $pParamHash['all'] ) ){
			$joinSql .= " INNER JOIN `".BIT_DB_PREFIX."liberty_edge` lcedge ON ( lc.`content_id` = lcedge.`head_content_id` )";
			$whereSql .= " AND lcedge.`tail_content_id` IS NULL";

			// Implement LibertyGraph - used in iter below
			require_once( LIBERTYGRAPH_PKG_PATH.'LibertyGraph.php' );
			$LCGraph = new LibertyGraph();
		}

		/* =-=- CUSTOM END: getList -=-= */


		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "
			SELECT category.*, lc.`content_id`, lc.`title`, lc.`data` $selectSql, lc.`format_guid`, lc.`user_id`, lc.`modifier_user_id`,
				uu.`email`, uu.`login`, uu.`real_name`
			FROM `".BIT_DB_PREFIX."category_data` category
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = category.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."category_data` category
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = category.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {

			if ( $gBitSystem->isFeatureActive( 'category_list_data' ) 
				|| !empty( $pParamHash['parse_data'] )
			){
				// parse data if to be displayed in lists 
				$parseHash['format_guid']	= $res['format_guid'];
				$parseHash['content_id']	= $res['content_id'];
				$parseHash['user_id']		= $res['user_id'];
				$parseHash['data']			= $res['data'];
				$res['parsed_data'] = $this->parseData( $parseHash ); 
			}

			/* =-=- CUSTOM BEGIN: getListIter -=-= */

			// default: limit to only top level categories, e.g. categories with no tails
			// fetch their full graph
			if( empty( $pParamHash['all'] ) ){
				$LCGraph->setContentId( $res['content_id'] );
				$res['graph'] = $LCGraph->getHeadGraphHash();
			}

			/* =-=- CUSTOM END: getListIter -=-= */

			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		// add all pagination info to pParamHash
		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	 * getDisplayUrl Generates the URL to the category page
	 * 
	 * @access public
	 * @return string URL to the category page
	 */
	function getDisplayUrl() {
		global $gBitSystem;
		$ret = NULL;
		if( @$this->isValid() ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
				$ret = CATEGORIES_PKG_URL.'category/'.$this->mCategoryId;
			} else {
				$ret = CATEGORIES_PKG_URL."index.php?category_id=".$this->mCategoryId;
			}
		}
		return $ret;
	}

	/**
	 * previewFields prepares the fields in this type for preview
	 */
	function previewFields(&$pParamHash) {
		$this->prepVerify();
		LibertyValidator::preview(
		$this->mVerification['category_data'],
			$pParamHash['category'],
			$this->mInfo);
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields(&$pParamHash) {
		$this->prepVerify();
		LibertyValidator::validate(
			$this->mVerification['category_data'],
			$pParamHash['category'],
			$this, $pParamHash['category_store']);
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
		if (empty($this->mVerification['category_data'])) {


		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	public function getSchema() {
		if (empty($this->mSchema['category_data'])) {

		}


		return $this->mSchema;
	}

	// Getters for reference column options - return associative arrays formatted for generating html select inputs




	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	/* =-=- CUSTOM END: methods -=-= */


	// }}} -- end of Custom Helper Methods

}
