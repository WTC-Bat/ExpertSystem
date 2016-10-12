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
**	TRUE is returned. If it is true, then FALSE is ruturned.
**
**	If FALSE (true) is returned, the fact will need to be evaluated. However,
**	even if TRUE (false) is returned, the fact should still go through
**	evaluation as their may be a rule that makes (or negates) it false (TRUE)
*/
//?
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
	$rarr = array();	//-!-//Maybe the lhs will hold the requirement while rhs holds any possible operators
						//-!-// -OR- maybe the rhs will be the requirements state. IE. "TRUE", "FALSE"
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
		$r = substr($nreq, ($lpar + 1), ($rpar - ($lpar + 1)));
		$rarr = add_to_array($rarr, $r);
		$nreq = get_nreq($nreq, $lpar, $rpar);
	}
	$nreq = remove_opchars($nreq);
	for ($cnt = 0; $cnt < strlen($nreq); $cnt++)
	{
		$char = substr($nreq, $cnt, 1);
		print("CHAR: " . $char . PHP_EOL);
		if (ctype_upper($char) === TRUE)
		{
			$rarr = add_to_array($rarr, $char);
		}
	}
	return ($rarr);
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
	$rarr = array();

	if (strpos($req, '(') !== FALSE)
	{
		$rarr = split_req($req);	//?
		evaluate_array($rarr, $facts, $rules);
	}
	//!!//CHECK FOR NEGATION!
}

//function evaluate_array($arr, array $facts, array $rules)
function evaluate_array($rarr, array $facts, array $rules)
{
	$results = array();
	$state;

	foreach ($rarr as $r)
	{
		if ((strpos($r, "+") !== FALSE) || (strpos($r, "|") !== FALSE) ||
			(strpos($r, "^") !== FALSE))
		{
			$state = evaluate_compound($r, $facts, $rules);
		}
		else
		{
			$state = evaluate($r, $facts, $rules);
		}
		$res = array($r, $state);
		$results = add_to_array($results, $res);
	}
}

//?
function eval_AND($fact1, $fact2, array $facts, array $rules)
{
	$efact1 = evaluate($fact1, $facts, $rules);
	$efact2 = evaluate($fact2, $facts, $rules);

	print("FACT1: " . $fact1 . PHP_EOL);
	print("FACT2: " . $fact2 . PHP_EOL);

	if ($efact1 === "TRUE" && (strpos($efact1, "!") === TRUE))
		return ("FALSE");
	if ($efact2 === "TRUE" && (strpos($efact2, "!") === TRUE))
		return ("FALSE");
	if ($efact1 === "TRUE" && $efact2 === "TRUE")
		return ("TRUE");
	else
		return ("FALSE");


	// $efact1 = evaluate($fact1, $facts, $rules);
	// $efact2 = evaluate($fact2, $facts, $rules);
	//
	// if ($efact1 === "TRUE" && $efact2 === "TRUE")	//What about negation?
	// 	return (TRUE);
	// else
	// 	return (FALSE);
}

function evaluate_compound($str, array $facts, array $rules)
{
	$char;
	$op;
	$negated = FALSE;
	$slen = strlen($str);
	$opchars = array('+', '|', '^');
	$evaluated = array();
	$state;
	$lhs;
	$rhs;

	for ($cnt = 0; $cnt < $slen; $cnt++)
	{
		$char = substr($str, $cnt, 1);
		if (in_array($char, $opchars) === TRUE)
		{
			// print("IN_ARRAY" . PHP_EOL);
			switch ($char)
			{
				case '+':
					if ($cnt > 1)
						if ($str[($cnt - 2)] === '!')
							 $lhs = "!";
					if ($cnt > 0)
						if (ctype_upper($str[($cnt - 1)]) === TRUE)
							$lhs .= $str[($cnt - 1)];
					if ($cnt < ($slen - 3 ))		///?
					{
						if ($str[($cnt + 1)] === '!')
							$rhs = "!";
						if (ctype_upper($str[($cnt + 2)]) === TRUE)
							$rhs .= $str[($cnt + 2)];
					}
					else if ($cnt < ($slen - 2))
					{
						if (ctype_upper($str[($cnt + 1)]) === TRUE)
							$rhs = $str[($cnt + 2)];
					}
					print("LHS: " . $lhs . PHP_EOL);
					print("RHS: " . $rhs . PHP_EOL);
					if (eval_AND($lhs, $rhs, $facts, $rules) === "FALSE")
						return ("FALSE");
					break;
				case '|':
					break;
				case '^':
					break;
			}
		}
		//Won't work properly if '$str' doesn't have an operator character
	}
}

/*
**	This version of evaluate() will return a string indicating "TRUE", "FALSE",
**	"UNDETERMINED" or "CONFLICT"
**
**	Conflicts may have to be searched for prior to full evaluation?
**
**	HOW WILL I HANDLE CONFLICT CHECKING?!?!?!?!
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
			reval($req, $facts, $rules);	//Need to hold the return from reval()
											//May just put the contents of this function here
		}

	}
	return ($state);
}

?>
