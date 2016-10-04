<?php

class Rule
{
	/*Properties*/
	private $_inference;
	private $_ioperator;
	private $_requirement;
	private $_state = FALSE;

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
	}

	/*Destructor*/
	function __destruct()
	{
		//...
	}

	/*Public Functions*/
	public function evaluate()
	{

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

	public function getState()
	{
		return ($this->_state);
	}

	public function printMembers()
	{
		$strstate = "FALSE";
		if ($this->_state == TRUE)
			$strstate = "TRUE";
		print("Inference:\t" . $this->_inference . PHP_EOL);
		print("Operator:\t" . $this->_ioperator . PHP_EOL);
		print("Requirement:\t" . $this->_requirement . PHP_EOL);
		print("State:\t\t" . $strstate . PHP_EOL);
	}

	public function printRule()
	{
		printf("%s %s %s" . PHP_EOL, $this->_inference, $this->_ioperator,
				$this->_requirement);
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
