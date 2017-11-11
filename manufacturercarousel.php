<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Selwyn Orren
 *  @copyright 2017 Linuxweb
 *  @license   LICENSE.md
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Manufacturercarousel extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'manufacturercarousel';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Selwyn Orren';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Manufacturers Carousel');
        $this->description = $this->l('Manufacturer logo carousel for front page');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('MF_TITLE', 'Our Brands');
        Configuration::updateValue('MF_MAN_NUMBER', 0);
        Configuration::updateValue('MF_PER_ROW', 4);
        Configuration::updateValue('MF_MAN_ORDER', 'name_asc');
        Configuration::updateValue('MF_SHOW_MAN_NAME', 0);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('MF_TITLE');
        Configuration::deleteByName('MF_MAN_NUMBER');
        Configuration::deleteByName('MF_PER_ROW');
        Configuration::deleteByName('MF_MAN_ORDER');
        Configuration::deleteByName('MF_SHOW_MAN_NAME');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitMClModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMClModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('Show manufacturer name'),
                        'name' => 'MF_TITLE',
                        'label' => $this->l('Enable Manufacturers Name'),
                        'required' => true,
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('Number of manufacturers to display (Enter 0 to display all)'),
                        'name' => 'MF_MAN_NUMBER',
                        'label' => $this->l('Number of Manufacturers'),
                        'required' => true,
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('How many logo\'s should be visible'),
                        'name' => 'MF_PER_ROW',
                        'label' => $this->l('Logo\'s per row'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'desc' => 'How the logo\'s should be sorted',
                        'name' => 'MF_MAN_ORDER',
                        'label' => $this->l('Order by'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'name_asc',
                                    'name' => $this->l('Name ASC'),
                                ),
                                array(
                                    'id_option' => 'name_desc',
                                    'name' => $this->l('Name DESC'),
                                ),
                                array(
                                    'id_option' => 'manu_asc',
                                    'name' => $this->l('Manufacturer ID ASC'),
                                ),
                                array(
                                    'id_option' => 'manu_desc',
                                    'name' => $this->l('Manufacturer ID DESC'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Manufacturers Name'),
                        'name' => 'MF_SHOW_MAN_NAME',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'MF_TITLE' => Configuration::get('MF_TITLE', 'Our Brands'),
            'MF_MAN_NUMBER' => Configuration::get('MF_MAN_NUMBER', 0),
            'MF_PER_ROW' => Configuration::get('MF_PER_ROW', 0),
            'MF_MAN_ORDER' => Configuration::get('MF_MAN_ORDER', 'name_asc'),
            'MF_SHOW_MAN_NAME' => Configuration::get('MF_SHOW_MAN_NAME', 0),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
    }

    /**
     * Get the Manufaturers ID, Name, SEO Friendly URL and Logo (If Exists)
     */
    public function getManufactures($orderby = 'name asc', $limit = false)
    {
    $sql = "SELECT id_manufacturer, name
            FROM "._DB_PREFIX_."manufacturer
            WHERE active = 1
            ORDER BY ".$orderby . ($limit ? '
            LIMIT 0,'.(int)$limit : '');
    $ms = Db::getInstance()->executeS($sql);
    if($ms)
        foreach($ms as &$m)
        {
            $m['link_rewrite'] = Tools::link_rewrite($m['name']);
        }
    return $ms;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/owl.carousel.min.js');
        $this->context->controller->addCSS($this->_path.'/views/css/owl.carousel.min.css');

        $this->context->controller->addJS($this->_path.'/views/js/mf.custom.js');
        $this->context->controller->addCSS($this->_path.'/views/css/mf.theme.default.css');
    }

    public function hookDisplayHome()
    {
        switch (Configuration::get('MF_MAN_ORDER'))
        {
            case 'name_desc':
                $order = 'name desc';
                break;
            case 'manu_asc':
                $order = 'id_manufacturer asc';
                break;
            case 'manu_desc':
                $order = 'id_manufacturer desc';
                break;
            default:
                $order = 'name asc';
                break;
        }
        $manufacturers = $this->getManufactures($order, (int)Configuration::get('MF_MAN_NUMBER'));
        foreach ($manufacturers as &$manufacturer)
        {
            if(file_exists(_PS_MANU_IMG_DIR_.$manufacturer['id_manufacturer'].'.jpg'))
                $manufacturer['image'] = _THEME_MANU_DIR_.$manufacturer['id_manufacturer'].'.jpg';
            else
                $manufacturer['image'] = $this->_path.'images/default_logo.jpg';
        }
        $this->smarty->assign(array(
            'manufacturers' => $manufacturers,
            'MF_TITLE' => Configuration::get('MF_TITLE'),
            'MF_SHOW_MAN_NAME' => (int)Configuration::get('MF_SHOW_MAN_NAME'),
            'MF_PER_ROW' => (int)Configuration::get('MF_PER_ROW'),
            'link' => $this->context->link,
            'view_all_mnf' => $this->context->link->getPageLink('manufacturer')
        ));

        return $this->display(__FILE__, 'mf_carousel.tpl');
    }
}
