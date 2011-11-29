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
	 * Calculate NPV function
	 * 
	 * Calculates the Net Present Value of a series of cash flows given a rate.
	 * 
	 * @param array $cashFlows An array of cash flows
	 * @param float $rate A decimal representation of the discount rate
	 * @return float The net present value
	 * @static
	 */
	public static function calculateNPV(array $cashFlows, $rate) {
		$NPV = 0;
		
		foreach ($this->cashFlows as $k => $flow) {
			$NPV += TimeValue::calculatePV($this->rate, $k, 0, $flow, false);
		}
		
		return $NPV;
	}
	
	/**
	 * Calculate Internal Rate of Return function
	 * 
	 * Calculates the required discount rate to have an NPV of zero on a series
	 * of cash flows.  By the nature of the problem, it is possible to have more
	 * than one rate that gives an NPV of zero, or possibly none.  In general,
	 * each sign change in the cash flows will correspond to a potential
	 * solution.  Thus, if there is one sign change, there will be one
	 * solution.  If there is more than one change, there may be multiple
	 * solutions.  If there are no sign changes, then there will be no solution
	 * and the result will diverge to infinity, so the function will return INF
	 * 
	 * @param array $cashFlows An array of cash flows
	 * @param float $guess Closest guess to the true IRR
	 * @return float The internal rate of return, as a decimal
	 * @static
	 */
	public static function calculateIRR(array $cashFlows, $guess = 0.08) {
		//Source: http://en.wikipedia.org/wiki/Internal_rate_of_return#Numerical_solution
		
		//Initial guesses to seed the approximation
		$IRRn_1 = 0;
		$IRR = $guess;
		$NPVn_1 = TimeValue::calculateNPV($cashFlows, $IRRn_1);
		
		//Start the iterations
		while (abs($NPVn) > 0.000001) {
			$NPVn = TimeValue::calculateNPV($cashFlows, $IRR);
			$IRR1 = $IRR - $NPVn*(($IRR - $IRRn_1)/($NPVn - $NPVn_1));
			
			//Shift values for next iteration
			$IRRn_1 = $IRR;
			$IRR = $IRR1;
			$NPVn_1 = $NPVn;
			
			//Check to see if we're diverging toward infinity (100,000% is considered absurdly high, and therefore a mark of divergence)
			if ($IRR > 1000) {
				$IRR = INF;
				break;
			}
		}
		
		return $IRR;
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
	 * @todo Implement beginning term payments
	 */
	public static function calculateRate($periods, $pv, $pmt, $fv, $isBeginning = false) {
		if ($pmt == 0) return pow(-$fv / $pv, 1 / $periods) - 1;
		else {
			$cashFlows = array($pv);
			for ($i = 0; $i < $periods; $i++) $cashFlows[] = $pmt;
			$cashFlows[] = $fv
		
			return self::calculateIRR($cashFlows, 0.1);
		}
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