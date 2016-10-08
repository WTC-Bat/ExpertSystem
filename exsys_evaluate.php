<?php

require_once("Rule.class.php");
require_once("exsys_funcs.php");

/*
**	Simple Implementation!!!
**
**	'$requirement' should be a single fact without any compounding.
**
**	Returns the state (TRUE or FALSE) of the specified requirement.
**	'$requirement' can be obtained from the Rule->getRequirement()
**	function after splitting and checking for other operators in
**	the requirement
*/
function req_state(array $facts, $requirement)
{
	foreach ($facts as $fact)
	{
		if ((key($fact)) == $requirement)
		{
			if ($fact[key($fact)] == 1)
				return (TRUE);
			else
				return (FALSE);
		}
	}
	return (FALSE);
}

/*
**	Simple Implementation!!!
**
**	Will evaluate a query and return TRUE or FALSE depending on recurring
**	evaluation of the requirements of the 'Rule' the query (inference)
**	belongs to.
**
**	This is a 'simple' implementation and will only work on the simplest
**	of rule sets (ie. ./tests/simple_test).
**
**	Fact compounding (|, +, !, ^) will be ignored.
**	Only one Fact from both the left hand side and right hand side will be
**	accounted for.
*/
function evaluate_simple($query, array $facts, array $rules)
{
	$state = initial_state($query, $facts);
	$req;

	foreach ($rules as $rule)
	{
		if (($rule->getInference()) == $query)
		{
			$req = $rule->getRequirement();
			if ((req_state($facts, $req)) == TRUE)
				return (TRUE);
			else
				return (evaluate_simple($req, $facts, $rules));
		}
	}
	return ($state);
}

//further_evaluation:
//	Checks if 'requirement' is negated ('!'):
//		For negation, without any other compunding, instead of return 'TRUE'
//		if the requirement is true, the inference will need to be switched.
//		If the inference was initially 'TRUE' it will become 'FALSE' and
//		vice versa.
//	Checks if either 'inference' (rhs) or 'requirement' are compunded:
//		|	-	OR
//		+	-	AND
//		^	-	XOR
//	Check for conflicts
//	Check for undetermined Facts

/*****************************************************************************/

//?
// function fill_negfacts($req)
// {
// 	$negfacts = array();
// 	$nfact = array();
// 	for ($cnt = 0; $cnt < strlen($req); $cnt++)
// 	{
// 		$char = substr($req, $cnt, 1);
// 		if (ctype_upper($char) === TRUE)
// 		{
// 			if (substr($req, $cnt - 1, 1) === '!')
// 				$nfact = array($char => TRUE);
// 			else
// 				$nfact = array($char => FALSE);
// 			if (count($negfacts) === 0)
// 				$negfacts[0] = $nfact;
// 			else
// 				array_push($negfacts, $nfact);
// 		}
// 	}
// 	if (count($negfacts) === 0)
// 		return (NULL);
// 	else
// 		return ($negfacts);
// }
//
// //?
// function fill_rfacts($req, $facts)
// {
// 	$rfacts = array();
// 	$rfact = array();
// 	for ($cnt = 0; $cnt < strlen($req); $cnt++)
// 	{
// 		$char = substr($req, $cnt, 1);
// 		if (ctype_upper($char) === TRUE)
// 		{
// 			foreach ($facts as $fact)
// 			{
// 				if (key($fact) === $char)
// 					$rfact = array($char, $fact[key($fact)]);
// 				if (count($rfacts) === 0)
// 					$rfacts[0] = $rfact;
// 				else
// 					array_push($rfacts, $rfact);
// 			}
// 		}
// 	}
// 	if (count($rfacts) === 0)
// 		return (NULL);
// 	else
// 		return ($rfacts);
// }

/*
**	Returns the initial state ("TRUE" or "FALSE") of the query as a string.
**
**	'array $facts' will be searched for an occurence of '$query'
**	and the state returned. The initial states in 'array $facts' are
**	initialised when the array is created.
**	see: exsys_funcs.php->get_initial_facts()
**
**	The original version returned TRUE or FALSE as boolean values
*/
function initial_state($query, array $facts)
{
	foreach ($facts as $fact)
	{
		if ((key($fact)) == $query)
		{
			if ($fact[key($fact)] == 1)
				// return (TRUE);
				return ("TRUE");
			else
				// return (FALSE);
				return ("FALSE");
		}
	}
	// return (FALSE);
	return ("FALSE");
}

