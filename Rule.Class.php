<?php

class Rule
{
	/*Properties*/
	private $_inference;			//public?
	private $_ioperator;			//public?
	private $_requirements;		//public?
	public $state = FALSE;

	/*Constructor*/
	function __construct($ruleline)
	{
		if (($iopidx = strpos($ruleline, "<=>")) == FALSE)
			if (($iopidx = strpos($ruleline, "=>")) == FALSE)
				throw new Exception("No valid inference operator detected.");
		$iop = $this->getInferenceOperator($iopidx, $ruleline);
		if (($idxiop = strpos($ruleline, $iop)) == FALSE)
			throw new Exception("Inference operator not detected in rule.");
		$this->_inference = substr($ruleline, $idxiop + 1);	//idxiop can sometimes contain characters from the operator
		$this->_ioperator = $iop;
		$this->_requirements = substr($ruleline, 0, $idxiop);
		print("INFERENCE: " . $this->_inference . PHP_EOL);
		print("OPERATOR: " . $this->_ioperator . PHP_EOL);
		print("REQUIRE: " . $this->_requirements . PHP_EOL . PHP_EOL);
	}

	/*Destructor*/
	function __destruct()
	{
		//...
	}

	/*Functions*/
	private function getInferenceOperator($iopidx, $ruleline)
	{
		//NEEDS FIXING
		$opend = strpos($ruleline, '>');
		$iop = substr($ruleline, $iopidx, $opend);
		return ($iop);
	}
}

?>
