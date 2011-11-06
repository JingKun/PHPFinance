<?php
/**
 * PHP Finance Library
 *
 * Copyright (C) 2011-2012 Michael Cordingley <mcordingley@gmail.com>
 * 
 * This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Library General Public License as published
 * by the Free Software Foundation; either version 3 of the License, or 
 * (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Library General Public
 * License for more details.
 * 
 * You should have received a copy of the GNU Library General Public License
 * along with this library; if not, write to the Free Software Foundation, 
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * 
 * LGPL Version 3
 *
 * @package PHPFinance
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
