{strip}
{foreach name=catOptions from=$catOptions item=optionlist key=optionlist_c_id}
	<div class="row">
		{formlabel label=$optionlist.content.title for="cat_options"}
		{if $optionlist.options|@count ne 0}
			{forminput}
				<select name="cat_options[{$optionlist_c_id}][]" {*multiple="multiple" size="6" *}>
					<option value="">
						{tr}Select one...{/tr}
					</option>
					{foreach from=$optionlist.options key=content_id item=option}
						<option value="{$content_id}" {if in_array($content_id,$catGraphs)}selected="selected"{/if}>
							{foreach from=$option item=node}
								{if $node.content_id != $optionlist_c_id} &raquo;{/if} {$node.title|escape}
							{/foreach}
						</option>
					{/foreach}
				</select>
			{/forminput}
		{else}
			{forminput}
				<p>{tr}There are no categories available at the moment.{/tr}</p>
				{if $gBitUser->isAdmin()}
					{smartlink ititle="Create Category" ipackage="categories" ifile="edit_category.php"}
				{/if}
			{/forminput}
		{/if}
	</div>
{/foreach}
{/strip}
