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
<div class="{if $smarty.const._PS_VERSION_ < 1.6}custom_responsive{/if}">
<div class="popup_mep">
	<div class="popup_info_row">
		<span class="popup_info">
			{l s='Count products:' mod='masseditproduct'}
			<span class="count_products">0</span>
		</span>
		<button class="toggleList active" type="button">
			<i class="icon-list"></i>
		</button>
		<button class="clearAll" type="button">
			{l s='Clear all' mod='masseditproduct'}
		</button>
	</div>
	<div class="list_products">
	</div>
	<div>
		<div class="btn-group btn-group-radio">
			<label for="mode_search">
				<input type="radio" checked name="mode" value="mode_search" id="mode_search"/>
				<span class="btn btn-default">{l s='Select products' mod='masseditproduct'}</span>
			</label>
			<label for="mode_edit">
				<input type="radio" name="mode" value="mode_edit" id="mode_edit"/>
				<span class="btn btn-default">{l s='Begin edit' mod='masseditproduct'}</span>
			</label>
		</div>
	</div>
</div>
<div class="wrapp_content">
	<div class="panel mode_search">
		<h3 class="panel-heading">{l s='Search products' mod='masseditproduct'}</h3>
		<div class="row">
			<div class="col-lg-6 tree_custom">
				<label class="control-label col-lg-12">
					{l s='Select category by search' mod='masseditproduct'}
				</label>
				{include file="./tree.tpl"
				categories=$categories
				id_category=Configuration::get('PS_ROOT_CATEGORY')
				root=true
				view_header=true
				multiple=true
				selected_categories=[]
				name='categories'
				}
			</div>
			<div class="col-lg-6">
				<div class="row">
					<label class="control-label col-lg-12">
						{l s='Search product' mod='masseditproduct'}
					</label>
					<div class="col-lg-12">
						<div class="form-group form-group-lg">
							<div class="col-sm-9">
								<input name="search_query" class="form-control" type="text"/>
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="type_search">
									<option value="0">{l s='Name' mod='masseditproduct'}</option>
									<option value="1">{l s='Id product' mod='masseditproduct'}</option>
									<option value="2">{l s='Reference' mod='masseditproduct'}</option>
									<option value="3">{l s='EAN-13' mod='masseditproduct'}</option>
									<option value="4">{l s='UPC' mod='masseditproduct'}</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="control-label col-lg-12">
						{l s='Search by manufacturer' mod='masseditproduct'}
					</label>
					<div class="col-lg-12">
						<select id="manufacturer" class="form-control" multiple name="manufacturer[]">
							<option value="0">-</option>
							{foreach from=$manufacturers item=manufacturer}
								<option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'quotes':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<label class="control-label col-lg-12">
							{l s='Only active products' mod='masseditproduct'}
						</label>
						<div class="col-lg-12">
							{if $smarty.const._PS_VERSION_ < 1.6}
								<label class="t"><img src="../img/admin/enabled.gif"></label>
								<input name="active" value="1" type="radio"/>
								<label class="t"><img src="../img/admin/disabled.gif"></label>
								<input checked name="active" value="0" type="radio"/>
							{else}
								<div class="input-group col-lg-4">
								<span class="switch prestashop-switch">
									{foreach [1,0] as $value}
										<input
												type="radio"
												name="active"
												{if $value == 1}
													id="active_on"
												{else}
													id="active_off"
												{/if}
												value="{$value|escape:'quotes':'UTF-8'}"
												{if 0 == $value}checked="checked"{/if}
												/>
										<label
												{if $value == 1}
													for="active_on"
												{else}
													for="active_off"
												{/if}
												>
											{if $value == 1}
												{l s='Yes' mod='masseditproduct'}
											{else}
												{l s='No' mod='masseditproduct'}
											{/if}
										</label>
									{/foreach}
									<a class="slide-button btn"></a>
								</span>
								</div>
							{/if}
						</div>
					</div>
					<div class="col-lg-6">
						<label class="control-label col-lg-12">
							{l s='Only disabled products' mod='masseditproduct'}
						</label>
						<div class="col-lg-12">
							{if $smarty.const._PS_VERSION_ < 1.6}
								<label class="t"><img src="../img/admin/enabled.gif"></label>
								<input name="disable" value="1" type="radio"/>
								<label class="t"><img src="../img/admin/disabled.gif"></label>
								<input checked name="disable" value="0" type="radio"/>
							{else}
								<div class="input-group col-lg-4">
								<span class="switch prestashop-switch">
									{foreach [1,0] as $value}
										<input
												type="radio"
												name="disable"
												{if $value == 1}
													id="disable_on"
												{else}
													id="disable_off"
												{/if}
												value="{$value|escape:'quotes':'UTF-8'}"
												{if 0 == $value}checked="checked"{/if}
												/>
										<label
												{if $value == 1}
													for="disable_on"
												{else}
													for="disable_off"
												{/if}
												>
											{if $value == 1}
												{l s='Yes' mod='masseditproduct'}
											{else}
												{l s='No' mod='masseditproduct'}
											{/if}
										</label>
									{/foreach}
									<a class="slide-button btn"></a>
								</span>
								</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="row">
					<label class="control-label col-lg-12">
						{l s='How many to show products?' mod='masseditproduct'}
					</label>
					<div class="col-lg-12">
						<select class="form-control" name="how_many_show">
							<option selected value="20">20</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="300">300</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-lg-12 control_btn">
				<button id="beginSearch" class="btn btn-default">
					{l s='Search product' mod='masseditproduct'}
				</button>
			</div>
		</div>
	</div>
	<div class="panel mode_search">
		<h3 class="panel-heading">{l s='Result search product' mod='masseditproduct'}</h3>
		<div class="row table_search_product">
			<div class="alert alert-warning">{l s='Need begin search' mod='masseditproduct'}</div>
		</div>
		<div class="row_select_all">
			<button class="btn btn-default selectAll">
				{l s='Select all' mod='masseditproduct'}
			</button>
		</div>
	</div>
	<div class="panel mode_edit">
		<h3 class="panel-heading">{l s='Begin work with selected products' mod='masseditproduct'}</h3>
		<div class="message_successfully success alert alert-success" style="display: none;">
			{l s='Update successfully!' mod='masseditproduct'}
		</div>
		<div class="message_error error alert alert-danger" style="display: none;">
		</div>
		<div class="tab_container">
			<ul class="tabs">
				<li data-tab="tab1">{l s='Category' mod='masseditproduct'}</li>
				<li data-tab="tab2">{l s='Price' mod='masseditproduct'}</li>
				<li data-tab="tab3">{l s='Quantity' mod='masseditproduct'}</li>
				<li data-tab="tab4">{l s='Active' mod='masseditproduct'}</li>
				<li data-tab="tab5">{l s='Manufacturer' mod='masseditproduct'}</li>
				<li data-tab="tab6">{l s='Accessories' mod='masseditproduct'}</li>
				<li data-tab="tab7">{l s='Supplier' mod='masseditproduct'}</li>
				<li data-tab="tab8">{l s='Discount' mod='masseditproduct'}</li>
				<li data-tab="tab9">{l s='Description' mod='masseditproduct'}</li>
				<li data-tab="tab10">{l s='Shipping' mod='masseditproduct'}</li>
			</ul>
			<div class="tabs_content">
				<div id="tab1">
					<div class="row">
						<label class="control-label col-lg-12">
							{l s='Set category for all products' mod='masseditproduct'}
						</label>
						<div class="col-lg-12 tree_custom_categories">
							{include file="./tree.tpl"
							categories=$categories
							id_category=Configuration::get('PS_ROOT_CATEGORY')
							root=true
							view_header=false
							multiple=false
							selected_categories=[]
							name='category'
							}
							{*<select name="category">*}
								{*{foreach from=$simple_categories item=category}*}
									{*<option value="{$category.id_category|intval}">{$category.name|escape:'quotes':'UTF-8'}</option>*}
								{*{/foreach}*}
							{*</select>*}
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setCategoryAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab2">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Apply change for' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="change_for_product">
									<input type="radio" checked name="change_for" value="0" id="change_for_product"/>
									<span class="btn btn-default">{l s='Product' mod='masseditproduct'}</span>
								</label>
								<label for="change_for_combination">
									<input type="radio" name="change_for" value="1" id="change_for_combination"/>
									<span class="btn btn-default">{l s='Combination' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Apply change for price' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="type_price_base">
									<input type="radio" checked name="type_price" value="0" id="type_price_base"/>
									<span class="btn btn-default">{l s='Base' mod='masseditproduct'}</span>
								</label>
								<label for="type_price_final">
									<input type="radio" name="type_price" value="1" id="type_price_final"/>
									<span class="btn btn-default">{l s='Final' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='What to do with price?' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="action_price_increase_percent">
									<input type="radio" checked name="action_price" value="1" id="action_price_increase_percent"/>
									<span class="btn btn-default">{l s='Increase on %' mod='masseditproduct'}</span>
								</label>
								<label for="action_price_increase">
									<input type="radio" name="action_price" value="2" id="action_price_increase"/>
									<span class="btn btn-default">{l s='Increase on value' mod='masseditproduct'}</span>
								</label>
								<label for="action_price_reduce_percent">
									<input type="radio" name="action_price" value="3" id="action_price_reduce_percent"/>
									<span class="btn btn-default">{l s='Reduce on %' mod='masseditproduct'}</span>
								</label>
								<label for="action_price_reduce">
									<input type="radio" name="action_price" value="4" id="action_price_reduce"/>
									<span class="btn btn-default">{l s='Reduce on value' mod='masseditproduct'}</span>
								</label>
								<label for="action_price_rewrite">
									<input type="radio" name="action_price" value="5" id="action_price_rewrite"/>
									<span class="btn btn-default">{l s='Rewrite' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Write value' mod='masseditproduct'}</label>
						<div class="col-lg-4">
							<input type="text" name="price_value"/>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setPriceAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab3">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Apply change for' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="change_for_qty_product">
									<input type="radio" checked name="change_for_qty" value="0" id="change_for_qty_product"/>
									<span class="btn btn-default">{l s='Product' mod='masseditproduct'}</span>
								</label>
								<label for="change_for_qty_combination">
									<input type="radio" name="change_for_qty" value="1" id="change_for_qty_combination"/>
									<span class="btn btn-default">{l s='Combination' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='What to do with quantity?' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="action_quantity_increase">
									<input type="radio" name="action_quantity" value="1" id="action_quantity_increase"/>
									<span class="btn btn-default">{l s='Increase on value' mod='masseditproduct'}</span>
								</label>
								<label for="action_quantity_reduce">
									<input type="radio" name="action_quantity" value="2" id="action_quantity_reduce"/>
									<span class="btn btn-default">{l s='Reduce on value' mod='masseditproduct'}</span>
								</label>
								<label for="action_quantity_rewrite">
									<input checked type="radio" name="action_quantity" value="3" id="action_quantity_rewrite"/>
									<span class="btn btn-default">{l s='Rewrite' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Write quantity' mod='masseditproduct'}</label>
						<div class="col-lg-4">
							<input type="text" name="quantity"/>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setQuantityAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab4">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Set active for all products' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="is_active_on">
									<input type="radio" checked name="is_active" value="1" id="is_active_on"/>
									<span class="btn btn-default">{l s='Yes' mod='masseditproduct'}</span>
								</label>
								<label for="is_active_off">
									<input type="radio" name="is_active" value="0" id="is_active_off"/>
									<span class="btn btn-default">{l s='No' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setActiveAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab5">
					<div class="row">
						<label class="control-label col-lg-12">
							{l s='Set manufacturer for all products' mod='masseditproduct'}
						</label>
						<div class="col-lg-12">
							<select name="id_manufacturer">
								{foreach from=$manufacturers item=manufacturer}
									<option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'quotes':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setManufacturerAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab6">
					<div class="row">
						<div class="select_products">
							<div class="search_row">
								<label>{l s='Write for search' mod='masseditproduct'}</label>
								<input class="search_product" type="text"/>
							</div>
							<div class="search_row">
								<div class="left_column">
									<label>{l s='Select from list' mod='masseditproduct'}</label>
									<select class="no_selected_product" multiple></select>
									<input class="add_select_product" value="{l s='Add in select products' mod='masseditproduct'}" type="button"/>
								</div>
								<div class="right_column">
									<label>{l s='Selected' mod='masseditproduct'}</label>
									<select name="accessories[]" class="selected_product" multiple></select>
									<input class="remove_select_product" value="{l s='Remove from select products' mod='masseditproduct'}" type="button"/>
								</div>
							</div>
						</div>
						<script>
							$(function () {
								$('.select_products').selectProducts({
									path_ajax: document.location.href.replace(document.location.hash, '')
								});
							});
						</script>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setAccessoriesAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab7">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Select suppliers' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<select multiple name="supplier[]">
								{if is_array($suppliers) && count($suppliers)}
									{foreach from=$suppliers item=supplier}
										<option value="{$supplier.id_supplier|intval}">{$supplier.name|escape:'quotes':'UTF-8'}</option>
									{/foreach}
								{/if}
							</select>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Select supplier default' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<select name="id_supplier_default"></select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setSupplierAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab8">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Apply change for' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="btn-group btn-group-radio">
								<label for="change_for_sp_product">
									<input type="radio" checked name="change_for_sp" value="0" id="change_for_sp_product"/>
									<span class="btn btn-default">{l s='Product' mod='masseditproduct'}</span>
								</label>
								<label for="change_for_sp_combination">
									<input type="radio" name="change_for_sp" value="1" id="change_for_sp_combination"/>
									<span class="btn btn-default">{l s='Combination' mod='masseditproduct'}</span>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='For' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<div class="col-lg-4">
								<select name="sp_id_currency">
									<option value="0">{l s='All currencies' mod='masseditproduct'}</option>
									{if is_array($currencies) && count($currencies)}
										{foreach from=$currencies item=currency}
											<option value="{$currency.id_currency|intval}">{$currency.id_currency|escape:'quotes':'UTF-8'}</option>
										{/foreach}
									{/if}
								</select>
							</div>
							<div class="col-lg-4">
								<select name="sp_id_country">
									<option value="0">{l s='All countries' mod='masseditproduct'}</option>
									{if is_array($countries) && count($countries)}
										{foreach from=$countries item=country}
											<option value="{$country.id_country|intval}">{$country.country|escape:'quotes':'UTF-8'}</option>
										{/foreach}
									{/if}
								</select>
							</div>
							<div class="col-lg-4">
								<select name="sp_id_group">
									<option value="0">{l s='All groups' mod='masseditproduct'}</option>
									{if is_array($groups) && count($groups)}
										{foreach from=$groups item=group}
											<option value="{$group.id_group|intval}">{$group.name|escape:'quotes':'UTF-8'}</option>
										{/foreach}
									{/if}
								</select>
							</div>
							<input name="sp_id_product_attribute" value="0" type="hidden"/>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='From' mod='masseditproduct'}</label>
						<div class="col-lg-3">
							<input name="sp_from" class="datepicker" type="text"/>
						</div>
						<label class="control-label col-lg-12">{l s='To' mod='masseditproduct'}</label>
						<div class="col-lg-3">
							<input name="sp_to" class="datepicker" type="text"/>
						</div>
					</div>
					<script>
						$('.datepicker').datetimepicker({
							prevText: '',
							nextText: '',
							dateFormat: 'yy-mm-dd',
							// Define a custom regional settings in order to use PrestaShop translation tools
							currentText: '{l s='Now' mod='masseditproduct' js=true}',
							closeText: '{l s='Done' mod='masseditproduct' js=true}',
							ampm: false,
							amNames: ['AM', 'A'],
							pmNames: ['PM', 'P'],
							timeFormat: 'hh:mm:ss tt',
							timeSuffix: '',
							timeOnlyTitle: '{l s='Choose Time' mod='masseditproduct' js=true}',
							timeText: '{l s='Time' mod='masseditproduct' js=true}',
							hourText: '{l s='Hour' mod='masseditproduct' js=true}',
							minuteText: '{l s='Minute' mod='masseditproduct' js=true}'
						});
					</script>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Begin from quantity' mod='masseditproduct'}</label>
						<div class="col-lg-3">
							<input name="sp_from_quantity" value="1" type="text"/>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Apply discount' mod='masseditproduct'}</label>
						<div class="col-lg-6">
							<div class="col-lg-3">
								<input name="sp_reduction" value="0" type="text"/>
							</div>
							<div class="col-lg-3">
								<select name="sp_reduction_type">
									<option selected>-</option>
									<option value="amount">{l s='Currency' mod='masseditproduct'}</option>
									<option value="percentage">{l s='Percent' mod='masseditproduct'}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setSpecificPriceAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab9">
                    <div class="row">
                       <div class="col-lg-12">
                            <div class="btn-group btn-group-radio">
                                <label for="type_long">
                                    <input type="radio" name="type" value="type_long" id="type_long"/>
                                    <span class="btn btn-default">{l s='Long description' mod='masseditproduct'}</span>
                                </label>
                                <label for="type_short">
                                    <input type="radio" checked name="type" value="type_short" id="type_short"/>
                                    <span class="btn btn-default">{l s='Short description' mod='masseditproduct'}</span>
                                </label>
                                <label for="type_meta">
                                    <input type="radio" name="type" value="type_meta" id="type_meta"/>
                                    <span class="btn btn-default">{l s='Meta description' mod='masseditproduct'}</span>
                                </label>
                            </div>
                        </div>
                    </div>
					<div class="row">
						<label class="control-label col-lg-12">{l s='Description' mod='masseditproduct'}</label>
						<div class="col-lg-8">
							<div class="row">
								<label>{l s='Set description for all products' mod='masseditproduct'}</label>
								<textarea name="description" type="text" cols="60" rows="8" /></textarea>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="row">
								<p>Type [product_name] to include the name of the product in the description.
                                When you save, this will be replaced by the name of the product.</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setDescriptionAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
				<div id="tab10">
					<div class="row">
						<label class="control-label col-lg-12">{l s='Shipping' mod='masseditproduct'}</label>

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="shipping_width"> Package width</label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">cm</span>
                                <input maxlength="14" id="shipping_width" name="shipping_width" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="shipping_height"> Package height</label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">cm</span>
                                <input maxlength="14" id="shipping_height" name="shipping_height" type="text" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="shipping_depth"> Package depth</label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">cm</span>
                                <input maxlength="14" id="shipping_depth" name="shipping_depth" type="text" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="shipping_weight"> Package weight</label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">kg</span>
                                <input maxlength="14" id="shipping_weight" name="shipping_weight" type="text" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="additional_shipping_cost">
                                <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="If a carrier has a tax, it will be added to the shipping fees.">
                                    Additional shipping fees (for a single item)
                                </span>

                            </label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">$  (tax excl.)</span>
                                <input type="text" id="additional_shipping_cost" name="additional_shipping_cost" >
                            </div>
                        </div>
						<label class="control-label col-lg-12">{l s='Select carriers' mod='masseditproduct'}</label>
						<div class="col-lg-12">
							<select multiple name="carrier[]">
								{if is_array($carriers) && count($carriers)}
									{foreach from=$carriers item=carrier}
										<option value="{$carrier.id_reference|intval}">{$carrier.name|escape:'quotes':'UTF-8'}</option>
									{/foreach}
								{/if}
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button id="setShippingAllProduct" class="btn btn-default">
								<span>{l s='Apply' mod='masseditproduct'}</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel mode_edit">
		<h3 class="panel-heading">{l s='Selected products' mod='masseditproduct'}</h3>
		<div class="row table_selected_products">
			{include file="./products.tpl" without_product=true}
		</div>
	</div>
</div>
</div>
