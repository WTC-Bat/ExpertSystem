<?php

class Rule
{
	/*Properties*/
	public static $verbose = FALSE;
	private $_inference;
	private $_ioperator;
	private $_requirement;

	/*Constructor*/
	function __construct($ruleline)
	{
		if (($iopidx = strpos($ruleline, "<=>")) == FALSE)
			if (($iopidx = strpos($ruleline, "=>")) == FALSE)
				throw new Exception("No valid inference operator detected.");
		$iop = $this->inferenceOperator($iopidx, $ruleline);
		if (($idxiop = strpos($ruleline, $iop)) == FALSE)
			throw new Exception("Inference operator not detected in rule.");
		$this->_inference = substr($ruleline, ($idxiop + strlen($iop)));
		$this->_ioperator = $iop;
		$this->_requirement = substr($ruleline, 0, $idxiop);
		if (self::$verbose == TRUE)
			print($this . " succesfully constructed." .	PHP_EOL);
	}

	/*Destructor*/
	function __destruct()
	{
		if (self::$verbose == TRUE)
			print($this . " destructed." .	PHP_EOL);
	}

	/*Public Functions*/
	public function __toString()
	{
		return (sprintf("Rule ( %s %s %s )", $this->_requirement,
						$this->_ioperator, $this->_inference));
	}

	public function getInference()
	{
		return ($this->_inference);
	}

	public function getIOperator()
	{
		return ($this->_ioperator);
	}

	public function getRequirement()
	{
		return ($this->_requirement);
	}

	public function printMembers()
	{
		print("Inference:\t" . $this->_inference . PHP_EOL);
		print("Operator:\t" . $this->_ioperator . PHP_EOL);
		print("Requirement:\t" . $this->_requirement . PHP_EOL);
	}

	/*Private Functions*/
	private function inferenceOperator($iopidx, $ruleline)
	{
		$opend = strpos($ruleline, '>');
		$iop = substr($ruleline, $iopidx, ($opend - ($iopidx - 1)));

		return ($iop);
	}
}

?>
