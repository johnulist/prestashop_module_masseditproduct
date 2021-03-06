{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA    <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<table class="table">
	<thead>
		<tr class="table_head">
			<th>{l s='ID' mod='masseditproduct'}</th>
			<th>{l s='Image' mod='masseditproduct'}</th>
			<th>{l s='Name' mod='masseditproduct'}</th>
			<th>{l s='Category default' mod='masseditproduct'}</th>
			<th>{l s='Price' mod='masseditproduct'}</th>
			<th>{l s='Price final' mod='masseditproduct'}</th>
			<th>{l s='Manufacturer' mod='masseditproduct'}</th>
			<th>{l s='Supplier' mod='masseditproduct'}</th>
			<th>{l s='Quantity' mod='masseditproduct'}</th>
			<th>{l s='Active' mod='masseditproduct'}</th>
			<th data-combinations>{l s='Combinations' mod='masseditproduct'}</th>
		</tr>
	</thead>
	<tbody>
	{if isset($products) && (!isset($without_product) || (isset($without_product) && !$without_product))}
		{if count($products)}
			{foreach from=$products item=product}
				{include file="./product_line.tpl" product=$product}
			{/foreach}
		{else}
			<tr class="no_products">
				<td colspan="7">{l s='No products' mod='masseditproduct'}</td>
			</tr>
		{/if}
	{/if}
	</tbody>
</table>
{if isset($products) && (!isset($without_product) || (isset($without_product) && !$without_product)) && isset($p)}
	<div class="pagination clearfix">
		{if $start!=$stop}
			<ul class="pagination">
				{if $p != 1}
					{assign var='p_previous' value=$p-1}
					<li class="pagination_previous">
						<a onclick="setPage('{$p_previous|intval}'); return false;" href="#">
							<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='masseditproduct'}</b>
						</a>
					</li>
				{else}
					<li class="disabled pagination_previous">
						<span>
							<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='masseditproduct'}</b>
						</span>
					</li>
				{/if}
				{if $start==3}
					<li>
						<a onclick="setPage('1'); return false;" href="#"">
							<span>1</span>
						</a>
					</li>
					<li>
						<a onclick="setPage('2'); return false;" href="#">
							<span>2</span>
						</a>
					</li>
				{/if}
				{if $start==2}
					<li>
						<a onclick="setPage('1'); return false;" href="#">
							<span>1</span>
						</a>
					</li>
				{/if}
				{if $start>3}
					<li>
						<a onclick="setPage('1'); return false;" href="#">
							<span>1</span>
						</a>
					</li>
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
				{/if}
				{section name=pagination start=$start loop=$stop+1 step=1}
					{if $p == $smarty.section.pagination.index}
						<li class="active current">
							<span>
								<span>{$p|escape:'html':'UTF-8'}</span>
							</span>
						</li>
					{else}
						<li>
							<a onclick="setPage('{$smarty.section.pagination.index|intval}'); return false;" href="#">
								<span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
							</a>
						</li>
					{/if}
				{/section}
				{if $pages_nb>$stop+2}
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
					<li>
						<a onclick="setPage('{$pages_nb|intval}'); return false;" href="#">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+1}
					<li>
						<a onclick="setPage('{$pages_nb|intval}'); return false;" href="#">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+2}
					<li>
						<a onclick="setPage('{$pages_nb-1|intval}'); return false;" href="#">
							<span>{$pages_nb-1|intval}</span>
						</a>
					</li>
					<li>
						<a onclick="setPage('{$pages_nb|intval}'); return false;" href="#">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb > 1 AND $p != $pages_nb}
					{assign var='p_next' value=$p+1}
					<li class="pagination_next">
						<a onclick="setPage('{$p_next|intval}'); return false;" href="#">
							<b>{l s='Next' mod='masseditproduct'}</b> <i class="icon-chevron-right"></i>
						</a>
					</li>
				{else}
					<li class="disabled pagination_next">
						<span>
							<b>{l s='Next' mod='masseditproduct'}</b> <i class="icon-chevron-right"></i>
						</span>
					</li>
				{/if}
			</ul>
		{/if}
	</div>
{/if}