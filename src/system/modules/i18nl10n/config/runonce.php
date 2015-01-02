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


/**
 * Class I18nl10nRunOnceJob
 *
 * Update or prepare module
 */
class I18nl10nRunOnceJob extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
        $this->import('Config');
    }

    /**
     * Move old settings to root page and update or prepare settings
     */
    public function run()
    {
        if ($this->Config->get('addLanguageToUrl')) {
            $this->deleteConfig('addLanguageToUrl');

            // Check if urlParam value already set, else set url
            if (!$this->Config->get('i18nl10n_urlParam')) {
                $this->addConfig('i18nl10n_urlParam', 'parameter');

            }
        } else {
            // Check if urlParam needs to be set
            if (!$this->Config->get('i18nl10n_urlParam')) {

                // Determin old setting or use default
                if ($this->Config->get('i18nl10n_addLanguageToUrl')) {
                    $strType = 'url';
                } elseif ($this->Config->get('i18nl10n_alias_suffix')) {
                    $strType = 'alias';
                } else {
                    $strType = 'parameter';
                }

                $this->addConfig('i18nl10n_urlParam', $strType);
            }
        }

        // If deprecated settings are used, move them to root page
        if ($arrLanguages = $this->Config->get('i18nl10n_languages')) {

            // Get first root page
            $objRootPage = $this->Database
                ->query('SELECT * FROM tl_page WHERE type = "root" ORDER BY id LIMIT 0,1');


            if($objRootPage->first() && !$objRootPage->i18nl10n_languages) {
                // Remove root language from languages
                foreach ($arrLanguages as $key => $strLanguage) {
                    if ($objRootPage->language === $strLanguage) {
                        unset($arrLanguages[$key]);
                    }
                }

                // Set localizations
                $this->Database
                    ->prepare('UPDATE tl_page SET i18nl10n = ? WHERE type = "root" ORDER BY id')
                    ->limit(1)
                    ->execute(serialize($arrLanguages));
            }
        }

        $this->removeL10nPageOrphans();
        $this->removeDeprecatedSettings();
    }

    /**
     * Set a config value
     * 
     * @param $strKey
     * @param $strValue
     */
    private function addConfig($strKey, $strValue)
    {
        $this->Config->add("\$GLOBALS['TL_CONFIG']['$strKey']", $strValue);
    }

    /**
     * Delete a config entry
     *
     * @param $varKey   String|Array
     */
    private function deleteConfig($varKey)
    {
        switch (gettype($varKey)) {
            case 'string':
                $this->Config->delete("\$GLOBALS['TL_CONFIG']['$varKey']");
                break;

            case 'array':
                foreach ($varKey as $key) {
                    $this->deleteConfig($key);
                }
                break;
        }
    }

    /**
     * Remove deprecated settings
     */
    private function removeDeprecatedSettings()
    {
        $arrKeys = array(
            'i18nl10n_default_language',
            'i18nl10n_languages',
            'i18nl10n_addLanguageToUrl'
        );

        $this->deleteConfig($arrKeys);
    }

    /**
     * Remove orphaned tl_page_i18nl10n entries
     */
    private function removeL10nPageOrphans()
    {
        $this->Database
            ->query('DELETE FROM tl_page_i18nl10n WHERE NOT EXISTS (SELECT * FROM tl_page as p WHERE tl_page_i18nl10n.pid = p.id)');
    }
}

$objI18nl10nRunOnceJob = new I18nl10nRunOnceJob();
$objI18nl10nRunOnceJob->run();
