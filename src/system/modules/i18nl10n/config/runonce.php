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
 * Class I18nl10nRunonceJob
 */
class I18nl10nRunOnceJob extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }


    // @todo: refactor this
    /**
     * If not set yet set default language and available languages
     */
    public function run()
    {

        $config = \Config::getInstance();

        $i18nl10nDefaultLanguage = $config->get('i18nl10n_default_language') ? : 'en';

        $objDatabase = \Database::getInstance();


        // if not default try to get from root or use fallback
        if (!$config->get('i18nl10n_default_language'))
        {

            $sql = "SELECT language FROM tl_page WHERE type = 'root' ORDER BY sorting";

            $objRootPage = $objDatabase
                ->prepare($sql)
                ->limit(1)
                ->execute();

            if ($objRootPage->row())
            {
                $i18nl10nDefaultLanguage = $objRootPage->language;
            }

            $config->add
                (
                    "\$GLOBALS['TL_CONFIG']['i18nl10n_default_language']",
                    $i18nl10nDefaultLanguage
                );
        }

        // if no available languages, set at least default
        if (!$config->get('i18nl10n_languages'))
        {
            $config->add
                (
                    "\$GLOBALS['TL_CONFIG']['i18nl10n_languages']",
                    serialize(array($i18nl10nDefaultLanguage))
                );
        } // if available languages, check if default needs to be added
        else
        {
            $defaultLanguage = $i18nl10nDefaultLanguage;
            $availableLanguages = deserialize(\Config::get('i18nl10n_languages'));

            if (!in_array($defaultLanguage, $availableLanguages))
            {
                $availableLanguages[] = $defaultLanguage;

                $config->update("\$GLOBALS['TL_CONFIG']['i18nl10n_languages']", serialize($availableLanguages));
            }
        }

        // Remove orphaned entries from tl_page_118nl10n
        $objDatabase
            ->prepare('DELETE FROM tl_page_i18nl10n
                       WHERE NOT EXISTS (
                          SELECT *
                          FROM tl_page as p
                          WHERE tl_page_i18nl10n.pid = p.id)')
            ->execute();

    }
}

$objI18nl10nRunOnceJob = new I18nl10nRunOnceJob();
$objI18nl10nRunOnceJob->run();
