<?php
/**
 * 2006-2021 THECON SRL
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
 * USED BY THIS MODULE.
 *
 * @author    THECON SRL <contact@thecon.ro>
 * @copyright 2006-2021 THECON SRL
 * @license   Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Thprodsearchref extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'thprodsearchref';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Presta Maniacs';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Search by Combination Reference in Admin Products');
        $this->description = $this->l('Enable Search by Combination Reference in Admin Products Catalog.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHooks()) {
            return false;
        }

        return true;
    }

    public function registerHooks()
    {
        if (!$this->registerHook('actionAdminProductsListingFieldsModifier')) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/maniacs.tpl');
    }

    public function hookActionAdminProductsListingFieldsModifier($list)
    {
        $search = Tools::getValue('filter_column_reference');
        if (!empty($search)) {
            if (isset($list['sql_where'])) {
                $list['sql_where'] = array('
                    (
                        EXISTS(SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute` `pa` WHERE pa.`id_product` = p.`id_product` AND (pa.`reference` LIKE \'%' . pSQL($search) . '%\'
                        OR pa.`supplier_reference` LIKE \'%' . pSQL($search) . '%\'
                        OR pa.`ean13` LIKE \'%' . pSQL($search) . '%\'
                        OR pa.`isbn` LIKE \'%' . pSQL($search) . '%\'
                        OR pa.`upc` LIKE \'%' . pSQL($search) . '%\'))
                    )
                    OR
                        p.`reference` LIKE \'%' . pSQL($search) . '%\'
                   '
                );
            }
        }
    }
}
