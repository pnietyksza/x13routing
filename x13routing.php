<?php

/**
 * 2007-2023 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class X13routing extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'x13routing';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'P.N.';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('routing module for x13');
        $this->description = $this->l('just for custom routes');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('moduleRoutes');
    }

    public function uninstall()
    {
        $form_values = $this->getConfigFormValues();
        foreach ($form_values as $key => $value) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitX13routingModule')) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitX13routingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        $languages = Language::getLanguages();
        $fields = [];

        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            $fields[] = [
                'col' => 3,
                'type' => 'text',
                'name' => "input_title_$idLang",
                'label' => $this->l('Title') . ' (' . $language['name'] . ')',
            ];
            $fields[] = [
                'col' => 3,
                'type' => 'text',
                'name' => "input_description_$idLang",
                'label' => $this->l('Description') . ' (' . $language['name'] . ')',
            ];
            $fields[] = [
                'col' => 3,
                'type' => 'text',
                'desc' => $this->l('Enter URL'),
                'name' => "input_url_$idLang",
                'label' => $this->l('URL') . ' (' . $language['name'] . ')',
            ];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => $fields,
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages();
        $values = [];

        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            $values["input_title_$idLang"] = Configuration::get("input_title_$idLang", null, null, null, 'Cześć X13');
            $values["input_description_$idLang"] = Configuration::get("input_description_$idLang", null, null, null);
            $values["input_url_$idLang"] = Configuration::get("input_url_$idLang", null, null, null, 'czesc');
        }

        return $values;
    }

    protected function postProcess()
    {
        $languages = Language::getLanguages();

        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            Configuration::updateValue("input_title_$idLang", Tools::getValue("input_title_$idLang"));
            Configuration::updateValue("input_description_$idLang", Tools::getValue("input_description_$idLang"));
            Configuration::updateValue("input_url_$idLang", Tools::getValue("input_url_$idLang"));
        }
    }

    public function hookModuleRoutes($params)
    {
        $languages = Language::getLanguages();
        $routes = [];

        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            $url = Configuration::get("input_url_$idLang", null, null, null, 'czesc');
            $route = [
                'controller' => 'display',
                'rule' => $url,
                'keywords' => [
                    'link_rewrite' => [
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'link_rewrite'
                    ],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'X13routing',
                ]
            ];
            $routes["X13routing_$url_$idLang"] = $route;
        }

        return $routes;
    }
}
