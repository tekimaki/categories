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

<div class="edit categories category">
	<div class="header">
		<h1>
			{if $gContent->mInfo.category_id}
				{tr}Edit {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New {$gContent->getContentTypeName()}{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{formfeedback warning=$errors}
		{form enctype="multipart/form-data" id="editcategoryform"}
			{* =-=- CUSTOM BEGIN: input -=-= *}

			{* =-=- CUSTOM END: input -=-= *}
			<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
			<div class="servicetabs">
			{jstabs id="servicetabs"}
				{* =-=- CUSTOM BEGIN: servicetabs -=-= *}

				{* =-=- CUSTOM END: servicetabs -=-= *}
				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl" display_help_tab=1}
			{/jstabs}
			</div>
			<div class="editcontainer">
			{jstabs}
				{if $preview eq 'y'}
					{jstab title="Preview"}
						{legend legend="Preview"}
						<div class="preview">
							{include file="bitpackage:categories/display_category.tpl" page=`$gContent->mInfo.category_id`}
						</div>
						{/legend}
					{/jstab}
				{/if}
				{jstab title="Edit"}
				{legend legend=$gContent->getContentTypeName()}
						<input type="hidden" name="category[category_id]" value="{$gContent->mInfo.category_id}" />
						{formfeedback warning=$errors.store}

						{if $gContent->mInfo.tail_content_id || $tailContentRef}
						<div class="row">
							{formlabel label="Parent Category" for="tail_content_id"}
							{forminput}
								<input type="hidden" name="liberty_edge[tail_content_id]" id="tail_content_id" value="{$gContent->mInfo.tail_content_id|default:$tailContentRef.content_id}" />
								{$gContent->mInfo.tail_title|default:$tailContentRef.title}
							{/forminput}
						</div>
						{/if}

						<div class="row">
							{formfeedback warning=$errors.title}
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" size="50" name="category[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>
						{textarea label="" name="category[edit]" help=""}{$gContent->mInfo.data}{/textarea}
						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

						<div class="buttonHolder row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />
							<input type="submit" name="save_category" value="{tr}Save{/tr}" />
						</div>


						{if $gBitUser->hasPermission('p_liberty_attach_attachments') }
							<div class=row>
							{legend legend="Attachments"}
								{include file="bitpackage:liberty/edit_storage.tpl"}
							{/legend}
							</div>
						{/if}


					{/legend}
				{/jstab}
			{/jstabs}
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end . -->

{/strip}
