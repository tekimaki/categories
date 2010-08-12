{strip}
<ul>
	{foreach from=$graph item=node}
	<li>
		<div class="floaticon">
		{if $gBitUser->hasPermission( 'p_categories_category_update' )}
			{smartlink ititle="Edit" ifile="edit_category.php" ibiticon="icons/accessories-text-editor" content_id=$node.content.content_id}
			{smartlink ititle="Add Sub-Category" ifile="edit_category.php" ibiticon="icons/document-new" tail_content_id=$node.content.content_id}
		{/if}
		{if $gBitUser->hasPermission( 'p_categories_category_expunge' )}
			<input type="checkbox" name="checked[]" title="{$node.content.title|escape}" value="{$node.content.content_id}" />
		{/if}
		</div>
		<h3><a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$node.content.content_id}">{$node.content.title}</a></h3>
		{if $gBitSystem->isFeatureActive( 'category_list_summary' ) eq 'y'}
			<div>{$node.content.summary|escape}</div>
		{/if}

		{include file="bitpackage:categories/graph_inc.tpl" graph=$node.nodes}
	</li>
	{/foreach}
</ul><!-- end outermost ul -->
{/strip}
