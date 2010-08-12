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
<div class="floaticon">{bithelp}</div>

<div class="listing categories category">
	<div class="header">
		<h1>{tr}{$gContent->getContentTypeName(TRUE)}{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					{if $gBitSystem->isFeatureActive( 'categories_list_category_id' ) eq 'y'}
						<th>{smartlink ititle="Category Id" isort=category_id offset=$control.offset iorder=desc idefault=1}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'category_list_title' ) eq 'y'}
						<th>{smartlink ititle="Title" isort=title offset=$control.offset}</th>
					{/if}




					{if $gBitSystem->isFeatureActive( 'category_list_summary' ) eq 'y'}
						<th>{smartlink ititle="Text" isort=data offset=$control.offset}</th>
					{/if}

					<th>{tr}Actions{/tr}</th>
				</tr>

				{foreach item=dataItem from=$categoryList}
					<tr class="{cycle values="even,odd"}">
						{if $gBitSystem->isFeatureActive( 'list_category_id' )}
							<td><a href="{$smarty.const.CATEGORIES_PKG_URL}index.php?category_id={$dataItem.category_id|escape:"url"}" title="{$dataItem.category_id}">{$dataItem.category_id}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'category_list_title' )}
							<td><a href="{$smarty.const.CATEGORIES_PKG_URL}index.php?category_id={$dataItem.category_id|escape:"url"}" title="{$dataItem.category_id}">{$dataItem.title|escape}</a></td>
						{/if}




						{if $gBitSystem->isFeatureActive( 'category_list_summary' )}
							<td>{$dataItem.summary|escape}</td>
						{/if}


						<td class="actionicon">
						{if $gBitUser->hasPermission( 'p_categories_category_update' )}
							{smartlink ititle="Edit" ifile="edit_category.php" ibiticon="icons/accessories-text-editor" category_id=$dataItem.category_id}
						{/if}
						{if $gBitUser->hasPermission( 'p_categories_category_expunge' )}
							<input type="checkbox" name="checked[]" title="{$dataItem.title|escape}" value="{$dataItem.category_id}" />
						{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission( 'p_categories_category_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.BitBase.switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_category_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input type="submit" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .listing -->
{/strip}
