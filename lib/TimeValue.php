<?php
/*
 * 
 */
namespace \PHPFinance;

class TimeValue {
	//Functions for regular cash flows
	private static function beginningPaymentsOffset($rate, $isBeginning) {
		return 1 + $rate * $isBeginning?1:0;
	}

	public static function calculateRate($periods, $pv, $pmt, $fv, $isBeginning = true) {
		if ($pmt == 0) return pow(-$fv / $pv, 1 / $periods) - 1;
		else return 0; //TODO
	}

	public static function calculatePeriods($rate, $pv, $pmt, $fv, $isBeginning = true) {
		if ($rate == 0) return -($pv + $fv) / $pmt;
		else return log(($pmt * beginningPaymentsOffset($rate, $isBeginning) - $fv * $rate)/($pmt * beginningPaymentsOffset($rate, $isBeginning) + $pv * rate)/log(1 + $rate);
	}

	public static function calculatePV($rate, $periods, $pmt, $fv, $isBeginning = true) {
		if ($rate == 0) return -($pmt * $periods + $fv);
		else return ($pmt * beginningPAymentsOffset($rate, $isBeginning) / $rate - $fv) * (1 / pow(1 + $rate, $periods)) - ($pmt * beginningPAymentsOffset($rate, $isBeginning) / $rate);
	}

	public static function calculatePMT($rate, $periods, $pv, $fv, $isBeginning = true) {
		if ($rate == 0) return -($pv + $fv) / $periods;
		else return (-$rate / beginningPAymentsOffset($rate, $isBeginning)) * ($pv + ($pv + $fv) / (pow(1 + $rate, $periods) - 1));
	}

	public static function calculateFV($rate, $periods, $pv, $pmt, $isBeginning = true) {
		if ($rate == 0) return -($pv + $pmt * $periods);
		else return ($pmt * beginningPAymentsOffset($rate, $isBeginning) / $rate) - pow(1 + $rate, $periods) * ($pv + $pmt * beginningPAymentsOffset($rate, $isBeginning) / $rate);
	}
}
?>
