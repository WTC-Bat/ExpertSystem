<?php

require_once("Rule.class.php");

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
	$inf;
	$req;

	foreach ($rules as $rule)
	{
		if (($rule->getInference()) == $query)
		{
			$req = $rule->getRequirement();
			if ((req_state($facts, $req)) == TRUE)
				return (TRUE);
			else
				return (evaluate($req, $facts, $rules));
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

?>
