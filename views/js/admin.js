/**
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
 */

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function PopupForm(popup_selector)
{
    this.popup = $(popup_selector);
    var _this = this;
    this.select_products = {};
    this.products = {};
    this.init = function () {
        _this.popup.find('.toggleList').live('click', function () {
            if ($(this).is('.active'))
            {
                _this.popup.find('.list_products').stop(true, true).slideUp(500);
                $(this).removeClass('active');
            }
            else
            {
                _this.popup.find('.list_products').stop(true, true).slideDown(500);
                $(this).addClass('active');
            }
        });
        _this.popup.find('.clearAll').live('click', function () {
            _this.popup.find('.product_item').each(function () {
                var id_product = $(this).data('id');
                _this.removeProduct(id_product);
            });
        });
        $('.removeProduct').live('click', function (e) {
            e.preventDefault();
            var product = $(this).closest('.product_item');
            var id_product = product.data('id');
            _this.removeProduct(id_product);
        });
        $('[class*=mode_]').stop(true, true).hide();
        $('.' + _this.popup.find('[name=mode]:checked').val()).stop(true, true).show();
        _this.popup.find('[name=mode]').change(function () {
            $('[class*=mode_]').stop(true, true).slideUp(500);
            $('.' + $(this).val()).stop(true, true).slideDown(500);
        });
        _this.updatePopup();
    };
    this.mergeProducts = function () {
        var products = _this.select_products;
        for (var i in products)
        {
            if (typeof _this.products[i] == 'undefined')
            {
                _this.products[i] = products[i];
                var product = products[i];
                _this.popup.find('.list_products')
                    .append('<div class="product_item product_'+product.id+'" data-id="'+product.id+'">'+
                    product.id+' - '+product.name
                    +' <a class="removeProduct" href="#"><i class="icon-remove"></i></a></div>');
                $('.table_search_product .product_' + product.id).find('[name=product]').removeAttr('checked');
                $('.table_search_product .product_' + product.id).removeClass('selected stateSelected').addClass('un-selected stateUnSelected');
                $('.table_selected_products table tbody').append($('.table_search_product .product_' + product.id).remove());
                $('.table_selected_products .selector_container').setSelectorContainer();
                var id_tab = parseInt(tab_container.tab.find('ul li.active').data('tab').replace('tab', ''));
                if (id_tab == 2 || id_tab == 3)
                    $('.table_selected_products [data-combinations]').show();
                else
                    $('[data-combinations]').hide();
            }
        }
        _this.resetSelect();
        _this.updatePopup();
        if (!$('.table_search_product tbody tr').length)
        {
            window.page = 1;
            $('#beginSearch').trigger('click');
        }
    };
    this.selectProduct = function (product) {
        if (typeof _this.select_products[product.id] == 'undefined')
        {
            _this.select_products[product.id] = product;
        }
        _this.updatePopup();
    };
    this.unselectProduct = function (id) {
        if (typeof _this.select_products[id] != 'undefined')
        {
            delete _this.select_products[id];
            _this.updatePopup();
            return true;
        }
        return false;
    };
    this.resetSelect = function ()
    {
        _this.select_products = {};
        _this.updatePopup();
    };
    this.removeProduct = function(id_product)
    {
        $('.table_search_product .no_products').remove();
        if (typeof _this.products[id_product] != 'undefined')
        {
            delete _this.products[id_product];
            _this.popup.find('.list_products .product_' + id_product).remove();
            $('.table_selected_products .product_' + id_product + ' .selector_container').trigger('destroy');
            $('.table_search_product table tbody').append($('.table_selected_products .product_' + id_product).remove());
        }
        _this.updatePopup();
    };
    this.updatePopup = function ()
    {
        if (!Object.size(_this.products))
            _this.popup.find('.toggleList, .clearAll').hide();
        else
            _this.popup.find('.toggleList, .clearAll').show();
        _this.popup.find('.count_selected_products').text(Object.size(_this.select_products));
        _this.popup.find('.count_products').text(Object.size(_this.products));
    };
}


function TabContainer(tab_container_selector)
{
    var _this = this;
    this.tab = $(tab_container_selector);
    this.init = function () {
        _this.tab.find('ul.tabs > li').live('click', function () {
            _this.tab.find('.tabs > li').removeClass('active');
            $(this).addClass('active');
            _this.tab.find('.tabs_content > div').hide();
            var id_tab = parseInt($(this).data('tab').replace('tab', ''));
            if (id_tab == 2 || id_tab == 3 || id_tab == 8)
                $('.table_selected_products [data-combinations]').show();
            else
                $('[data-combinations]').hide();
            _this.tab.find('[id="'+$(this).data('tab')+'"]').show();
        });
        _this.tab.find('.tabs_content > div').hide();
        _this.tab.find('.tabs_content > div:first').show();
        _this.tab.find('ul.tabs > li:first').addClass('active');
    }
}

