<?php
/**
 * User: Сергей Пименов
 * Date: 24.01.12
 * Time: 12:48
 * File: htp.test.php
 */

define("UT_ROOT", dirname(dirname(dirname(__FILE__))) . "/");
include(UT_ROOT . "bootstrap.php");

use FireKit\Helpers\HTP;

$htp = new HTP;

$htp->comment("Unit test for htp package");

$htp->doctype();
$htp->a("/", "test link", array("target"=>"_blank"), array("onclick"=>"return false;"));

$htp->comment("End Unit test for htp package");
?>