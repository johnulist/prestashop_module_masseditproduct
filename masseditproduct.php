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
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2015 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class MassEditProduct extends Module
{
	public function __construct()
	{
		$this->name = 'masseditproduct';
		$this->tab = 'front_office_features';
		$this->version = '1.3.5';
		$this->author = 'SeoSa';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Mass edit product');
		$this->description = $this->l('Mass edit product');
		$this->module_key = '6f052f2d8d49a03ec1d864d012e19ad7';
	}
	public function install()
	{
		$this->createTab('AdminMassEditProduct', 'AdminCatalog', array(
			'en' => 'Mass edit product',
			'ru' => 'Массовое редактирование товаров'
		));
		if (!parent::install())
			return false;
		return true;
	}
	public function uninstall()
	{
		$this->deleteTab('AdminMassEditProduct');
		if (!parent::uninstall())
			return false;
		return true;
	}
	public function createTab($class_name, $parent, $name)
	{
		if (!is_array($name))
			$name = array('en' => $name);
		elseif (is_array($name) && !count($name))
			$name = array('en' => $class_name);
		elseif (is_array($name) && count($name) && !isset($name['en']))
			$name['en'] = current($name);

		$tab = new Tab();
		$tab->class_name = $class_name;
		$tab->module = $this->name;
		$tab->id_parent = Tab::getIdFromClassName($parent);
		$tab->active = true;
		foreach ($this->getLanguages() as $l)
			$tab->name[$l['id_lang']] = (isset($name[$l['iso_code']]) ? $name[$l['iso_code']] : $name['en']);
		$tab->save();
	}
	public function deleteTab($class_name)
	{
		$tab = Tab::getInstanceFromClassName($class_name);
		$tab->delete();
	}
	public $languages;
	public function getLanguages()
	{
		if (!is_null($this->languages))
			return $this->languages;
		$languages = Language::getLanguages(false);
		foreach ($languages as &$l)
			$l['is_default'] = (Configuration::get('PS_DEFAULT_LANG') == $l['id_lang']);
		$this->languages = $languages;
		return $languages;
	}
	public function autoloadCSS()
	{
		$dir_css = _PS_MODULE_DIR_.'masseditproduct/views/css/autoload/';
		$files = glob($dir_css.'*.css');
		foreach ($files as $file)
			$this->context->controller->addCSS($this->_path.'views/css/autoload/'.str_replace($dir_css, '', $file));
	}
}