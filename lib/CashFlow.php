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
 * CashFlow class
 * 
 * Class that represents an irregular series of cash flows.
 */
class CashFlow {
	protected $cashFlows;
	protected $rate
	
	protected $payback;
	protected $discountedPayback;
	protected $NPV;
	protected $IRR;
	protected $MIRR;
	
	/**
	 * Constructor function
	 * 
	 * @param array $cashFlows Array representing a series of cash flows
	 */
	public function __construct(array $cashFlows, $rate = 0.08) {
		$this->cashFlows = $cashFlows;
		$this->rate = $rate;
		
		$this->invalidateCalculations();
	}
	
	//Clear out all cached calculations.
	private function invalidateCalculations() {
		$this->payback = null;
		$this->discountedPayback = null;
		$this->NPV = null;
		$this->IRR = null;
		$this->MIRR = null;
	}
	
	//Returns 1 on positive and -1 on negative.
	private function getSign($number) {
		return ($number == abs($number))1?-1;
	}
	
	//Getters/Setters
	
	/**
	 * Add Cash Flow function
	 * 
	 * Adds a new cash flow to the end of the series.
	 * 
	 * @param float $amount The new cash flow to add
	 */
	public function addCashFlow($amount) {
		$this->cashFlows[] = $amount;
		
		$this->invalidateCalculations();
	}
	
	/**
	 * Get Cash Flow function
	 * 
	 * Returns a cash flow at the selected index
	 * 
	 * @param int Index of the cash flow to return
	 * @return float The amount of the cash flow
	 */
	public function getCashFlow($index) {
		return $this->cashFlows[$index];
	}
	
	//Calculated Values
	
	/**
	 * Get Payback function
	 * 
	 * Returns the payback period of the provided cash flows.  That is, the
	 * number of periods that need to elapse until the net of cash flows
	 * changes sign.
	 * 
	 * @return float The number of periods until the first cash flow has been paid back
	 */
	public function getPayback() {
		if ($this->payback == null) {
			$flows = $this->cashFlows;
			
			$net = array_shift($flows);
			$sign = $this->getSign($net);
			$payback = 0;
			
			foreach ($flows as $flow) {
				if ($sign == $this->getSign($net + $flow)) {
					//If sign's unchanged, just add the next cf on.
					$net += $flow;
					$payback++;
				}
				else {
					//We've found the end of our payback.  Calculate the fractional part and exit.
					$payback += -$net / $flow;
					break;
				}
			}
			
			$this->payback = $payback;
		}
		
		return $this->payback;
	}
	
	/**
	 * Get Discounted Payback function
	 * 
	 * Returns the discounted payback period of the provided cash flows.  That
	 * is, the number of periods that need to elapse until the net of 
	 * discounted cash flows changes sign.
	 * 
	 * @return float The number of periods until the first cash flow has been paid back
	 */
	public function getDiscountedPayback() {
		if ($this->discountedPayback == null) {
			$flows = $this->cashFlows;
			
			$net = array_shift($flows);
			$sign = $this->getSign($net);
			$payback = 0;
			
			foreach ($flows as $flow) {
				$discountedFlow = TimeValue::calculatePV($this->rate, $payback + 1, 0, $flow, false);
				if ($sign == $this->getSign($net + $discountedFlow)) {
					//If sign's unchanged, just add the next cf on.
					$net += $discountedFlow;
					$payback++;
				}
				else {
					//We've found the end of our payback.  Calculate the fractional part and exit.
					$payback += -$net / $discountedFlow;
					break;
				}
			}
			
			$this->discountedPayback = $payback;
		}
		
		return $this->discountedPayback;
	}
	
	/**
	 * Get Net Present Value function
	 * 
	 * Returns the net present value of the series of cash flows.
	 * 
	 * @return float The NPV of the CashFlow object
	 */
	public function getNPV() {
		if ($this->NPV == null) {
			$this->NPV = TimeValue::calculateNPV($this->cashFlows, $this->rate);
		}
		
		return $this->NPV;
	}
	
	/**
	 * Get Internal Rate of Return function
	 * 
	 * Returns the internal rate of return for the supplied series of cash
	 * flows.
	 * 
	 * @return float The IRR, as a decimal
	 */
	public function getIRR() {
		//Source: http://en.wikipedia.org/wiki/Internal_rate_of_return#Numerical_solution
		if ($this->IRR == null) {
			//Initial guesses to seed the approximation
			$IRRn_1 = 0;
			$IRR = $this->rate;
			$NPVn_1 = TimeValue::calculateNPV($this->cashFlows, $IRRn_1);
			$NPVn = TimeValue::calculateNPV($this->cashFlows, $IRR);
			
			//Start the iterations
			while (abs($NPVn) > 0.000001) {
				$NPVn = TimeValue::calculateNPV($this->cashFlows, $IRR);
				$IRR1 = $IRR - $NPVn*(($IRR - $IRRn_1)/($NPVn - $NPVn_1));
				
				//Shift values for next iteration
				$IRRn_1 = $IRR;
				$IRR = $IRR1;
				$NPVn_1 = $NPVn;
				
				//Check to see if we're diverging toward infinity (100,000% is considered absurdly high, and therefore a mark of divergence)
				if ($IRR > 1000) {
					$this->IRR = INF;
					break;
				}
			}
			
			$this->IRR = $IRR;
		}
		
		return $this->IRR;
	}
	
	/**
	 * Get Modified Internal Rate of Return function
	 * 
	 * Returns the modified internal rate of return for the supplied series of
	 * cash flows.
	 * 
	 * @return float The MIRR, as a decimal
	 */
	public function getMIRR() {
		if ($this->MIRR == null) {
			//Aggregate the NPV of the flows with the same sign as the starting flow
			//and aggregate the NFV of the flows with the opposite sign.
			$sign = $this->getSign($this->cashFlows[0]);
			$periods = count($this->cashFlows) - 1;
			
			$startingFlows = 0;
			$endingFlows = 0;
			
			foreach ($this->cashFlows as $k => $flow) {
				if ($this->getSign($flow) == $sign) $startingFlows += TimeValue::calculatePV($this->rate, $k, 0, $flow, false);
				else $endingFlows += TimeValue::calculateFV($this->rate, $periods - $k, $flow, 0, false);
			}
			
			//Calculate rate of the aggregated amounts.
			$this->MIRR = TimeValue::calculateRate($periods, $startingFlows, 0, $endingFlows, false);
		}
		
		return $this->MIRR;
	}
}