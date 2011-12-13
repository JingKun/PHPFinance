<?php
require_once('lib/Forecast.php');
use \PHPFinance\Forecast as Forecast;

class ForecastTest extends PHPUnit_Framework_TestCase {
	private $testTimeSeries;
	
	function __construct() {
		$this->testTimeSeries = new Forecast(new Array(4,56,3,3,3,6,7));
	}
}