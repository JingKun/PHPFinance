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
 
namespace PHPFinance;

/**
 * TimeValue class
 * 
 * Static class that exposes basic time-value of money calculations.  Positive
 * cash values represent inflows and negative cash values represent outflows.
 */
class TimeValue {
	//Functions for regular cash flows
	private static function beginningPaymentsOffset($rate, $isBeginning) {
		return 1 + $rate * $isBeginning?1:0;
	}

	/**
	 * Calculate Rate function
	 * 
	 * @param float $periods The number of compounding periods for the cash flow
	 * @param float $pv The present value
	 * @param float $pmt The recurring payment value
	 * @param float $fv The future value
	 * @param bool $isBeginning Whether cash flows are assessed at the beginning of a period or at the end
	 * @return float The interest rate per period, as a decimal instead of a percent
	 * @static
	 * @todo Find a good iterative approach to calculating this.
	 */
	public static function calculateRate($periods, $pv, $pmt, $fv, $isBeginning = false) {
		if ($pmt == 0) return pow(-$fv / $pv, 1 / $periods) - 1;
		else return 0; //TODO
	}

	/**
	 * Calculate Periods function
	 * 
	 * @param float $rate The interest rate per period, as a decimal instead of a percent
	 * @param float $pv The present value
	 * @param float $pmt The recurring payment value
	 * @param float $fv The future value
	 * @param bool $isBeginning Whether cash flows are assessed at the beginning of a period or at the end
	 * @return float The number of compounding periods for the cash flow
	 * @static
	 */
	public static function calculatePeriods($rate, $pv, $pmt, $fv, $isBeginning = false) {
		if ($rate == 0) return -($pv + $fv) / $pmt;
		else return log(($pmt * self::beginningPaymentsOffset($rate, $isBeginning) - $fv * $rate)/($pmt * self::beginningPaymentsOffset($rate, $isBeginning) + $pv * rate)/log(1 + $rate);
	}

	/**
	 * Calculate Present Value function
	 * 
	 * @param float $rate The interest rate per period, as a decimal instead of a percent
	 * @param float $periods The number of compounding periods for the cash flow
	 * @param float $pmt The recurring payment value
	 * @param float $fv The future value
	 * @param bool $isBeginning Whether cash flows are assessed at the beginning of a period or at the end
	 * @return float The present value
	 * @static
	 */
	public static function calculatePV($rate, $periods, $pmt, $fv, $isBeginning = false) {
		if ($rate == 0) return -($pmt * $periods + $fv);
		else return ($pmt * self::beginningPaymentsOffset($rate, $isBeginning) / $rate - $fv) * (1 / pow(1 + $rate, $periods)) - ($pmt * self::beginningPaymentsOffset($rate, $isBeginning) / $rate);
	}

	/**
	 * Calculate Payment function
	 * 
	 * @param float $rate The interest rate per period, as a decimal instead of a percent
	 * @param float $periods The number of compounding periods for the cash flow
	 * @param float $pv The present value
	 * @param float $fv The future value
	 * @param bool $isBeginning Whether cash flows are assessed at the beginning of a period or at the end
	 * @return float The recurring payment value
	 * @static
	 */
	public static function calculatePMT($rate, $periods, $pv, $fv, $isBeginning = false) {
		if ($rate == 0) return -($pv + $fv) / $periods;
		else return (-$rate / self::beginningPaymentsOffset($rate, $isBeginning)) * ($pv + ($pv + $fv) / (pow(1 + $rate, $periods) - 1));
	}

	/**
	 * Calculate Future Value function
	 * 
	 * @param float $rate The interest rate per period, as a decimal instead of a percent
	 * @param float $periods The number of compounding periods for the cash flow
	 * @param float $pv The present value
	 * @param float $pmt The recurring payment value
	 * @param bool $isBeginning Whether cash flows are assessed at the beginning of a period or at the end
	 * @return float The future value
	 * @static
	 */
	public static function calculateFV($rate, $periods, $pv, $pmt, $isBeginning = false) {
		if ($rate == 0) return -($pv + $pmt * $periods);
		else return ($pmt * self::beginningPaymentsOffset($rate, $isBeginning) / $rate) - pow(1 + $rate, $periods) * ($pv + $pmt * self::beginningPaymentsOffset($rate, $isBeginning) / $rate);
	}
}