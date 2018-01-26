<?php

namespace Verstaerker\I18nl10nBundle\Hook;

use Verstaerker\I18nl10nBundle\Classes\I18nl10n;

/**
 * Class LoadDataContainerHook
 * @package Verstaerker\I18nl10nBundle\Hook
 *
 * https://docs.contao.org/books/api/extensions/hooks/loadDataContainer.html
 */
class LoadDataContainerHook
{
    /**
     * @param $strName
     */
    public function setLanguages($strName)
    {
        // Some modules are not able to support user permission base languages, so get all
        $arrLanguages = I18nl10n::getInstance()->getAvailableLanguages(false, true);

        // @todo: add neutral?

        // @todo: refactor modules to get languages from config too
        $GLOBALS['TL_DCA'][$strName]['config']['languages'] = $arrLanguages;
    }

    /**
     * loadDataContainer hook
     *
     * Add onload_callback definition when loadDataContainer hook is
     * called to define onload_callback as late as possible
     *
     * @param   String  $strName
     */
    public function appendLanguageSelectCallback($strName)
    {
        if ($strName === 'tl_content' &&
            !in_array(\Input::get('do'), I18nl10n::getInstance()->getUnsupportedModules())
        ) {
            $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] =
                array('tl_content_l10n', 'appendLanguageInput');
        }
    }

    /**
     * loadDataContainer hook
     *
     * Redefine button_callback for tl_content elements to allow permission
     * based display/hide.
     *
     * @param   String  $strName
     */
    public function appendButtonCallback($strName)
    {
        // Append tl_content callbacks
        if ($strName === 'tl_content' && \Input::get('do') === 'article') {
            $this->setButtonCallback('tl_content', 'edit');
            $this->setButtonCallback('tl_content', 'copy');
            $this->setButtonCallback('tl_content', 'cut');
            $this->setButtonCallback('tl_content', 'delete');
            $this->setButtonCallback('tl_content', 'toggle');
        }

        // Append tl_page callbacks
        if ($strName === 'tl_page' && \Input::get('do') === 'page') {
            $this->setButtonCallback('tl_page', 'edit');
            $this->setButtonCallback('tl_page', 'copy');
            $this->setButtonCallback('tl_page', 'copyChilds');  // Copy with children button
            $this->setButtonCallback('tl_page', 'cut');
            $this->setButtonCallback('tl_page', 'delete');
            $this->setButtonCallback('tl_page', 'toggle');
        }
    }

    /**
     * Set button callback for given table and operation
     *
     * @param $strTable
     * @param $strOperation
     */
    private function setButtonCallback($strTable, $strOperation)
    {
        $arrVendorCallback = $GLOBALS['TL_DCA'][$strTable]['list']['operations'][$strOperation]['button_callback'];

        switch ($strTable) {
            case 'tl_page':
                $objCallback = new \tl_page_l10n();
                break;

            case 'tl_content':
                $objCallback = new \tl_content_l10n();
                break;

            default:
                return;
        }

        // Create an anonymous function to handle callback from different DCAs
        $GLOBALS['TL_DCA'][$strTable]['list']['operations'][$strOperation]['button_callback'] =
            function () use ($strTable, $objCallback, $strOperation, $arrVendorCallback) {

                // Get callback arguments
                $arrArgs = func_get_args();

                return call_user_func_array(
                    array($objCallback, 'createButton'),
                    array($strOperation, $arrArgs, $arrVendorCallback)
                );
            };
    }

    /**
     * List label callback for loadDataContainer hook
     *
     * Appending label callback for tl_article while keeping original callback
     *
     * @param $strName
     */
    public function appendLabelCallback($strName)
    {
        // Append tl_content callbacks
        if ($strName === 'tl_article' && \Input::get('do') === 'article') {
            $arrVendorCallback = $GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'];
            $objCallback = new \tl_article_l10n();

            // Create an anonymous function to handle callback from different DCAs
            $GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'] =
                function () use ($objCallback, $arrVendorCallback) {
                    // Get callback arguments
                    $arrArgs = func_get_args();

                    return call_user_func_array(
                        array($objCallback, 'labelCallback'),
                        array($arrArgs, $arrVendorCallback)
                    );
                };
        }
    }

    /**
     * Child record callback for loadDataContainer hook
     *
     * Appending child record callback for tl_content while keeping original callback
     *
     * @param $strName
     */
    public function appendChildRecordCallback($strName)
    {
        // Append tl_content callbacks
        if ($strName === 'tl_content' &&
            !in_array(\Input::get('do'), I18nl10n::getInstance()->getUnsupportedModules())
        ) {
            $arrVendorCallback = $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'];
            $objCallback = new \tl_content_l10n();

            // Create an anonymous function to handle callback from different DCAs
            $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] =
                function () use ($objCallback, $arrVendorCallback) {
                    // Get callback arguments
                    $arrArgs = func_get_args();

                    return call_user_func_array(
                        array($objCallback, 'childRecordCallback'),
                        array($arrArgs, $arrVendorCallback)
                    );
                };
        }
    }
}
