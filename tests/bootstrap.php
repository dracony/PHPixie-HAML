<?php
if(!defined('INIT')) {	
	define('ROOT',dirname(dirname(dirname(dirname(dirname(__FILE__))))));
	$loader = require_once(ROOT.'/vendor/autoload.php');
	$loader->add('PHPixie', ROOT.'/vendor/phpixie/core/classes/');
	$loader->add('PHPixie', ROOT.'/vendor/phpixie/db/classes/');
	$loader->add('PHPixie', ROOT.'/vendor/phpixie/orm/classes/');
	$loader->add('PHPixie', ROOT.'/vendor/phpixie/haml/classes/');
	$loader->add('MtHaml',ROOT.'/vendor/mthaml/mthaml/lib/');
	define('INIT', true);
}
	