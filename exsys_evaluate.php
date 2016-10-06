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
**	Returns the initial state (TRUE or FALSE) of the query.
**
**	'array $facts' will be searched for an occurence of '$query'
**	and the state returned. The initial states in 'array $facts' are
**	initialised when the array is created.
**	see: exsys_funcs.php->get_initial_facts()
*/
function initial_state($query, array $facts)
{
	foreach ($facts as $fact)
	{
		if ((key($fact)) == $query)
		{
			if ($fact[key($fact)] == 1)
				return (TRUE);
			else
				return (FALSE);
		}
	}
	return (FALSE);
}



function evaluate($query, array $facts, array $rules)
{
	//get the initial state of the query
	$istate = initial_state($query, $facts);
	//will hold the value of 'Rule->getInference()'
	$inf;
	//will hold the value of 'Rule->getRequirement()'
	$req;

	//iterate through 'Rule' objects and find the one/s in which the query
	//is the inference (right hand side)
	foreach ($rules as $rule)
	{
		$inf = $rule->getInference();
		// will hold the position of the $query if found in $inf
		$qpos;

		//check if '$inf' contains the '$query'
		if (($qpos = strpos($inf, $query)) != FALSE)
		{
			//check if the inference of the query uses negation
			if ($inf[$qpos - 1] == '!')
			{
				
			}
		}

	}
}

?>
