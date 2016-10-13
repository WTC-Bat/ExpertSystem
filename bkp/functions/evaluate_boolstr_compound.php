<?php

function evaluate_boolstr_compound($str)
{
	$orpos = null;
	$ortype = null;
	$lhs;
	$rhs;
	$lstate = "CHEESE";
	$rstate = "CHEESE";
	$state = "FALSE";
	// $opchars = array("|", "^", "+");

	if ((($orpos = strpos($str, "|")) !== FALSE) ||
		(($orpos = strpos($str, "^")) !== FALSE) ||
		(($orpos = strpos($str, "+")) !== FALSE))
	{
		$ortype = $str[$orpos];
		$lhs = substr($str, 0, $orpos);
		$rhs = substr($str, ($orpos + 1));
		print("LHS: " . $lhs . PHP_EOL);
		print("RHS: " . $rhs . PHP_EOL);
		if (str_contains($lhs, array("|", "^")) === FALSE)
			$lstate = $lhs;
		else
			$lstate = evaluate_boolstr_compound($lhs);
		if (str_contains($rhs, array("|", "^")) === FALSE)
			$rstate = $rhs;
		else
			$rstate = evaluate_boolstr_compound($rhs);
	}
	// print("LSTATE: " . $lstate . PHP_EOL);
	// print("RSTATE: " . $rstate . PHP_EOL);
	if ($ortype === "|")
		$state = eval_OR($lstate, $rstate);
	else if ($ortype === "^")
		$state = eval_XOR($lstate, $rstate);
	else if ($ortype === "+")
		$state = eval_AND($lstate, $rstate);
	return ($state);
}


?>
