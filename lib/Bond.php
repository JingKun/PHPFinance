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
 * Bond class
 * 
 * Computes useful information about a bond given certain starting parameters.
 * 
 */
class Bond {
	private $maturityDate;
	private $startDate;
	private $couponRate;
	private $couponFrequency;
	private $parValue;

	function __construct($maturityDate, $startDate, $couponRate = 5, $couponFrequency = 2, $parValue = 100) {
		$this->maturityDate = $maturityDate;
		$this->startDate = $startDate;
		$this->couponRate = $couponRate;
		$this->couponFrequency = $couponFrequency;
		$this->parValue = $parValue;
	}
}