$(function () {
    window.page = 1;
    window.tree = new TreeCustom('.tree_custom .tree_categories', '.tree_custom .tree_categories_header');
    window.tree.init();
    window.tree_categories = new TreeCustom('.tree_custom_categories .tree_categories', '.tree_custom_categories .tree_categories_header');
    window.tree_categories.init();
    window.tree_categories.checkAssociatedCategory(2);
    $('#beginSearch').live('click', function () {
        $('.wrapp_content').addClass('loading');
        var categories = tree.getListSelectedCategories();
        var search_query = $('[name=search_query]').val();
        var type_search = $('[name=type_search]').val();
        var manufacturers = $('[name="manufacturer[]"]').val();
        var how_many_show = $('[name="how_many_show"]').val();
        var active = parseInt($('[name="active"]:checked').val());
        var disable = parseInt($('[name="disable"]:checked').val());
        var exclude_ids = [];
        $('.table_selected_products [name="id_product"]').each(function () {
            exclude_ids.push($(this).val());
        });
        var url = document.location.href.replace(document.location.hash, '');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                categories: categories,
                search_query: search_query,
                type_search: type_search,
                manufacturers: manufacturers,
                how_many_show: how_many_show,
                active: active,
                disable: disable,
                page: window.page,
                ajax: true,
                action: 'search_products',
                exclude_ids: exclude_ids
            },
            success: function (r) {
                $('.wrapp_content').removeClass('loading');
                $('.table_search_product').html(r.products);
                popup_form.resetSelect();
                var id_tab = parseInt(tab_container.tab.find('ul li.active').data('tab').replace('tab', ''));
                if (id_tab == 2 || id_tab == 3)
                    $('.table_selected_products [data-combinations]').show();
                else
                    $('[data-combinations]').hide();
                //var table = $('.table_search_product table').finderSelect({
                //    children: '> tr:not(.table_head)'
                //});
                //table.finderSelect('addHook','highlight:before', function(el) {
                //    el.find('input[name=product]').attr('checked', 'checked');
                //    el.each(function () {
                //        popup_form.selectProduct({
                //            id: $(this).find('[name=id_product]').val(),
                //            name: $(this).find('[data-name]').text()
                //        });
                //    });
                //});
                //table.finderSelect('addHook','unHighlight:before', function(el) {
                //    el.find('input[name=product]').removeAttr('checked');
                //    el.find('input[name=id_product]').each(function () {
                //        popup_form.unselectProduct($(this).val());
                //    });
                //});
                document.location.hash = r.hash;
            },
            error: function () {
                $('.wrapp_content').removeClass('loading');
            }
        });
    });
    $('#setCategoryAllProduct').live('click', function () {
        var tab = $('#tab1');
        var data = {};
        var categories = tree_categories.getListSelectedCategories();
        data['category'] = categories[0].id;
        setAllProducts(data, 'category');
    });
    $('#setDefaultCategoryAllProduct').live('click', function () {
        var tab = $('#tab1');
        var data = {};
        var categories = tree_categories.getListSelectedCategories();
        data['category'] = categories[0].id;
        setAllProducts(data, 'defaultcategory');
    });
    $('#removeCategoryAllProduct').live('click', function () {
        var tab = $('#tab1');
        var data = {};
        var categories = tree_categories.getListSelectedCategories();
        data['category'] = categories[0].id;
        setAllProducts(data, 'removecategory');
    });
    $('#setPriceAllProduct').live('click', function () {
        var tab = $('#tab2');
        var data = {};
        data['type_price'] = tab.find('[name="type_price"]:checked').val();
        data['action_price'] = tab.find('[name="action_price"]:checked').val();
        data['change_for'] = tab.find('[name="change_for"]:checked').val();
        data['price_id_group'] = tab.find('[name="price_id_group"]').val();
        data['combinations'] = $.getAllValues();
        data['price_value'] = tab.find('[name="price_value"]').val();
        setAllProducts(data, 'price');
    });
    $('#setQuantityAllProduct').live('click', function () {
        var tab = $('#tab3');
        var data = {};
        data['quantity'] = parseInt(tab.find('[name="quantity"]').val());
        data['change_for'] = tab.find('[name="change_for_qty"]:checked').val();
        data['combinations'] = $.getAllValues();
        data['action_quantity'] = parseInt(tab.find('[name="action_quantity"]:checked').val());
        setAllProducts(data, 'quantity');
    });
    $('#setActiveAllProduct').live('click', function () {
        var tab = $('#tab4');
        var data = {};
        data['active'] = parseInt(tab.find('[name="is_active"]:checked').val());
        setAllProducts(data, 'active');
    });
    $('#setManufacturerAllProduct').live('click', function () {
        var tab = $('#tab5');
        var data = {};
        data['id_manufacturer'] = parseInt(tab.find('[name="id_manufacturer"]').val());
        setAllProducts(data, 'manufacturer');
    });
    $('#setAccessoriesAllProduct').live('click', function () {
        var tab = $('#tab6');
        var data = {};
        data['accessories'] = [];
        tab.find('[name="accessories[]"] option').each(function () {
            data['accessories'].push({
                id: parseInt($(this).attr('value'))
            });
        });
        setAllProducts(data, 'accessories');
    });
    $('#setSupplierAllProduct').live('click', function () {
        var tab = $('#tab7');
        var data = {};
        data['supplier'] = [];
        tab.find('[name="supplier[]"] option:selected').each(function () {
            data['supplier'].push(parseInt($(this).attr('value')));
        });
        data['id_supplier_default'] = tab.find('[name="id_supplier_default"]').val();
        setAllProducts(data, 'supplier');
    });
    $('#setSpecificPriceAllProduct').live('click', function () {
        var tab = $('#tab8');
        var data = {};
        data['sp_from_quantity'] = tab.find('[name="sp_from_quantity"]').val();
        data['sp_id_currency'] = tab.find('[name="sp_id_currency"]').val();
        data['sp_id_country'] = tab.find('[name="sp_id_country"]').val();
        data['sp_id_group'] = tab.find('[name="sp_id_group"]').val();
        data['sp_from'] = tab.find('[name="sp_from"]').val();
        data['sp_to'] = tab.find('[name="sp_to"]').val();
        data['sp_reduction'] = tab.find('[name="sp_reduction"]').val();
        data['sp_reduction_type'] = tab.find('[name="sp_reduction_type"]').val();
        data['change_for'] = tab.find('[name="change_for_sp"]:checked').val();
        data['combinations'] = $.getAllValues();
        setAllProducts(data, 'discount');
    });
    $('#setDescriptionAllProduct').live('click', function () {
        var tab = $('#tab9');
        var data = {};
        data['description'] = tab.find('[name="description"]').val();
        data['type'] = tab.find('[name="type"]:checked').val();
        setAllProducts(data, 'description');
    });
    $('#setShippingAllProduct').live('click', function () {
        var tab = $('#tab10');
        var data = {};
        data['carrier'] = [];
        data['shipping_height'] = tab.find('[name="shipping_height"]').val();
        data['shipping_width'] = tab.find('[name="shipping_width"]').val();
        data['shipping_depth'] = tab.find('[name="shipping_depth"]').val();
        data['shipping_weight'] = tab.find('[name="shipping_weight"]').val();
        data['additional_shipping_cost'] = tab.find('[name="additional_shipping_cost"]').val();

        tab.find('[name="carrier[]"] option:selected').each(function () {
            data['carrier'].push(parseInt($(this).attr('value')));
        });
        setAllProducts(data, 'shipping');
    });
    $('.product_checkbox').live('click', function () {
        var tr = $(this).closest('tr');
        if ($(this).is(':checked'))
        {
            tr.addClass('selected');
            popup_form.selectProduct({
                            id: tr.find('[name=id_product]').val(),
                            name: tr.find('[data-name]').text()
                        });
            popup_form.mergeProducts();
        }
        else
        {
            tr.removeClass('selected');
            popup_form.unselectProduct(tr.find('[name=id_product]').val());
        }
    });
    $('[name="supplier[]"]').live('change', function () {
        $('[name="id_supplier_default"]').html('');
        $(this).find('option:selected').each(function () {
            $('[name="id_supplier_default"]').append($(this).clone());
        });
    });
    checkURL();
    window.popup_form = new PopupForm('.popup_mep');
    window.popup_form.init();
    window.tab_container = new TabContainer('.tab_container');
    window.tab_container.init();

    $('.selectAll').live('click', function () {
        $('.table_search_product .product_checkbox').each(function () {
            var tr = $(this).closest('tr');
            tr.addClass('selected');
            popup_form.selectProduct({
                id: tr.find('[name=id_product]').val(),
                name: tr.find('[data-name]').text()
            });
        });
        popup_form.mergeProducts();
    });
});

