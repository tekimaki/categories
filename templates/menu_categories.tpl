{strip}
{*
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * *
 * $Id: $
 * @package categories
 * @subpackage templates
 *}
	<ul>
		{if $gBitUser->hasPermission( 'p_categories_view')}
			<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}index.php">{tr}Categories Home{/tr}</a></li>


			{if $gBitUser->hasPermission( 'p_category_view')}
				<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}list_category.php">{tr}List Categories{/tr}</a></li>
			{/if}


		{/if}


		{if $gBitUser->hasPermission( 'p_category_create')}
		<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}edit_category.php">{tr}Create Category{/tr}</a></li>
		{/if}


	</ul>
{/strip}