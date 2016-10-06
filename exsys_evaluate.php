<?php

require_once("Rule.class.php");

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

function req_evaluation($req)
{
	$has_AND = FALSE;
	$has_OR = FALSE;
	$has_XOR = FALSE;
	$has_NEG = FALSE;
	$has_PREC = FALSE;

	for ($cnt = 0; $cnt < strlen($req); $cnt++)
	{
		$char = substr($req, $cnt, 1);
		// print($char . PHP_EOL);
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
		//if the 'inference' for the query uses negation (!A), this will be
		//set to TRUE
		//// $negation = FALSE;

		//check if '$inf' contains the '$query'
		if (($qpos = strpos($inf, $query)) !== FALSE)
		{
			// print("QPOS" . PHP_EOL);
			if ((strpos($inf, "|")) !== FALSE)	//not working
				return ("UNDETERMINED");	//Could maybe search for more
											//instances of '$query' in the rules
											//if an initial 'undetermined' is
											//found. However, that may be
											//erroneus
			$req = $rule->getRequirement();
			req_evaluation($req);
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
