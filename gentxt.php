<?php

// usage: "php gentxt.php > outputexample.txt" at same directory of files

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'ClassTxtBanks.php';

$out = ClassTxtBanks::printTxtBanks('banecos','db:tablapagos').PHP_EOL;

