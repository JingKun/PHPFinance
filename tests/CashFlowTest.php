<?php
require_once('lib/CashFlow.php');
use \PHPFinance\CashFlow as CashFlow;

class CashFlowTest extends PHPUnit_Framework_TestCase {
	private $testCashFlow;
	
	function __construct() {
		$this->testCashFlow = new CashFlow(new Array(1000, 0, 0, -245), 0.08);
	}
}