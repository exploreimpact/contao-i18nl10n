<?php
/**
 * Created by PhpStorm.
 * User: atreju
 * Date: 04.08.14
 * Time: 20:45
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
