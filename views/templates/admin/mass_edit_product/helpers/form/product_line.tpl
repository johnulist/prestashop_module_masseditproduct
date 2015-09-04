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

<tr class="product_{$product.id_product|intval}">
	<td style="width: 100px; text-align: center;">
		{$product.id_product|intval}
		<input name="id_product" type="hidden" value="{$product.id_product|intval}"/>
		<input name="product" class="product_checkbox" type="checkbox"/>
		<div class="wrapp_checkbox"><i class="icon-check"></i></div>
	</td>
	<td>{$product.image|escape:'quotes':'UTF-8'}</td>
	<td data-name>{$product.name|escape:'quotes':'UTF-8'}</td>
	<td data-category>{$product.category|escape:'quotes':'UTF-8'}</td>
	<td data-price>{disPrice price=$product.price currency=$currency}</td>
	<td data-price_final>{disPrice price=$product.price_final currency=$currency}</td>
	<td data-manufacturer>{$product.manufacturer|escape:'quotes':'UTF-8'}</td>
	<td data-supplier>{$product.supplier|escape:'quotes':'UTF-8'}</td>
	<td data-quantity>{$product.quantity|intval}</td>
	<td data-active><img src="../img/admin/{if $product.active}enabled.gif{else}disabled.gif{/if}"></td>
	<td data-combinations>
		{if isset($product.combinations) && is_array($product.combinations) && count($product.combinations)}
			<input type="hidden" name="products[{$product.id_product|intval}][has_combination]" value="1"/>
			<div class="selector_container">
				<div class="selector_label">
					<a class="selector_list" href="#"><i class="icon-list"></i></a>
					<span class="selector_count">0</span>
					<span class="selector_all">
						<input name="pa_{$product.id_product|intval}" type="checkbox" class="selector_checkbox" data-selector-all value="1"/>
						<span class="checkbox_styler">{l s='all' mod='masseditproduct'}</span>
					</span>
				</div>
				<div class="selector_item">
					{foreach from=$product.combinations key=id_pa item=combination}
						<div>
							<input name="{$product.id_product|intval}" data-selector-item="{$combination.id_product|intval}_{$id_pa|intval}" type="checkbox">
							<span class="pa_attributes">
								{$combination.attributes|escape:'quotes':'UTF-8'},
							</span>
							<span class="pa_quantity">
								{l s='qty' mod='masseditproduct'}: <span data-pa-quantity="{$id_pa|intval}"> {$combination.quantity|intval}</span>,
							</span>
							<span class="pa_price">
								{l s='price' mod='masseditproduct'}: <span data-pa-total-price="{$id_pa|intval}">{disPrice price=$combination.total_price currency=$currency}</span> [{l s='impact' mod='masseditproduct'} <span data-pa-price="{$id_pa|intval}">{disPrice price=$combination.price currency=$currency}</span>],
							</span>
							<span class="pa_price_final">
								{l s='price final' mod='masseditproduct'}: <span data-pa-total-price-final="{$id_pa|intval}">{disPrice price=$combination.total_price_final currency=$currency} ({l s='incl tax' mod='masseditproduct'})</span> [{l s='impact' mod='masseditproduct'} <span data-pa-price-final="{$id_pa|intval}">{disPrice price=$combination.price_final currency=$currency} ({l s='incl tax' mod='masseditproduct'})</span>]
							</span>
						</div>
					{/foreach}
				</div>
			</div>
		{else}
			<input type="hidden" name="products[{$product.id_product|intval}][has_combination]" value="0"/>
		{/if}
	</td>
</tr>