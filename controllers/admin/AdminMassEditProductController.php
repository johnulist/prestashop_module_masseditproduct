<?php
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

class AdminMassEditProductController extends ModuleAdminController
{
	public $sql_shop = true;
	public $ids_shop = array();
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'configuration';
		$this->identifier = 'id_configuration';
		$this->className = 'Configuration';
		$this->bootstrap = true;
		$this->display = 'edit';
		if (!array_key_exists('disPrice', $this->context->smarty->registered_plugins['function']))
			$this->context->smarty->registerPlugin('function', 'disPrice', array($this, 'displayPrice'));
		parent::__construct();
		if (Context::getContext()->shop->getContext() == ShopCore::CONTEXT_SHOP)
		{
			$this->sql_shop = ' = '.(int)Context::getContext()->shop->id;
			$this->ids_shop = array(Context::getContext()->shop->id);
		}
		elseif (Context::getContext()->shop->getContext() == ShopCore::CONTEXT_GROUP)
		{
			$shops = ShopGroupCore::getShopsFromGroup(Context::getContext()->shop->id_shop_group);
			$ids_shop = array();
			foreach ($shops as $shop)
				$ids_shop[] = $shop['id_shop'];
			$this->sql_shop = ' IN('.(count($ids_shop) ? implode(',', $ids_shop) : 'NULL').')';
			$this->ids_shop = $ids_shop;
		}
		elseif (Context::getContext()->shop->getContext() == ShopCore::CONTEXT_ALL)
		{
			$this->sql_shop = false;
			$all_shops = ShopCore::getShops(true);
			$ids_shop = array();
			foreach ($all_shops as $shop)
				$ids_shop[] = $shop['id_shop'];
			$this->ids_shop = $ids_shop;
		}
	}

	public function displayPrice($params)
	{
		return Tools::displayPrice($params['price'], $params['currency']);
	}

	public function renderForm()
	{
		$this->module->autoloadCSS();

		if (_PS_VERSION_ < 1.6)
		{
			$this->context->controller->addJqueryUI('ui.slider');
			$this->context->controller->addJqueryUI('ui.datepicker');
			$this->context->controller->addCSS($this->module->getPathUri().'views/css/jquery-ui-timepicker-addon.css');
			$this->context->controller->addJS($this->module->getPathUri().'views/js/jquery-ui-timepicker-addon.js');
		}
		else
			$this->context->controller->addJqueryPlugin('timepicker');

		$this->context->controller->addJS($this->module->getPathUri().'views/js/tree_custom.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/jquery.finderSelect.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/search_product.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/selector_container.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/admin.js');
		$tpl_vars = array(
			'categories' => Category::getCategories($this->context->language->id),
			'simple_categories' => Category::getSimpleCategories($this->context->language->id),
			'manufacturers' => Manufacturer::getManufacturers(false, 0, false),
			'suppliers' => Supplier::getSuppliers(false, 0, false),
			'currencies' => Currency::getCurrencies(false, true),
			'carriers' => Carrier::getCarriers($this->context->language->id),
			'countries' => Country::getCountries($this->context->language->id, true),
			'groups' => Group::getGroups($this->context->language->id)
		);
		$this->tpl_form_vars = array_merge($this->tpl_form_vars, $tpl_vars);
		$this->fields_form = array(
			'legend' => array(
				'title' => 'tree_custom.tpl'
			)
		);
		return parent::renderForm();
	}
	public function setQuantity($id_product, $id_product_attribute, $quantity, $action_quantity, $id_shop = null)
	{
		if (!Validate::isUnsignedId($id_product))
			return false;

		$context = Context::getContext();

		// if there is no $id_shop, gets the context one
		if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP)
			$id_shop = (int)$context->shop->id;

		$depends_on_stock = StockAvailable::dependsOnStock($id_product);

		//Try to set available quantity if product does not depend on physical stock
		if (!$depends_on_stock)
		{
			$id_stock_available = (int)StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
			if ($id_stock_available)
			{
				$stock_available = new StockAvailable($id_stock_available);
				if ($action_quantity === self::ACTION_QUANTITY_INCREASE)
					$quantity = $stock_available->quantity + (int)$quantity;
				elseif ($action_quantity === self::ACTION_QUANTITY_REDUCE)
					$quantity = $stock_available->quantity - (int)$quantity;

				$stock_available->quantity = (int)$quantity;

				$stock_available->update();
			}
			else
			{
				$out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
				$stock_available = new StockAvailable();
				$stock_available->out_of_stock = (int)$out_of_stock;
				$stock_available->id_product = (int)$id_product;
				$stock_available->id_product_attribute = (int)$id_product_attribute;

				if ($action_quantity === self::ACTION_QUANTITY_INCREASE)
					$quantity = $stock_available->quantity + (int)$quantity;
				elseif ($action_quantity === self::ACTION_QUANTITY_REDUCE)
					$quantity = $stock_available->quantity - (int)$quantity;

				$stock_available->quantity = (int)$quantity;

				if ($id_shop === null)
					$shop_group = Shop::getContextShopGroup();
				else
					$shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));

				// if quantities are shared between shops of the group
				if ($shop_group->share_stock)
				{
					$stock_available->id_shop = 0;
					$stock_available->id_shop_group = (int)$shop_group->id;
				}
				else
				{
					$stock_available->id_shop = (int)$id_shop;
					$stock_available->id_shop_group = 0;
				}
				$stock_available->add();
			}
			Hook::exec('actionUpdateQuantity',
				array(
					'id_product' => $id_product,
					'id_product_attribute' => $id_product_attribute,
					'quantity' => $stock_available->quantity
				)
			);
		}
		Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$id_product.'*');
		return $quantity;
	}

	public function updatePriceProduct($id_product, $price)
	{
		if (!Shop::isFeatureActive())
			Db::getInstance()->update('product', array(
				'price' => ($price < 0 ? 0 : (float)$price)
			), ' id_product = '.(int)$id_product);
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_shop` ps
		LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
		SET ps.`price` = '.($price < 0 ? 0 : (float)$price).'
		WHERE ps.`id_product` = '.(int)$id_product.'
		'.(Shop::isFeatureActive() && $this->sql_shop ? ' AND ps.`id_shop` '.$this->sql_shop : ''));
	}

	public function updatePriceCombination($id_product_attribute, $price)
	{
		if (!Shop::isFeatureActive())
			Db::getInstance()->update('product_attribute', array(
				'price' => ($price < 0 ? 0 : (float)$price)
			), ' id_product_attribute = '.(int)$id_product_attribute);
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_attribute_shop` pas
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pas.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = pa.`id_product`
		SET pas.`price` = '.($price < 0 ? 0 : (float)$price).'
		WHERE pas.`id_product_attribute` = '.(int)$id_product_attribute
			.(Shop::isFeatureActive() && $this->sql_shop ? ' AND pas.`id_shop` '.$this->sql_shop : ''));
	}
	public function actionPrice($price, $action_price, $price_value)
	{
		switch ($action_price)
		{
			case self::ACTION_PRICE_INCREASE_PERCENT:
				$price += ($price * ($price_value / 100));
				break;
			case self::ACTION_PRICE_INCREASE:
				$price += $price_value;
				break;
			case self::ACTION_PRICE_REDUCE_PERCENT:
				$price -= ($price * ($price_value / 100));
				break;
			case self::ACTION_PRICE_REDUCE:
				$price -= $price_value;
				break;
			case self::ACTION_PRICE_REWRITE:
				$price = $price_value;
				break;
		}
		return $price;
	}

	public function getCombinationsByIds($ids_combinations, $id_shop)
	{
		if (!is_array($ids_combinations) || (is_array($ids_combinations) && !count($ids_combinations)))
			return array();
		$combinations = Db::getInstance()->executeS('SELECT
		pa.`id_product`,
		pa.`id_product_attribute`,
		sa.`quantity`,
		pss.`price` as `product_price`,
		pas.`price`,
		(pas.`price` + pss.`price`) as total_price
		FROM '._DB_PREFIX_.'product_attribute pa
		LEFT JOIN '._DB_PREFIX_.'product p ON p.`id_product` = pa.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'product_shop` pss ON (pa.`id_product` = pss.`id_product` AND pss.id_shop = '.pSQL($id_shop).')
		LEFT JOIN '._DB_PREFIX_.'tax_rules_group trg ON trg.`id_tax_rules_group` = pss.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'tax t ON t.`id_tax` = pss.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'product_attribute_shop pas ON pas.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN '._DB_PREFIX_.'stock_available sa ON sa.`id_product_attribute` = pa.`id_product_attribute` AND sa.`id_shop` = '.pSQL($id_shop).'
		WHERE pa.`id_product_attribute` IN ('.pSQL(implode(',', $ids_combinations)).') AND pas.`id_shop` = '.pSQL($id_shop).'
		GROUP BY pa.`id_product_attribute`');
		$country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
		$address = new Address();
		$address->id_country = $country->id;
		foreach ($combinations as &$combination)
		{
			if ((int)Configuration::get('PS_TAX'))
			{
				$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$combination['id_product'], $this->context));
				$product_tax_calculator = $tax_manager->getTaxCalculator();
				$combination['product_price_final'] = $product_tax_calculator->addTaxes($combination['product_price']);
				$combination['price_final'] = $product_tax_calculator->addTaxes($combination['price']);
				$combination['total_price_final'] = $product_tax_calculator->addTaxes($combination['price'] + $combination['product_price']);
				$combination['rate'] = $tax_manager->getTaxCalculator()->getTotalRate();
			}
			else
			{
				$combination['product_price_final'] = $combination['product_price'];
				$combination['price_final'] = $combination['price'];
				$combination['total_price_final'] = $combination['price'] + $combination['product_price'];
				$combination['rate'] = 0;
			}
		}
		return $combinations;
	}

	const SEARCH_TYPE_NAME = 0;
	const SEARCH_TYPE_ID = 1;
	const SEARCH_TYPE_REFERENCE = 2;
	const SEARCH_TYPE_EAN13 = 3;
	const SEARCH_TYPE_UPC = 4;
	protected static $search_type_fields = array(
		self::SEARCH_TYPE_NAME => 'pl.`name`',
		self::SEARCH_TYPE_ID => 'p.`id_product`',
		self::SEARCH_TYPE_REFERENCE => 'p.`reference`',
		self::SEARCH_TYPE_EAN13 => 'p.`ean13`',
		self::SEARCH_TYPE_UPC => 'p.`upc`',
	);

	public function ajaxProcessSearchProducts()
	{
		$categories = Tools::getValue('categories');
		$search_query = Tools::getValue('search_query');
		$type_search = (int)Tools::getValue('type_search', 0);
		$manufacturers = Tools::getValue('manufacturers');
		$how_many_show = (int)Tools::getValue('how_many_show', 20);
		$active = (int)Tools::getValue('active', 0);
		$disable = (int)Tools::getValue('disable', 0);
		$page = (int)Tools::getValue('page', 1);
		$exclude_ids = Tools::getValue('exclude_ids', array());
		$this->intValueRequestVar($exclude_ids);
		$hash = array();

		$sql_category = false;
		if (is_array($categories) && count($categories))
		{
			$ids_categories = array();
			foreach ($categories as $category)
				$ids_categories[] = (int)$category['id'];
			$sql_category = implode(',', $ids_categories);
			$hash[] = 'categories-'.implode('_', $ids_categories);
		}

		$sql_manufactures = false;
		if (is_array($manufacturers) && count($manufacturers))
		{
			$this->intValueRequestVar($manufacturers);
			$sql_manufactures = implode(',', $manufacturers);
			$hash[] = 'manufacturers-'.implode('_', $manufacturers);
		}
		$sql_search_query = false;
		if ($search_query)
		{
			switch ($type_search)
			{
				case self::SEARCH_TYPE_ID:
					$ids = explode(' ', $search_query);
					$this->intValueRequestVar($ids);
					$sql_search_query = '('.implode(',', $ids).')';
					$sql_type_search = 'p.`id_product` IN';
					$hash[] = 'type_search-1';
					break;
				case self::SEARCH_TYPE_NAME:
				case self::SEARCH_TYPE_REFERENCE:
				case self::SEARCH_TYPE_EAN13:
				case self::SEARCH_TYPE_UPC:
					$sql_search_query = '"'.pSQL($search_query).'"';
					$sql_type_search = self::$search_type_fields[$type_search].' LIKE ';
					$hash[] = 'type_search-'.$type_search;
					break;
				default:
					throw new LogicException('Unknown search type');
			}
			$hash[] = 'search_query-'.urlencode($search_query);
		}

		if ($active)
			$hash[] = 'active-1';
		if ($disable)
			$hash[] = 'disable-1';

		if ($page > 1)
			$hash[] = 'page-'.$page;

		if ($how_many_show > 20)
			$hash[] = 'how_many_show-'.$how_many_show;
		$id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'p.id_shop_default';
		$nb_products = Db::getInstance()->getValue('SELECT COUNT(DISTINCT p.`id_product`)
		FROM '._DB_PREFIX_.'product p
		JOIN `'._DB_PREFIX_.'product_shop` pss ON (p.`id_product` = pss.`id_product` AND pss.id_shop = '.pSQL($id_shop).')
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.'
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON cp.`id_product` = p.`id_product`
		AND pl.`id_shop` = '.pSQL($id_shop).'
		WHERE 1
		'.($sql_search_query ? 'AND '.$sql_type_search.' '.$sql_search_query.' ' : '').'
		'.($sql_category ? 'AND cp.`id_category` IN('.pSQL($sql_category).')' : '').'
		'.($sql_manufactures ? 'AND p.`id_manufacturer` IN('.pSQL($sql_manufactures).')' : '').'
		'.($active && !$disable ? ' AND pss.`active` = 1 ' : '').'
		'.($disable && !$active ? ' AND pss.`active` = 0 ' : '').'
		'.(is_array($exclude_ids) && count($exclude_ids) ? ' AND pss.`id_product` NOT IN('.pSQL(implode(',', $exclude_ids)).')' : ''));
		$result = Db::getInstance()->executeS('SELECT p.`id_product`, pss.`active`,
			pss.`price`,
			pl.`name`, pl.`link_rewrite`,
			sa.`quantity`,
			cl.`name` as category,
			m.`name` as manufacturer,
			s.`name` as supplier,
			(SELECT i.`id_image` FROM '._DB_PREFIX_.'image i WHERE i.`id_product` = p.`id_product` ORDER BY i.`cover` ASC LIMIT 0,1) cover
		FROM '._DB_PREFIX_.'product p
		JOIN `'._DB_PREFIX_.'product_shop` pss ON (p.`id_product` = pss.`id_product` AND pss.id_shop = '.pSQL($id_shop).')
		LEFT JOIN '._DB_PREFIX_.'tax_rules_group trg ON trg.`id_tax_rules_group` = p.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON m.`id_manufacturer` = p.`id_manufacturer`
		LEFT JOIN '._DB_PREFIX_.'supplier s ON s.`id_supplier` = p.`id_supplier`
		LEFT JOIN '._DB_PREFIX_.'tax t ON t.`id_tax` = p.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product`
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON cp.`id_product` = p.`id_product`
		AND pl.`id_lang` = '.(int)$this->context->language->id.' AND pl.`id_shop` = '.pSQL($id_shop).'
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON cl.`id_category` = pss.`id_category_default` AND cl.`id_lang` = '.(int)$this->context->language->id.'
		LEFT JOIN '._DB_PREFIX_.'stock_available sa ON sa.`id_product` = p.`id_product`
		AND sa.`id_product_attribute` = 0 AND sa.`id_shop` = '.pSQL($id_shop).'
		WHERE 1
		'.($sql_search_query ? 'AND '.$sql_type_search.' '.$sql_search_query.' ' : '').'
		'.($sql_category ? 'AND cp.`id_category` IN('.pSQL($sql_category).')' : '').'
		'.($sql_manufactures ? 'AND p.`id_manufacturer` IN('.pSQL($sql_manufactures).')' : '').'
		'.($active && !$disable ? ' AND pss.`active` = 1 ' : '').'
		'.($disable && !$active ? ' AND pss.`active` = 0 ' : '').'
		'.(is_array($exclude_ids) && count($exclude_ids) ? ' AND pss.`id_product` NOT IN('.pSQL(implode(',', $exclude_ids)).')' : '').'
		GROUP BY p.`id_product` LIMIT '.(((int)$page - 1) * (int)$how_many_show).','.(int)$how_many_show);
		$pages_nb = ceil($nb_products / $how_many_show);
		$range = 5;
		$start = ($page - $range);
		if ($start < 1)
			$start = 1;
		$stop = ($page + $range);
		if ($stop > $pages_nb)
			$stop = (int)$pages_nb;
		$country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
		$address = new Address();
		$address->id_country = $country->id;
		foreach ($result as &$product)
		{
			$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
			$product_tax_calculator = $tax_manager->getTaxCalculator();
			if ((int)Configuration::get('PS_TAX'))
				$product['price_final'] = $product_tax_calculator->addTaxes($product['price']);
			else
				$product['price_final'] = $product['price'];
			$product['image'] = ImageManager::thumbnail(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($product['cover']).$product['cover'].'.jpg',
			'product_mini_'.$product['id_product'].'_'.$product['cover'].'.jpg', 45);
		}
		$products = array();
		foreach ($result as $prod)
			$products[$prod['id_product']] = $prod;
		$ids_product = (is_array($products) && count($products) ? array_keys($products) : array('NULL'));
		$attributes = Db::getInstance()->executeS('SELECT
		pa.`id_product`,
		pa.`id_product_attribute`,
		sa.`quantity`,
		pas.`price`,
		pss.`price` as product_price,
		(pas.`price` + pss.`price`) as total_price,
		agl.`name` as group_name,
		al.`name`
		FROM '._DB_PREFIX_.'product_attribute pa
		LEFT JOIN '._DB_PREFIX_.'product p ON p.`id_product` = pa.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'product_shop` pss ON (pa.`id_product` = pss.`id_product` AND pss.id_shop = '.pSQL($id_shop).')
		LEFT JOIN '._DB_PREFIX_.'tax_rules_group trg ON trg.`id_tax_rules_group` = pss.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'tax t ON t.`id_tax` = pss.`id_tax_rules_group`
		LEFT JOIN '._DB_PREFIX_.'product_attribute_shop pas ON pas.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN '._DB_PREFIX_.'stock_available sa ON sa.`id_product_attribute` = pa.`id_product_attribute` AND sa.`id_shop` = '.pSQL($id_shop).'
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN '._DB_PREFIX_.'attribute a ON a.`id_attribute` = pac.`id_attribute`
		LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '.(int)$this->context->language->id.'
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON agl.`id_attribute_group` = a.`id_attribute_group`
		AND agl.`id_lang` = '.(int)$this->context->language->id.'
		WHERE pa.`id_product` IN ('.pSQL(implode(',', $ids_product)).') AND pas.`id_shop` = '.pSQL($id_shop));
		$country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
		$address = new Address();
		$address->id_country = $country->id;
		foreach ($attributes as $attribute)
		{
			$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
			$product_tax_calculator = $tax_manager->getTaxCalculator();
			if (array_key_exists($attribute['id_product'], $products)
				&& !array_key_exists('combinations', $products[$attribute['id_product']]))
				$products[$attribute['id_product']]['combinations'] = array();
			if (!array_key_exists($attribute['id_product_attribute'], $products[$attribute['id_product']]['combinations']))
			{
				// Fixme: $product['product_price'] is undefined key!!!
				if (!array_key_exists('product_price', $product))
					$product['product_price'] = 0;

				$products[$attribute['id_product']]['combinations'][$attribute['id_product_attribute']] = array(
					'id_product' => $attribute['id_product'],
					'price' => $attribute['price'],
					'price_final' => ((int)Configuration::get('PS_TAX') ? $product_tax_calculator->addTaxes($product['price']) : $product['price']),
					'total_price' => $attribute['total_price'],
					'total_price_final' =>
						((int)Configuration::get('PS_TAX') ?
							$product_tax_calculator->addTaxes($product['price'] + $product['product_price']) :
							$product['price'] + $product['product_price']),
					'quantity' => $attribute['quantity'],
					'attributes' => $attribute['group_name'].': '.$attribute['name']
				);
			}

			else
				$products[$attribute['id_product']]['combinations'][$attribute['id_product_attribute']]['attributes']
					.= ', '.$attribute['group_name'].': '.$attribute['name'];
		}

		$currency = Currency::getCurrency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency['decimals'] = 1;

		$this->context->smarty->assign(array(
			'currency' => $currency,
			'products' => $products,
			'link' => $this->context->link,
			'nb_products' => $nb_products,
			'products_per_page' => $pages_nb,
			'pages_nb' => $pages_nb,
			'p' => $page,
			'n' => $pages_nb,
			'range' => $range,
			'start' => $start,
			'stop' => $stop
		));
		die(Tools::jsonEncode(array(
			'products' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'masseditproduct/views/templates/admin/mass_edit_product/helpers/form/products.tpl'),
			'hash' => implode('&', $hash)
		)));
	}
	public function ajaxProcessGetProducts()
	{
		$query = Tools::getValue('query');
		$select_products = Tools::getValue('select_products');
		if (!is_array($select_products) || !count($select_products))
			$select_products = array();
		$this->intValueRequestVar($select_products);
		$result = Db::getInstance()->executeS('SELECT pl.`id_product`, pl.`name` FROM '._DB_PREFIX_.'product_shop p
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.
			' WHERE pl.`name` LIKE "%'.pSQL($query).'%" AND p.`id_shop` = '.(int)$this->context->shop->id.
			(count($select_products) ?
				' AND p.id_product NOT IN('.pSQL(implode(',', $select_products)).') '
				: ''));
		if (!$result)
			$result = array();
		die(Tools::jsonEncode($result));
	}

	public function ajaxProcessSetCategoryAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$category = (int)Tools::getValue('category');

		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');

		$obj_category = new Category($category, $this->context->language->id);
		if (!Validate::isLoadedObject($obj_category))
			$error[] = $this->module->l('Category not exists');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		$ids_product = $this->getProductsForRequest();
		Db::getInstance()->update(
			'product',
			array(
				'id_category_default' => (int)$category
			),
			' id_product IN('.pSQL(implode(',', $ids_product)).')'
		);
		//UPDATE `ps_product` SET `id_category_default` = '2' WHERE  id_product IN( 1) AND (TRUNCATE TABLE `ps_orders`)

		Db::getInstance()->update('product_shop', array(
			'id_category_default' => (int)$category
		), ' id_product IN('.pSQL(implode(',', $ids_product)).')'
			.(Shop::isFeatureActive() && $this->sql_shop ? ' AND id_shop '.pSQL($this->sql_shop) : ''));

		$category_product_data = array();
		foreach ($ids_product as $id_product)
		{
			$category_product_data[] = array(
				'id_product' => (int)$id_product,
				'id_category' => (int)$category,
			);
		}
		Db::getInstance()->insert('category_product', $category_product_data, false, true, Db::INSERT_IGNORE);

		$return_products = array();
		foreach ($products as $product)
			$return_products[$product['id']] = $obj_category->name;

		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}

	const TYPE_PRICE_BASE = 0;
	const TYPE_PRICE_FINAL = 1;

	const ACTION_PRICE_INCREASE_PERCENT = 1;
	const ACTION_PRICE_INCREASE = 2;
	const ACTION_PRICE_REDUCE_PERCENT = 3;
	const ACTION_PRICE_REDUCE = 4;
	const ACTION_PRICE_REWRITE = 5;

	const CHANGE_FOR_PRODUCT = 0;
	const CHANGE_FOR_COMBINATION = 1;

	public function ajaxProcessSetPriceAllProduct()
	{
		$error = array();
		$currency = Currency::getCurrency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency['decimals'] = 1;
		$ids_product = $this->getProductsForRequest();
		$type_price = (int)Tools::getValue('type_price');
		$action_price = (int)Tools::getValue('action_price');
		$price_value = (float)Tools::getValue('price_value');
		$change_for = (int)Tools::getValue('change_for');
		$combinations = Tools::getValue('combinations');
		if (!(int)$price_value)
			$error[] = $this->module->l('Write value');
		if (!count($ids_product))
			$error[] = $this->module->l('No products');
		if ($change_for === self::CHANGE_FOR_COMBINATION && (!is_array($combinations) || (is_array($combinations) && !count($combinations))))
			$error[] = $this->module->l('No combinations');
		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));

		$combinations = $this->getCombinationsForRequest();
		$id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'p.id_shop_default';
		$query_products = Db::getInstance()->executeS('SELECT
			p.`id_product`,
			pss.`price`
		FROM '._DB_PREFIX_.'product p
		JOIN `'._DB_PREFIX_.'product_shop` pss ON (p.`id_product` = pss.`id_product` AND pss.id_shop = '.pSQL($id_shop).')
		WHERE p.`id_product` IN ('.pSQL(implode(',', $ids_product)).')');
		$return_products = array();
		$return_combinations = array();
		$country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
		$address = new Address();
		$address->id_country = $country->id;
		foreach ($query_products as $product)
		{
			$price = 0;

			if ((int)Configuration::get('PS_TAX'))
			{
				$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
				$product_tax_calculator = $tax_manager->getTaxCalculator();
				$product['price_final'] = $product_tax_calculator->addTaxes($product['price']);
				$product['rate'] = $tax_manager->getTaxCalculator()->getTotalRate();
			}
			else
			{
				$product['price_final'] = $product['price'];
				$product['rate'] = 0;
			}
			$update_combinations = array();
			if ($type_price === self::TYPE_PRICE_BASE)
				$price = $product['price'];
			else if ($type_price === self::TYPE_PRICE_FINAL)
				$price = $product['price_final'];
			if ($change_for === self::CHANGE_FOR_PRODUCT)
				$price = $this->actionPrice($price, $action_price, $price_value);
			if ($change_for === self::CHANGE_FOR_COMBINATION && array_key_exists($product['id_product'], $combinations))
			{
				$product_combinations = $this->getCombinationsByIds($combinations[$product['id_product']], $id_shop);
				foreach ($product_combinations as $combination)
				{
					$price_pa = 0;
					$total_price_pa = 0;
					if ($type_price === self::TYPE_PRICE_BASE)
					{
						$price_pa = $combination['price'];
						$total_price_pa = $combination['total_price'];
					}
					else if ($type_price === self::TYPE_PRICE_FINAL)
					{
						$price_pa = $combination['price_final'];
						$total_price_pa = $combination['total_price_final'];
					}
					$price_pa = $this->actionPrice($price_pa, $action_price, $price_value);
					$total_price_pa = $this->actionPrice($total_price_pa, $action_price, $price_value);
					$final_price_pa = 0;
					$total_final_price_pa = 0;
					if ($type_price === self::TYPE_PRICE_FINAL)
					{
						$final_price_pa = $price_pa;
						$total_final_price_pa = $total_price_pa;
						if (Configuration::get('PS_TAX'))
							$price_pa = $price_pa / (100 + (int)$product['rate']) * 100;
						$total_price_pa = $total_price_pa / (100 + (int)$product['rate']) * 100;
					}
					else if ($type_price === self::TYPE_PRICE_BASE)
					{
						if (Configuration::get('PS_TAX'))
							$final_price_pa = $price_pa + ($price_pa / 100 * (int)$product['rate']);
						else
							$final_price_pa = $price_pa;
						$total_final_price_pa = $total_price_pa + ($total_price_pa / 100 * (int)$product['rate']);
					}
					$return_combinations[$combination['id_product_attribute']] = array(
						'price' => Tools::displayPrice($price_pa, $currency),
						'total_price' => Tools::displayPrice($combination['product_price'] + $total_price_pa, $currency),
						'price_final' => Tools::displayPrice($final_price_pa),
						'total_price_final' => Tools::displayPrice($combination['product_price_final'] + $total_final_price_pa, $currency)
					);
					$update_combinations[$combination['id_product_attribute']] = $price_pa;
				}
			}
			$final_price = 0;
			if ($type_price === self::TYPE_PRICE_FINAL)
			{
				$final_price = $price;
				if (Configuration::get('PS_TAX'))
					$price = $price / (100 + (int)$product['rate']) * 100;
			}
			else if ($type_price === self::TYPE_PRICE_BASE)
			{
				if (Configuration::get('PS_TAX'))
					$final_price = $price + ($price / 100 * (int)$product['rate']);
				else
					$final_price = $price;
			}
			if ($change_for === self::CHANGE_FOR_PRODUCT)
				$this->updatePriceProduct($product['id_product'], $price);
			if ($change_for === self::CHANGE_FOR_COMBINATION && count($update_combinations))
			{
				foreach ($update_combinations as $id_pa => $pa_price)
					$this->updatePriceCombination($id_pa, $pa_price);
			}
			$return_products[$product['id_product']] = array(
				'price' => Tools::displayPrice($price, $currency),
				'price_final' => Tools::displayPrice($final_price, $currency)
			);
		}
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products,
			'combinations' => $return_combinations
		)));
	}

	const ACTION_QUANTITY_INCREASE = 1;
	const ACTION_QUANTITY_REDUCE = 2;
	const ACTION_QUANTITY_REWRITE = 3;
	public function ajaxProcessSetQuantityAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$quantity = (int)Tools::getValue('quantity');
		$action_quantity = (int)Tools::getValue('action_quantity');
		$change_for = (int)Tools::getValue('change_for');
		$combinations = Tools::getValue('combinations');
		if ($change_for === self::CHANGE_FOR_COMBINATION && (!is_array($combinations) || (is_array($combinations) && !count($combinations))))
			$error[] = $this->module->l('No combinations');
		if (!(int)$quantity)
			$error[] = $this->module->l('Write quantity');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		$combinations = $this->getCombinationsForRequest();

		$return_products = array();
		$return_combinations = array();
		foreach ($products as $product)
		{
			if ($change_for === self::CHANGE_FOR_PRODUCT)
			{
				if (count($this->ids_shop))
					foreach ($this->ids_shop as $id_shop)
						$return_products[(int)$product['id']] = $this->setQuantity((int)$product['id'], 0, $quantity, $action_quantity, $id_shop);
			}
			if ($change_for === self::CHANGE_FOR_COMBINATION && array_key_exists((int)$product['id'], $combinations))
			{
				foreach ($combinations[(int)$product['id']] as $id_pa)
				{
					if (count($this->ids_shop))
						foreach ($this->ids_shop as $id_shop)
							$return_combinations[$id_pa] = $this->setQuantity((int)$product['id'], $id_pa, $quantity, $action_quantity, $id_shop);
				}
			}
		}
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products,
			'combinations' => $return_combinations
		)));
	}
	public function ajaxProcessSetActiveAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$active = (int)Tools::getValue('active');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		$return_products = array();
		foreach ($products as $product)
		{
			if (!Shop::isFeatureActive())
				Db::getInstance()->update('product', array(
					'active' => (int)$active
				), ' id_product = '.(int)$product['id']);
			Db::getInstance()->update('product_shop', array(
				'active' => (int)$active
			), ' id_product = '.(int)$product['id'].' '.(Shop::isFeatureActive() && $this->sql_shop ? ' AND id_shop '.$this->sql_shop : ''));
			$return_products[(int)$product['id']] = $active;
		}
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}
	public function ajaxProcessSetDescriptionAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$description = Tools::getValue('description');
		$type = Tools::getValue('type');

        $description_fields = array(
            'type_long' => 'description',
            'type_short' => 'description_short',
            'type_meta' => 'meta_description'
        );

		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		$return_products = array();

		foreach ($products as $product)
		{
            $product_description = pSQL(str_replace('[product_name]', $product['name'], $description), $type != 'type_meta');

            Db::getInstance()->update('product_lang', array(
                $description_fields[$type] => $product_description
            ), ' id_product = '.(int)$product['id']);

            $return_products[(int)$product['id']] = $product_description;
		}
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}

	public function ajaxProcessSetManufacturerAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$id_manufacturer = (int)Tools::getValue('id_manufacturer');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');
		$obj_manufacturer = new Manufacturer($id_manufacturer, $this->context->language->id);
		if (!Validate::isLoadedObject($obj_manufacturer))
			$error[] = $this->module->l('Manufacturer not exists');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		$ids_product = $this->getProductsForRequest();
		Db::getInstance()->update('product', array(
			'id_manufacturer' => (int)$id_manufacturer
		), ' id_product IN('.pSQL(implode(',', $ids_product)).')');
		$return_products = array();
		foreach ($products as $product)
			$return_products[(int)$product['id']] = $obj_manufacturer->name;
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}
	public function ajaxProcessSetAccessoriesAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$accessories = Tools::getValue('accessories');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');
		if (!is_array($accessories) || !count($accessories))
			$error[] = $this->module->l('No accessories');
		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		foreach ($products as $product)
		{
			$product = new Product((int)$product['id']);
			if (Validate::isLoadedObject($product))
				$product->setWsAccessories($accessories);
		}
		$return_products = array();
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}

	public function ajaxProcessSetSupplierAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$supplier = Tools::getValue('supplier');
		$id_supplier_default = (int)Tools::getValue('id_supplier_default');
		if (!$id_supplier_default)
			$error[] = $this->module->l('Supplier default no selected');
		if (!is_array($supplier) || !count($supplier))
			$error[] = $this->module->l('No suppliers');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');
		$obj_supplier = new Supplier($id_supplier_default, $this->context->language->id);
		if (!Validate::isLoadedObject($obj_supplier) && $id_supplier_default)
			$error[] = $this->module->l('Supplier not exists');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		foreach ($products as $product)
		{
			$product = new Product((int)$product['id']);
			if (Validate::isLoadedObject($product))
			{
				$product->deleteFromSupplier();
				foreach ($supplier as $sup)
					$product->addSupplierReference($sup, 0);
			}
		}
		$ids_product = $this->getProductsForRequest();
		Db::getInstance()->update('product', array(
			'id_supplier' => (int)$obj_supplier->id
		), ' id_product IN('.pSQL(implode(',', $ids_product)).')');
		$return_products = array();
		foreach ($products as $product)
			$return_products[(int)$product['id']] = $obj_supplier->name;
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}
	public function ajaxProcessSetShippingAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$carriers = Tools::getValue('carrier');
		$height = Tools::getValue('shipping_height');
		$width = Tools::getValue('shipping_width');
		$depth = Tools::getValue('shipping_depth');
		$weight = Tools::getValue('shipping_weight');
		$additional_shipping_cost = Tools::getValue('additional_shipping_cost');

		if (!is_array($carriers) || !count($carriers))
			$error[] = $this->module->l('No carriers');
		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');

		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		foreach ($products as $product)
		{

            $vars = array();
            $product_id = (int)$product['id'];
			$product = new Product($product_id);
			if (Validate::isLoadedObject($product))
			{
                if (strlen($height) > 0) {
                    $vars['height'] = (float)$height;
                }
                if (strlen($width) > 0) {
                    $vars['width'] = (float)$width;
                }
                if (strlen($depth) > 0) {
                    $vars['depth'] = (float)$depth;
                }
                if (strlen($weight) > 0) {
                    $vars['weight'] = (float)$weight;
                }
                if (strlen($additional_shipping_cost) > 0) {
                    $vars['additional_shipping_cost'] = (float)$additional_shipping_cost;
                }
				$product->setCarriers($carriers);

                Db::getInstance()->update('product', $vars, ' id_product = '.$product_id);
			}
		}
		$ids_product = $this->getProductsForRequest();
		$return_products = array();
		die(Tools::jsonEncode(array(
			'hasError' => false,
			'products' => $return_products
		)));
	}
	public function ajaxProcessSetDiscountAllProduct()
	{
		$error = array();
		$products = Tools::getValue('products');
		$id_currency = Tools::getValue('sp_id_currency');
		$id_country = Tools::getValue('sp_id_country');
		$id_group = Tools::getValue('sp_id_group');
		$price = -1;
		$from_quantity = Tools::getValue('sp_from_quantity');
		$reduction = (float)Tools::getValue('sp_reduction');
		$reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
		$from = Tools::getValue('sp_from');
		if (!$from)
			$from = '0000-00-00 00:00:00';
		$to = Tools::getValue('sp_to');
		if (!$to)
			$to = '0000-00-00 00:00:00';
		$id_shop = $this->context->shop->id;

		$change_for = (int)Tools::getValue('change_for');
		$combinations = $this->getCombinationsForRequest();
		if ($change_for === self::CHANGE_FOR_COMBINATION && (!is_array($combinations) || (is_array($combinations) && !count($combinations))))
			$error[] = $this->module->l('No combinations');

		if (!is_array($products) || !count($products))
			$error[] = $this->module->l('No products');
		if ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100))
			$error[] = $this->module->l('Product №%s: submitted reduction value (0-100) is out-of-range');
		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		foreach ($products as $product)
		{
			SpecificPrice::deleteByProductId((int)$product['id']);
			if ($change_for === self::CHANGE_FOR_PRODUCT)
			{
				if ($this->validateSpecificPrice((int)$product['id'],
					$id_shop,
					$id_currency,
					$id_country,
					$id_group,
					0,
					$price,
					$from_quantity,
					$reduction, $reduction_type, $from, $to, 0, $error))
				{
					$specific_price = new SpecificPrice();
					$specific_price->id_product = (int)$product['id'];
					$specific_price->id_product_attribute = (int)0;
					$specific_price->id_shop = (int)$id_shop;
					$specific_price->id_currency = (int)$id_currency;
					$specific_price->id_country = (int)$id_country;
					$specific_price->id_group = (int)$id_group;
					$specific_price->id_customer = 0;
					$specific_price->price = (float)$price;
					$specific_price->from_quantity = (int)$from_quantity;
					$sp_reduction = $reduction_type == 'percentage' ? $reduction / 100 : $reduction;
					$specific_price->reduction = (float)$sp_reduction;
					$specific_price->reduction_type = $reduction_type;
					$specific_price->from = $from;
					$specific_price->to = $to;
					if (!$specific_price->add())
						$error[] = sprintf($this->module->l('Product №%s: an error occurred while updating the specific price.'), $product['id']);
				}
			}
			if ($change_for === self::CHANGE_FOR_COMBINATION && array_key_exists((int)$product['id'], $combinations))
			{
				foreach ($combinations[(int)$product['id']] as $id_pa)
				{
					if ($this->validateSpecificPrice((int)$product['id'],
						$id_shop,
						$id_currency,
						$id_country,
						$id_group,
						$id_pa,
						$price,
						$from_quantity,
						$reduction, $reduction_type, $from, $to, 0, $error))
					{
						$specific_price = new SpecificPrice();
						$specific_price->id_product = (int)$product['id'];
						$specific_price->id_product_attribute = (int)$id_pa;
						$specific_price->id_shop = (int)$id_shop;
						$specific_price->id_currency = (int)$id_currency;
						$specific_price->id_country = (int)$id_country;
						$specific_price->id_group = (int)$id_group;
						$specific_price->id_customer = 0;
						$specific_price->price = (float)$price;
						$specific_price->from_quantity = (int)$from_quantity;
						$sp_reduction = $reduction_type == 'percentage' ? $reduction / 100 : $reduction;
						$specific_price->reduction = (float)$sp_reduction;
						$specific_price->reduction_type = $reduction_type;
						$specific_price->from = $from;
						$specific_price->to = $to;
						if (!$specific_price->add())
							$error[] = sprintf($this->module->l('Product №%s: an error occurred while updating the specific price.'), $product['id']);
					}
				}
			}
		}
		if (count($error))
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'error' => implode('<br>', $error)
			)));
		else
			die(Tools::jsonEncode(array(
				'hasError' => false
			)));
	}
	public function validateSpecificPrice($id_product, $id_shop,
										$id_currency,
										$id_country,
										$id_group,
										$id_customer,
										$price, $from_quantity,
										$reduction,
										$reduction_type,
										$from, $to, $id_combination = 0, &$errors)
	{
		if (!Validate::isUnsignedId($id_shop)
			|| !Validate::isUnsignedId($id_currency)
			|| !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer))
			$errors[] = sprintf($this->module->l('Product №%s: wrong IDs'), $id_product);
		elseif ((!isset($price)
				&& !isset($reduction))
			|| (isset($price)
				&& !Validate::isNegativePrice($price))
			|| (isset($reduction) && !Validate::isPrice($reduction)))
			$errors[] = sprintf($this->module->l('Product №%s: invalid price/discount amount'), $id_product);
		elseif (!Validate::isUnsignedInt($from_quantity))
			$errors[] = sprintf($this->module->l('Product №%s: invalid quantity'), $id_product);
		elseif ($reduction && !Validate::isReductionType($reduction_type))
			$errors[] = sprintf($this->module->l('Product №%s: please select a discount type (amount or percentage).'), $id_product);
		elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to)))
			$errors[] = sprintf($this->module->l('Product №%s: the from/to date is invalid.'), $id_product);
		elseif (SpecificPrice::exists((int)$id_product,
			$id_combination,
			$id_shop,
			$id_group,
			$id_country,
			$id_currency,
			0, $from_quantity, $from, $to, false))
			$errors[] = sprintf($this->module->l('Product №%s: a specific price already exists for these parameters.'), $id_product);
		else
			return true;
		return false;
	}
	public function intValueRequestVar(&$var)
	{
		if (!is_array($var))
			return false;
		foreach ($var as &$item)
			$item = (int)$item;
	}
	public function stringValueRequestVar(&$var)
	{
		if (!is_array($var))
			return false;
		foreach ($var as &$item)
			$item = pSQL($item);
	}
	public function getProductsForRequest()
	{
		$products = Tools::getValue('products');
		$ids_product = array();
		foreach ($products as $product)
			$ids_product[] = (int)$product['id'];
		return $ids_product;
	}

	public function getCombinationsForRequest()
	{
		$combinations = Tools::getValue('combinations');
		$tmp_combinations = array();
		if (is_array($combinations) && count($combinations))
			foreach ($combinations as $combination)
			{
				$combination = explode('_', $combination);
				if (!array_key_exists((int)$combination[0], $tmp_combinations))
					$tmp_combinations[(int)$combination[0]] = array();
				$tmp_combinations[(int)$combination[0]][] = (int)$combination[1];
			}
		$combinations = $tmp_combinations;
		return $combinations;
	}
}