/*
**	Returns an array of all the facts in the requirement (left hand side), in
**	an array with the fact being the key and the facts initial state (string)
**	as the value.
*/
function rfact_array($req, array $facts)
{
	$rfacts = array();
	$rfact;

	for ($cnt = 0; $cnt < strlen($req); $cnt++)
	{
		$char = substr($req, $cnt, 1);
		if (ctype_upper($char) === TRUE)
		{
			$rfact = array($char => initial_state($char, $facts));
			if (count($rfacts) === 0)
				$rfacts[0] = $rfact;
			else
				array_push($rfacts, $rfact);
		}
	}
	if (count($rfacts) === 0)
		return (NULL);
	else
		return ($rfacts);
}

/*
**	Returns the key (position) of the char '$fact' in the array '$rfacts'
*/
function get_rfacts_key(array $rfacts, $fact)
{
	$key = 0;

	foreach ($rfacts as $rfact)
	{
		if (key($rfact) === $fact)
			return ($key);
		$key++;
	}
}

function negation_test(array $rfacts)
{
	$rkey = get_rfacts_key($rfacts, $char);
	if ($rfacts[$rkey][$char] === "FALSE")
	{
		if ((evaluate($char, $facts, $rules)) == "TRUE")
		{
			return (FALSE);
		}
	}
	else
	{
		return (FALSE);
	}
	return (TRUE);
}

/*
**
*/
function req_evaluation($req, array $facts, array $rules)
{
	//holds the requirments and whether or not they use negation
	//// $negfacts = fill_negfacts($req);
	//holds the requirements and their inital state
	//// $rfacts = fill_rfacts($req, $facts);

	//--check for OR, XOR, AND, and ()
	//--if false, evaluate

	// $state;
	// $facteval = array();
	$rfacts = rfact_array($req, $facts);
	// print_r($rfacts);

	//CHECK FOR PARENTHESES

	for ($cnt = 0; $cnt < strlen($req); $cnt++)
	{
		$char = substr($req, $cnt, 1);
		if (ctype_upper($char) === TRUE)
		{
			//check if the current requirement uses negation (needs to be
			//false?). If it does, and the requirement is true, then return
			//FALSE
			//this assumes that negation on the right hand side means
			//the fact needs to be false for the rule to evaluate to true
			if (substr($req, $cnt - 1, 1) === '!')
				if (negation_test($rfacts, $char) === FALSE)
					return ("FALSE");	//not yet, need to check for XOR
			//check that we aren't on the last char of '$req'
			// THIS WILL BE TOO LATE FOR OR AND XOR!!!
			if ($cnt < (strlen($req) - 1))
			{
				//check if the next char is an OR, AND, or XOR
				switch (substr($req, $cnt + 1, 1))
				{
					case "|":
						break;
					case "&":
						break;
					case "^":
						break;
				}
			}
		}
	}
}

/*
**	This version of evaluate() will return a string indicating "TRUE", "FALSE",
**	"UNDETERMINED" or "CONFLICT"
**
**	Conflicts may have to be searched for prior to full evaluation?
*/
function evaluate($query, array $facts, array $rules)
{
	//get the initial state of the query
	$state = initial_state($query, $facts);
	//will hold the value of 'Rule->getInference()'
	$inf;
	//will hold the value of 'Rule->getRequirement()'
	$req;

	//iterate through 'Rule' objects and find the one/s in which the query
	//is the inference (right hand side)
	foreach ($rules as $rule)
	{
		$inf = $rule->getInference();
		//will hold the position of the $query if found in $inf
		$qpos;
		//will hold the result of the evaluation of the reqiurement
		$reval;

		if ((strpos($inf, "|")) !== FALSE && (strpos($inf, "^")) !== FALSE)
			return ("UNDETERMINED");	//Could maybe search for more
										//instances of '$query' in the rules
										//if an initial 'undetermined' is
										//found. However, that may be
										//erroneus
		//check if '$inf' contains the '$query'
		if (($qpos = strpos($inf, $query)) !== FALSE)
		{
			// print("QPOS" . PHP_EOL);
			$req = $rule->getRequirement();
			///!!
			$reval = req_evaluation($req, $facts);	//should return a string state
			if ($reval === "FALSE")	//?	//still needs to account for negation
				return ($reval);
			//check if the inference of the query uses negation
			// if ($inf[$qpos - 1] == '!')
			// {
			//
			// }
		}

	}
	return ($state);
}

?>
