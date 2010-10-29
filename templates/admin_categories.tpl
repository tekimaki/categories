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
{form}
	{jstabs}

	{jstab title="Category Content Preferences"}
		<h2>{tr}Set Category Content Preferences{/tr}</h2>
		{if !$gBitSystem->isPackageActive( 'lcconfig' )}
			{tr}You can customize the Categories to assocaite different categories with different content types. However, to do so you must have the LCConfig package installed. The LCConfig package currently does not appear to be installed.{/tr}
		{else}
			{formfeedback hash=$feedback}
			{form}
				<input type="hidden" name="page" value="{$page}" />
				<table class="data">
					<caption>{tr}Available Content Types{/tr}</caption>
					{foreach from=$gLibertySystem->mContentTypes item=ctype key=c name=ctypes}
						{if $prev_package != $ctype.handler_package}
							<tr>
								<th class="alignleft">{tr}Package{/tr} - {$ctype.handler_package|ucfirst}</th>
								{foreach name=cat_ids from=$catRootContentIds item=cat}
									<th class="width25p">
										{$cat.title}
									</th>
								{/foreach}
							</tr>
							{assign var=prev_package value=$ctype.handler_package}
						{/if}
						<tr class="{cycle values="odd,even"}">
							<td title="{$c}">{$gLibertySystem->getContentTypeName($ctype.content_type_guid)}</td>
							{foreach name=cat_ids from=$catRootContentIds item=cat}
								{assign var=config_key value=service_category_content_id_`$cat.content_id`}
								<td class="aligncenter">
									<input id="{$c}_{$cat.content_id}" type="checkbox" value="{$c}" name="category_ids[{$cat.content_id}][{$c}]" title="{$cat.title}" {if $LCConfigSettings.$c.$config_key}checked="checked"{/if}/>
								</td>
							{/foreach}
						</tr>
					{/foreach}
				</table>

				<div class="submit">
					<input type="submit" name="save_category_prefs" value="{tr}Apply Changes{/tr}" />
				</div>
			{/form}
		{/if}
	{/jstab}

	{jstab title="Category Settings"}
	{jstabs}
			{jstab title="Category List Settings"}
				{legend legend="Category List Settings"}
					<input type="hidden" name="page" value="{$page}" />
					{foreach from=$formcategoryLists key=item item=output}
						<div class="row">
							{formlabel label=`$output.label` for=$item}
							{forminput}
								{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
								{formhelp note=`$output.note` page=`$output.page`}
							{/forminput}
						</div>
					{/foreach}
				{/legend}
				<div class="buttonHolder row submit">
					<input type="submit" name="categories_settings" value="{tr}Change preferences{/tr}" />
				</div>
			{/jstab}
{* End List Settings *}

		{/jstabs}
	{/jstab}

	{/jstabs}
{/form}
{/strip}
