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
{* Are there homeable settings? *}


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
				<div class="row submit">
					<input type="submit" name="categories_settings" value="{tr}Change preferences{/tr}" />
				</div>
			{/jstab}
{* End List Settings *}

		{/jstabs}
	{/jstab}

	{/jstabs}
{/form}
{/strip}