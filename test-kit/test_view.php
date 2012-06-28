<?php
include('../bootstrap.php');

use \FireKit\View\SimpleView;

$view = new SimpleView("");
$tpl = $view->Display(
    "test.phtml"
);

var_dump($tpl);
?>
