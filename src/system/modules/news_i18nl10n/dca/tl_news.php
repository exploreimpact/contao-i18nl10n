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
 * @author      Patric Eberle <line-in@derverstaerker.ch>
 * @package     i18nl10n
 * @license     LGPLv3 http://www.gnu.org/licenses/lgpl-3.0.html
 */

$GLOBALS['TL_DCA']['tl_news']['list']['operations']['i18nl10n'] = array
(
    'label'               => 'L10Ns',
    'href'                => 'do=i18nl10n',
    'button_callback'     => array('tl_news_l10n', 'editL10n')
);



class tl_news_l10n extends Backend
{
    public function editL10n($row, $href, $label, $title, $icon)
    {
        $title = sprintf($GLOBALS['TL_LANG']['MSC']['editL10n'],"\"{$row['title']}\"");
        $buttonURL = $this->addToUrl($href . '&amp;node=' . $row['id']) ;

        $button = '
            <a  href="' . $buttonURL . '"
                title="' . specialchars($title) . '"
                onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\'Dateien auswählen\',\'url\':this.href,\'id\':\'singleSRC\'});return false"
                >
                <img src="system/modules/core_i18nl10n/assets/img/i18nl10n.png" />
            </a>';

        return $button;
    }
}