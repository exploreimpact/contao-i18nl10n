<?php

/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * PHP version 5
 * @copyright   Verstärker, Patric Eberle 2014
 * @copyright   Krasimir Berov 2010-2013
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @author      Krasimir Berov
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['i18nl10nLanguageNavigation'] = '
    {title_legend},name,headline,type;
    {template_legend:hide},i18nl10nLanguageTpl,i18nl10nLanguageStyle;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space
';

$i18nl10nLanguageNavigation = array
(
    'i18nl10nLanguageTpl' => array
    (
        'label'             => &$GLOBALS['TL_LANG']['tl_module']['i18nl10nLanguageTpl'],
        'exclude'           => true,
        'inputType'         => 'select',
        'options_callback'  => array('tl_module_l10n', 'getLanguageTemplates'),
        'default'           => 'lang_default',
        'eval'              => array
        (
            'tl_class'  => 'w50'
        ),
        'sql'               => "varchar(64) NOT NULL default ''"
    ),
    'i18nl10nLanguageStyle' => array
    (
        'label'             => &$GLOBALS['TL_LANG']['tl_module']['i18nl10nLanguageStyle'],
        'exclude'           => true,
        'inputType'         => 'radio',
        'default'           => 'full',
        'options'           => array('full', 'text', 'image', 'disable'),
        'reference'         => &$GLOBALS['TL_LANG']['tl_module']['i18nl10nLanguageStyleLabels'],
        'eval'              => array
        (
            'tl_class'  => 'w50 autoheight'
        ),
        'sql'               => "varchar(64) NOT NULL default ''"
    )

);

array_insert(
    $GLOBALS['TL_DCA']['tl_module']['fields'],
    count($GLOBALS['TL_DCA']['tl_module']['fields']) + 1,
    $i18nl10nLanguageNavigation
);


class tl_module_l10n extends Backend
{
    /**
     * Return all navigation templates as array
     * @return array
     */
    public function getLanguageTemplates()
    {
        return $this->getTemplateGroup('lang_');
    }
}