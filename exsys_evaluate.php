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

/*
**	Checks if the fact (that uses negation) is indeed false. If it is false,
**	TRUE is returned. If it is true, then FALSE is reuturned.
**
**	If FALSE (true) is returned, the fact will need to be evaluated. However,
**	even if TRUE (false) is returned, the fact should still go through
**	evaluation as their may be a rule that makes (or negates) it false (TRUE)
*/
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
**	Returns a string ('$nreq') without the characters between the positions
**	specified in '$lpar' and '$rpar'
**
**	Eg.
**	"I like cheese and crackers"
**	        ^         ^
**	        7         17
**
**	get_nreq("I like cheese and crackers", 7, 17) == "I like crackers"
**
**	!!:
**	Returns with the excess operators from the requirement
**	before it was split (+, |, &, ^). Use 'strwithout()' to clean
**
**	Could be named something else and put in "exsys_funcs.php"!!!
*/
function get_nreq($req, $lpar, $rpar)
{
	$nreq = "";

	for ($cnt = 0; $cnt < strlen($req); $cnt++)
	{
		$char = substr($req, $cnt, 1);
		if ($cnt < $lpar || $cnt > $rpar)
		{
			$nreq .= $char;
		}
	}
	return ($nreq);
}

/*
**	Returns an arrray containing the facts in the requirement in the order they
**	need to be evaluated.
**
**	used for removing the current facts in parentheses from '$nreq'.
**	Essentially removes "parenthesized" facts from $nreq
*/
function split_req($req)
{
	$evals = array();	//-!-//Maybe the lhs will hold the requirement while rhs holds any possible operators
	$nreq = $req;
	$lpar;
	$rpar;

	while (($lpar = strpos($nreq, '(')) !== FALSE)
	{
		if (($rpar = strpos($nreq, ')')) === FALSE)
		{
			print("ERROR: Missing closing parentheses." . PHP_EOL);
			exit(1);
		}
		$eval = substr($nreq, ($lpar + 1), ($rpar - ($lpar + 1)));
		$evals = add_to_array($evals, $eval);
		$nreq = get_nreq($nreq, $lpar, $rpar);
	}
	$evals = add_to_array($evals, strwithout($nreq, '+'));
	return ($evals);
}

/*
**	Evaluates the requirement specified in '$req' using 'array $facts' and
**	'array $rules'
*/
function reval($req, array $facts, array $rules)
{
	//Holds the requirements facts and their initial value
	$rfacts = rfact_array($req, $facts);
	//Holds the facts in the order they need to be evaluated. Facts in
	//parentheses will be first in the array, starting with the first pair of
	//parentheses ending with the last pair and then the fact/s without
	//parentheses. [WILL NEED TO BE MATCHED AGAINST OPERATORS IN '$req']
	$evals = array();

	if (strpos($req, '(') !== FALSE)
	{
		$evals = split_req($req);
		print_r($evals);
	}
	//!!//CHECK FOR NEGATION!
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
			$req = $rule->getRequirement();
			reval($req, $facts, $rules);
			///!!
			// $reval = req_evaluation($req, $facts, $rules);	//should return a string state
			///!!

			if ($reval === "FALSE")	//?	//still needs to account for negation
				return ($reval);
			//!!//CHECK FOR NEGATION!
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