function setAllProducts(data, field)
{
    $('.message_successfully').hide();
    $('.message_error').hide();
    var table = $('.table_selected_products tbody');
    var url = document.location.href.replace(document.location.hash, '');
    data['products'] = popup_form.products;
    data['ajax'] = true;
    data['action'] = 'set_'+field+'_all_product';
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (r)
        {
            if (!r.hasError)
            {
                $('.message_successfully').slideDown(500);
                for (var i in r.products)
                {
                    if (field == 'active')
                        table.find('.product_' + i + ' [data-'+field+'] img').attr('src', (r.products[i] ? '../img/admin/enabled.gif' : '../img/admin/disabled.gif'));
                    else if (field == 'price')
                    {
                        table.find('.product_' + i + ' [data-'+field+']').text(r.products[i].price);
                        table.find('.product_' + i + ' [data-'+field+'_final]').text(r.products[i].price_final);
                    }
                    else if (field == 'accessories' || field == 'discount' || field == 'description' || field == 'shipping')
                    {

                    }
                    else
                        table.find('.product_' + i + ' [data-'+field+']').text(r.products[i]);
                }

                if (field == 'price')
                {
                    for (var i in r.combinations)
                    {
                        $('[data-pa-price="'+i+'"]').text(r.combinations[i].price);
                        $('[data-pa-total-price="'+i+'"]').text(r.combinations[i].total_price);
                        $('[data-pa-price-final="'+i+'"]').text(r.combinations[i].price_final);
                        $('[data-pa-total-price-final="'+i+'"]').text(r.combinations[i].total_price_final);
                    }
                }
                if (field == 'quantity')
                {
                    for (var i in r.combinations)
                    {
                        $('[data-pa-quantity="'+i+'"]').text(parseInt(r.combinations[i]));
                    }
                }
            }
            else
                $('.message_error').html(r.error).slideDown(500);
        },
        error: function () {
            $('.message_error').html('Has error').slideDown(500);
        }
    });
}

