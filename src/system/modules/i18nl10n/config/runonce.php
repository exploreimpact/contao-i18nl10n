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
        // If Contao addLanguageToUrl is used, disable it
        if ($this->Config->get('addLanguageToUrl')) {
            $this->deleteConfig('addLanguageToUrl');

            // Check if urlParam value already set, else set url
            if (!$this->Config->get('i18nl10n_urlParam')) {
                $this->addConfig('i18nl10n_urlParam', 'url');

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
        $this->moveI18nl10nLanguageSettings();
        $this->renameTableFields();

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
        if ($this->tableExists('tl_page_i18nl10n')) {
            $this->Database->query('DELETE FROM tl_page_i18nl10n WHERE NOT EXISTS (SELECT * FROM tl_page as p WHERE tl_page_i18nl10n.pid = p.id)');
        }
    }

    /**
     * Move i18nl10n language settings to first root page
     */
    private function moveI18nl10nLanguageSettings() {
        if ($arrLanguages = $this->Config->get('i18nl10n_languages')) {

            // Get first root page
            $objRootPage = $this->Database
                ->query('SELECT * FROM tl_page WHERE type = "root" ORDER BY id LIMIT 0,1');


            if($objRootPage->first() && !$this->Database->fieldExists('i18nl10n_localizations', 'tl_page')) {

                // Remove root language from languages
                foreach ($arrLanguages as $key => $strLanguage) {
                    if ($objRootPage->language === $strLanguage) {
                        unset($arrLanguages[$key]);
                    }
                }

                $arrMappedLanguages = array();

                // Map to db format
                foreach ($arrLanguages as $language) {
                    $arrMappedLanguages[] = array
                    (
                        'language' => $language
                    );
                }

                // Add new field
                $this->Database->query('ALTER TABLE tl_page ADD i18nl10n_localizations BLOB NULL');

                // Set localizations
                $this->Database
                    ->prepare('UPDATE tl_page SET i18nl10n_localizations = ? WHERE type = "root" ORDER BY id')
                    ->limit(1)
                    ->execute(serialize($arrMappedLanguages));
            }
        }

    }

    /**
     * Rename 'old' table fields
     */
    private function renameTableFields() {
        if ($this->Database->fieldExists('l10n_published', 'tl_page')) {
            $this->Database->query('ALTER TABLE tl_page CHANGE l10n_published i18nl10n_published char(1) NOT NULL default 1');
        }

        if ($this->Database->fieldExists('l10n_published', 'tl_page_i18nl10n')) {
            $this->Database->query('ALTER TABLE tl_page_i18nl10n CHANGE l10n_published i18nl10n_published char(1) NOT NULL default 1');
        }

        if ($this->Database->fieldExists('i18nl10nLangTpl', 'tl_module')) {
            $this->Database->query('ALTER TABLE tl_module CHANGE i18nl10nLangTpl i18nl10n_langTpl varchar(64) NOT NULL default ""');
        }

        if ($this->Database->fieldExists('i18nl10nLangStyle', 'tl_module')) {
            $this->Database->query('ALTER TABLE tl_module CHANGE i18nl10nLangStyle i18nl10n_langStyle varchar(64) NOT NULL default ""');
        }

        if ($this->Database->fieldExists('i18nl10nLangHide', 'tl_module')) {
            $this->Database->query('ALTER TABLE tl_module CHANGE i18nl10nLangHide i18nl10n_langHide char(1) NOT NULL default ""');
        }
    }
}

$objI18nl10nRunOnceJob = new I18nl10nRunOnceJob();
$objI18nl10nRunOnceJob->run();
