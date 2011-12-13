<?php
require_once('lib/Bond.php');
use \PHPFinance\Bond as Bond;

class BondTest extends PHPUnit_Framework_TestCase {
	private $testBond;
	
	function __construct() {
		$this->testBond = new Bond(new DateTime('2011-12-31 23:59:59'), new DateTime('2001-01-01 00:00:00'), 5, 2, 100);
	}
}