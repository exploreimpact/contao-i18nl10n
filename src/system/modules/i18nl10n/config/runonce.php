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
class I18nl10nRunonceJob extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }

    public function run()
    {

        if($GLOBALS['TL_CONFIG']['i18nl10n_default_language'])
        {
            return;
        }

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

        $i18nl10n_default_language = \Database::getInstance()
            ->prepare($sql)
            ->limit(1)
            ->execute()
            ->language;

        if(!$i18nl10n_default_language) {
            $i18nl10n_default_language = 'en';
        }

        $config = \Config::getInstance();
        $config->add("\$GLOBALS['TL_CONFIG']['i18nl10n_default_language']", $i18nl10n_default_language);

    }
}

$objI18nl10nRunonceJob = new I18nl10nRunonceJob();
$objI18nl10nRunonceJob->run();