function checkURL()
{
    var hash = document.location.hash;
    var data = hash.replace('#', '').split('&');
    for (var i = 0; i < data.length; i++)
        data[i] = decodeURIComponent(data[i]);
    for (var i in data)
    {
        var param = data[i].split('-');
        if (param[0] == 'categories')
        {
            $.each(param[1].split('_'), function (index, value)
            {
                window.tree.checkAssociatedCategory(value);
            });
        }
        else if(param[0] == 'categories')
        {
            var manufacturers = $('[name="manufacturers[]"]');
            $.each(param[1].split('_'), function (index, value)
            {
                manufacturers.find('option[value='+value+']').attr('selected', 'selected');
            });
        }
        else if(param[0] == 'search_query')
        {
            $('[name=search_query]').val(param[1]);
        }
        else if(param[0] == 'type_search')
        {
            var type_search = $('[name=type_search]');
            type_search.find('option').removeAttr('selected');
            type_search.find('option[value='+param[1]+']').attr('selected', 'selected');
        }
        else if(param[0] == 'how_many_show')
        {
            var how_many_show = $('[name=how_many_show]');
            how_many_show.find('option').removeAttr('selected');
            how_many_show.find('option[value='+param[1]+']').attr('selected', 'selected');
        }
        else if(param[0] == 'active')
        {
            var active = $('[name=active]');
            active.removeAttr('checked');
            $('[name=active][value='+param[1]+']').attr('checked', 'checked');
        }
        else if(param[0] == 'disable')
        {
            var disable = $('[name=disable]');
            disable.removeAttr('checked');
            $('[name=disable][value='+param[1]+']').attr('checked', 'checked');
        }
        else if(param[0] == 'page')
        {
            window.page = param[1];
        }
    }
    if (data.length)
        $('#beginSearch').trigger('click');
}

function setPage(page)
{
    window.page = page;
    page = 'page-' + page;
    var hash = document.location.hash.replace('#', '');
    if (!hash.length)
        document.location.hash = page;
    else
    {
        var old_page = /page-[0-9]+/.exec(hash);
        if (old_page)
        {
            hash.replace(old_page[0], page);
        }
        else
        {
            hash += '&' + page;
        }
        document.location.hash = page;
    }
    checkURL();
}

