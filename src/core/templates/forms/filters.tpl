{if sizeof($elements)}
	<fieldset class="listing-filters collapsible {if !$filtersset}collapsed screen{/if}">
		<legend> Filters </legend>
		<div class="collapsible-contents screen">
			{if $readonly}
				{foreach $elements as $element}
					{if $element->get('value')}
						{$element->get('title')}:
						{$element->getValueTitle()}
					{/if}
				{/foreach}
				{else}
				<form action="" method="GET">
					{foreach $elements as $element}
						{$element->render()}
					{/foreach}

					<div class="clear"></div>
					<a href="#" class="button reset-filters">
						<i class="icon-remove"></i>
						<span>Reset Filters</span>
					</a>
					<!-- Render a submit button so 'Enter' works... -->
					<input type="submit" style="display:none;"/>
					<a href="#" class="button apply-filters">
						<i class="icon-ok"></i>
						<span>Apply Filters</span>
					</a>
				</form>
			{/if}
		</div>


		{if $filtersset}
			<div class="print">
			{* The printable display for filters *}
				{foreach $elements as $element}
					{if $element->get('value')}
						{$element->get('title')}:
						{$element->getValueTitle()}
						&nbsp;&nbsp;&nbsp;
					{/if}

				{/foreach}
			</div>
		{/if}
	</fieldset>
{/if}

{script library="jqueryui"}{/script}
{script location="foot"}<script>
	$(function(){
		$('.apply-filters').click(function(){
			$(this).closest('form').submit();
			return false;
		});

		$('.reset-filters').click(function(){
			$(this).closest('form').find(':input').val('');
			$(this).closest('form').submit();
			return false;
		});
	});
</script>{/script}

{if $hassort}
	{css}<style>
		.column-sortable th[sortkey] { cursor: pointer; }
		.column-sortable th[data-sortkey] { cursor: pointer; }
		.column-sortable th i { float: right; }
		.column-sortable th i.other { visibility: hidden; }
		.column-sortable th:hover i.other { visibility: visible; }
		.column-sortable th:hover i.current { visibility: hidden; }
	</style>{/css}

	{script location="foot"}<script type="text/javascript">
		var $columnsortabletable = $('.column-sortable'),
			sortkey = "{$sortkey}",
			sortdir ="{$sortdir}",
			sortother = (sortdir == 'up' ? 'down' : 'up'),
			$tableheads = $columnsortabletable.find('th[sortkey],th[data-sortkey]');

		$tableheads.each(function(){
			var $th = $(this),
				thissortkey = $th.data('sortkey') !== undefined ? $th.data('sortkey') : $th.attr('sortkey');

			// Make sure it has a useful title.
			if(!$th.attr('title')) $th.attr('title', 'Sort by ' + $th.html());

			if(thissortkey == sortkey){
				$th.append('<i class="icon-sort-' + sortdir + ' current"></i>');
				$th.append('<i class="icon-sort-' + sortother + ' other"></i>');
			}
			else{
				$th.append('<i class="icon-sort other"></i>');
			}
		});

		$tableheads.click(function(){
			var $th = $(this), newkey, newdir, req,
				thissortkey = $th.data('sortkey') !== undefined ? $th.data('sortkey') : $th.attr('sortkey');

			if(thissortkey == sortkey){
				// Set the dir
				newkey = sortkey;

				if(sortdir == 'up') newdir = 'down';
				else newdir = 'up';
			}
			else{
				newkey = thissortkey;
				newdir = sortdir;
			}

			req = 'sortkey=' + newkey + '&sortdir=' + newdir;

			window.location.search = '?' + req;
		});
	</script>{/script}
{/if}