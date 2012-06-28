<?php
include('../bootstrap.php');


define('GOOGLE_MAGIC', 0xE6359A60);

use \FireKit\Seo\SeoRoutines;
$domain = "hostmaster.ua";
echo "Test SeoRoutines\r\n";
echo "Domain: $domain\r\n";

echo "\r\n";
echo "Google PR: ";
echo SeoRoutines::GooglePR($domain);

echo "Yandex Cy: ";
echo SeoRoutines::YandexCy($domain);

echo "\r\n";
echo "Alexa Rank: ";
echo SeoRoutines::AlexaRank($domain);
?>
