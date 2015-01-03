<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */


// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['i18nl10nLanguageSelection'] = '
    {title_legend},name,headline,type;
    {template_legend:hide},i18nl10n_langTpl,i18nl10n_langStyle,i18nl10n_langHide;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space
';

$i18nl10nLanguageSelection = array
(
    'i18nl10n_langTpl'   => array
    (
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['i18nl10n_langTpl'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => array('tl_module_l10n', 'getLanguageTemplates'),
        'default'          => 'lang_default',
        'eval'             => array
        (
            'tl_class' => 'w50'
        ),
        'sql'              => "varchar(64) NOT NULL default ''"
    ),
    'i18nl10n_langStyle' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['i18nl10n_langStyle'],
        'exclude'   => true,
        'inputType' => 'radio',
        'default'   => 'full',
        'options'   => array('full', 'text', 'image', 'iso', 'disable'),
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['i18nl10n_langStyleLabels'],
        'eval'      => array
        (
            'tl_class' => 'w50 autoheight'
        ),
        'sql'       => "varchar(64) NOT NULL default ''"
    ),
    'i18nl10n_langHide'  => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['i18nl10n_langHide'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array
        (
            'tl_class' => 'clr'
        ),
        'sql'       => "char(1) NOT NULL default ''"
    )

);

array_insert(
    $GLOBALS['TL_DCA']['tl_module']['fields'],
    count($GLOBALS['TL_DCA']['tl_module']['fields']) + 1,
    $i18nl10nLanguageSelection
);


class tl_module_l10n extends Backend
{

    /**
     * Return all navigation templates as array
     *
     * @returns array
     */
    public function getLanguageTemplates()
    {
        return $this->getTemplateGroup('lang_');
    }

}