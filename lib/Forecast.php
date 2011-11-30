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
 * Forecast Class
 * 
 * Class for analyzing timeseries data and making predictions from it using a
 * variety of models.
 */
class Forecast {
	private $timeSeries;

	/**
	 * Constructor function
	 * 
	 * @param array $data An array of timeseries data
	 */
	public function __construct(array $data) {
		$this->timeSeries = $data;
	}
	
	/*
	 * Exponential Smoothing function
	 * 
	 * Implements exponential smoothing, a more sophisticated variant of the
	 * weighted average.  Every observation is included into the average,
	 * though the weight falls off exponentially for older observations.
	 * 
	 * @param int $periods How many periods to forecast into the future
	 * @param float $alpha The smoothing constant
	 * @return array An array of predicted timeseries data, one prediction for each observation plus $periods predictions
	 */
	public function exponentialSmoothing($periods = 0, $alpha = 0.5) {
		$predictions = array($this->timeSeries[0]); //Seed.
		
		//"Predict" observations
		for ($i = 1; $i < count($this->timeSeries); $i++) {
			$predictions[] = $alpha * $this->timeSeries[$i - 1] + (1 - $alpha) * $predictions[$i - 1];
		}
		
		//Carry it forward into the future
		for ($i = count($this->timeSeries); $i < $periods + count($this->timeSeries); $i++) {
			$predictions[] = $alpha * isset($this->timeSeries[$i - 1])?$this->timeSeries[$i - 1]:$predictions[$i - 1] + (1 - $alpha) * $predictions[$i - 1]; //Yes, this just evaluates to the last prediction
		}
		
		return $predictions;
	}
	
	/**
	 * Double Exponential Smoothing function
	 * 
	 * Implements double exponential smoothing, also known as Holt-Winters
	 * double exponential smoothing.  It is the same as single exponential
	 * smoothing, but with a trend component added in, as single exponential
	 * smoothing will tend to lag behind any trending in the data.
	 * 
	 * @param int $periods How many periods to forecast into the future
	 * @param float $alpha The smoothing constant
	 * @param float $beta The trending constant
	 * @return array An array of predicted timeseries data, one prediction for each observation plus $periods predictions
	 */
	public function doubleExponentialSmoothing($periods = 0, $alpha = 0.5, $beta = 0.5) {
		$predictions = array($this->timeSeries[0]);
		$trend = array(0);
		
		//"Predict" observations
		for ($i = 1; $i < count($this->timeSeries); $i++) {
			$predictions[] = $alpha * $this->timeSeries[$i] + (1 - $alpha) * ($predictions[$i - 1] + $trend[$i - 1]);
			$trend[] = $beta * ($predictions[$i] - $predictions[$i - 1]) + (1 - $beta) * $trend[$i -1];
		}
		
		//Carry it forward into the future
		for ($i = count($this->timeSeries); $i < $periods + count($this->timeSeries); $i++) {
			$predictions[] = $alpha * $predictions[$i] + (1 - $alpha) * ($predictions[$i - 1] + $trend[$i - 1]);
			$trend[] = $beta * ($predictions[$i] - $predictions[$i - 1]) + (1 - $beta) * $trend[$i -1];
		}
		
		return $predictions;
	}
	 
	/**
	 * Triple Exponential Smoothing function
	 * 
	 * Implements triple exponential smoothing, also known as Holt-Winters
	 * additive smoothing.  It is the same as the double exponential smoothing
	 * but with the inclusion of a seasonal component.
	 * 
	 * @param int $periods How many periods to forecast into the future
	 * @param int $seasonLength How many periods are in a full turn of seasons (e.g. 12 periods for monthly data for which the seasonal component repeats annually)
	 * @param float $alpha The smoothing constant
	 * @param float $beta The trending constant
	 * @param float $gamma The seasonal constant
	 * @return array An array of predicted timeseries data, one prediction for each observation plus $periods predictions
	 */
	public function tripleExponentialSmoothing($periods = 0, $seasonLength = 12, $alpha = 0.5, $beta = 0.5, $gamma = 0.5) {
		$predictions = array($this->timeSeries[0]);
		$trend = array(0);
		$seasonal = array(array_fill(0, $seasonLength, 0);
		
		//"Predict" observations
		for ($i = 1; $i < count($this->timeSeries); $i++) {
			$predictions[] = $alpha * $this->timeSeries[$i] / 
		}
		
		//Carry it forward into the future
		for ($i = count($this->timeSeries); $i < $periods + count($this->timeSeries); $i++) {
		}
		
		return $predictions;
	}
}