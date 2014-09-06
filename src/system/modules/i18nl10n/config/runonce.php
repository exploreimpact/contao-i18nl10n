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


    /**
     * If not set yet set default language and available languages
     */
    public function run()
    {

        $config = \Config::getInstance();

        $i18nl10nDefaultLanguage = \Config::get('i18nl10n_default_language') ? : 'en';


        // if not default try to get from root or use fallback
        if (!\Config::get('i18nl10n_default_language')) {

            $sql = "
                SELECT
                  language
                FROM
                  tl_page
                WHERE
                  type = 'root'
                ORDER BY
                  sorting
            ";

            $objRootPage = \Database::getInstance()
                ->prepare($sql)
                ->limit(1)
                ->execute();

            if ($objRootPage->row()) {
                $i18nl10nDefaultLanguage = $objRootPage->language;
            }

            $config->add
                (
                    "\\Config::get('i18nl10n_default_language')",
                    $i18nl10nDefaultLanguage
                );
        }

        // if no available languages, set at least default
        if (!\Config::get('i18nl10n_languages')) {
            $config->add
                (
                    "\\Config::get('i18nl10n_languages')",
                    serialize(array($i18nl10nDefaultLanguage))
                );
        } // if available languages, check if default needs to be added
        else {
            $defaultLanguage = $i18nl10nDefaultLanguage;
            $availableLanguages = deserialize(\Config::get('i18nl10n_languages'));

            if (!in_array($defaultLanguage, $availableLanguages)) {
                $availableLanguages[] = $defaultLanguage;

                $config->update("\\Config::get('i18nl10n_languages')", serialize($availableLanguages));
            }
        }

    }
}

$objI18nl10nRunOnceJob = new I18nl10nRunOnceJob();
$objI18nl10nRunOnceJob->run();
