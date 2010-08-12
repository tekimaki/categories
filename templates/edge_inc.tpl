{strip}
<ul>
	{foreach from=$graph item=node}
	<li>
		<h3><a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$node.content.content_id}">{$node.content.title}</a></h3>
		{include file="bitpackage:categories/edge_inc.tpl" graph=$node.nodes}
	</li>
	{/foreach}
</ul><!-- end outermost ul -->
{/strip}
