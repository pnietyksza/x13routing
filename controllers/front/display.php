<?php

/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class X13routingdisplayModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $languages = Language::getLanguages();
        $title = [];
        $description = [];

        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            $title[$idLang] = Configuration::get("input_title_$idLang", null, null, null, 'Cześć X13');
            $description[$idLang] = Configuration::get("input_description_$idLang", null, null, null);
        }

        $currentLanguage = $this->context->language->id;
        $this->context->smarty->assign('title', $title[$currentLanguage]);
        $this->context->smarty->assign('description', $description[$currentLanguage]);
        $this->setTemplate('module:x13routing/views/templates/front/display.tpl');
    }
}
