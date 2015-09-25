<?php
/**
 * i18nl10n Contao Module
 *
 * The i18nl10n module for Contao allows you to manage multilingual content
 * on the element level rather than with page trees.
 *
 *
 * @copyright   Copyright (c) 2014-2015 Verstärker, Patric Eberle
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n classes
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace Verstaerker\i18nl10n\Widgets;

use Verstaerker\I18nl10n\Classes\I18nl10n;

class I18nl10nMetaWizard extends \MetaWizard {

    /**
     * Generate file i18nl10n meta wizard
     *
     * @return string
     */
    public function generate()
    {
        $strForm = parent::generate();

        $arrAvailableLanguages = I18nl10n::getInstance()->getAvailableLanguages(true, true);
        $arrLanguages = array_intersect_key($this->getLanguages(), array_flip($arrAvailableLanguages));
        preg_match_all('@data-language="(.*?)"@', $strForm, $arrMatchTaken);

        $options = array('<option value="">-</option>');
        $taken = array();

        // Parse used languages
        foreach ($arrMatchTaken as $k => $v) {
            if ($k) {
                $taken[] = $v[0];
            }
        }

        // Set language options
        foreach ($arrLanguages as $k => $v) {
            $options[] = '<option value="' . $k . '"' . (in_array($k, $taken) ? ' disabled' : '') . '>' . $v . '</option>';
        }

        // Replace language select options
        $strForm = preg_replace('@(<select.*?>)(.*?)(<\/select>)@', '${1}' . implode('', $options) . '${3}', $strForm);

        return $strForm;
    }
}
