<?php

require_once("Rule.class.php");
require_once("exsys_funcs.php");

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
		//will hold the position of the $query, if found in $inf
		$qpos;
		//will hold the result of the evaluation of the reqiurement
		$reval;

		//check if '$inf' contains the '$query'
		if (($qpos = strpos($inf, $query)) !== FALSE)
		{
			if ((strpos($inf, "|")) !== FALSE && (strpos($inf, "^")) !== FALSE)
				return ("UNDETERMINED");	//Could maybe search for more
											//instances of '$query' in the rules
											//if an initial 'undetermined' is
											//found. However, that may be
											//erroneus
			$req = $rule->getRequirement();
			$state = evaluate_requirement($req, $facts, $rules);
		}

	}
	return ($state);
}

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
**	Evaluates the requirement specified in '$req' using 'array $facts' and
**	'array $rules'
*/
function evaluate_requirement($req, array $facts, array $rules)
{
	//Holds the requirements facts and their initial value
	$rfacts = rfact_array($req, $facts);	//So far unused
	//Holds the facts in the order they need to be evaluated. Facts in
	//parentheses will be first in the array, starting with the first pair of
	//parentheses ending with the last pair and then the fact/s without
	//parentheses. [WILL NEED TO BE MATCHED AGAINST OPERATORS IN '$req']
	$rarr = array();
	$resarr = array();	//??
	$state;

	if (strlen($req) == 1)
	{
		evaluate($req, $facts, $rules);
	}
	else if (strpos($req, '(') !== FALSE)
	{
		$rarr = split_req($req);
		//$state = evaluate_array($rarr, $facts, $rules);
		$resarr = evaluate_array($rarr, $facts, $rules);
		$state = evaluate_results_array($req, $resarr, $facts, $rules);	//also needs requiremnt?
	}
	else
	{
		$state = evaluate_compound($req, $facts, $rules);
	}
	//!!//CHECK FOR NEGATION!	<-	I know 'eval_AND()' does this!!	see: ./bkp/exsys_evaluate3.php
	return ($state);
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
**	Returns an arrray containing the facts in the requirement in the order they
**	need to be evaluated.
**
**	Used for removing the current facts in parentheses from '$nreq'.
**	However, every fact not in parentheses will have it's own element in '$rarr'
**
**	Eg.
**	$req = "A + B | C + (D | E)";
**	$arr = split_req($req);
**	print_r($arr);
**
**	Output:
**	Array
**	(
**		[0] => D|E
**		[1] => A
**		[2] => B
**		[3] => C
**	)
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
		if (($rpar = strpos($nreq, ')')) === FALSE)	//This maybe should be in an error check function
		{
			print("ERROR: Missing closing parentheses." . PHP_EOL);
			exit(1);
		}
		$r = substr($nreq, ($lpar + 1), ($rpar - ($lpar + 1)));
		$rarr = add_to_array($rarr, $r);	//$rarr should contain an operator too
		$nreq = anti_substr($nreq, $lpar, $rpar);
	}
	//?
	$nreq = remove_opchars($nreq);
	for ($cnt = 0; $cnt < strlen($nreq); $cnt++)
	{
		$char = substr($nreq, $cnt, 1);
		if (ctype_upper($char) === TRUE)
		{
			$rarr = add_to_array($rarr, $char);
		}
	}
	print_r($rarr);
	return ($rarr);
}

//PROBLEMS! 'evaluate_array()' SO FAR DOESN'T RETURN EVERYTHING. IT MUST EITHER
//RETURN AN ARRAY CONTAINING EVALUATED FACTS, OR MUST DO EVALUATION IT'S SELF
//AND RETURN THE STATE ('$state')

//The actual evaluation of the array should go in here.
function evaluate_array(array $rarr, array $facts, array $rules)
{
	$results = array();
	// $op;
	$oppos;
	$state;

	foreach ($rarr as $r)
	{
		// $op = "NONE";
		if ((($oppos = strpos($r, "+")) !== FALSE) ||
			(($oppos = strpos($r, "|")) !== FALSE) ||
			(($oppos = strpos($r, "^")) !== FALSE))
		{
			$op = $r[$oppos];
			$state = evaluate_compound($r, $facts, $rules);
		}
		else
		{
			$state = evaluate($r, $facts, $rules);
		}
		$res = array($r, $state);
		// $res = array($r, $state, $op);
		$results = add_to_array($results, $res);
	}
	print_r($results);
	return ($results);
}

function evaluate_results_array($req, array $resarr, array $facts, array $rules) //and $req?
{
	$char;
	$key;

	print("REQ:" . $req . PHP_EOL);
	for ($cnt = 0; $cnt < strlen($req); $cnt++)
	{
		$char = substr($req, $cnt, 1);
		if (ctype_upper($char) === TRUE)
		{
			//CHECK FOR COMPOUNDING		-	and negation
			// $key = array_array_key($resarr, $char);
			// if ($key === null)
			// 	print("KEY IS NULL" . PHP_EOL);
			// else
			// 	print($resarr[$key][0] . PHP_EOL);
		}
	}



	// foreach ($resarr as $res)
	// {
	// 	for ($cnt = 0; $cnt < strlen($res); $cnt++)
	// 	{
	//
	// 	}
	// }
}

function evaluate_compound($str, array $facts, array $rules)
{
	// $char;
	// $op;
	// $negated = FALSE;
	// $slen = strlen($str);
	// $opchars = array('+', '|', '^');
	// $evaluated = array();

	$orpos = null;
	$lhs;
	$rhs;
	$lstate;
	$rstate;
	$state = "FALSE";
	// $negated = FALSE;

	if ((($orpos = strpos($str, "|")) !== FALSE) ||
		(($orpos = strpos($str, "^")) !== FALSE) ||
		(($orpos = strpos($str, "+")) !== FALSE))
	{
		$rhs = substr($str, 0, $orpos);
		$lhs = substr($str, ($orpos + 1));
		if (strlen($lhs) == 1)
			$lstate = evaluate($lhs, $facts, $rules);
		else
			$lstate = evaluate_requirement($lhs, $facts, $rules);
		if (strlen($rhs) == 1)
			$rstate = evaluate($rhs, $facts, $rules);
		else
			$rstate = evaluate_requirement($rhs, $facts, $rules);
		if ($str[$orpos] == "|")
			$state = eval_OR($lstate, $rstate);
		else if ($str[$orpos] == "^")
			$state = eval_XOR($lstate, $rstate);
		else if ($str[$orpos] == "+")
			$state = eval_AND($lstate, $rstate);
	}
	return ($state);
}

function eval_OR($lstate, $rstate)
{
	if ($lstate === "TRUE" || $rstate === "TRUE")
		return ("TRUE");
	else
		return ("FALSE");
}

function eval_XOR($lstate, $rstate)
{
	if (($lstate === "TRUE" && $rstate === "FALSE") ||
		($lstate === "FALSE" && $rstate === "TRUE"))
		return ("TRUE");
	else
		return ("FALSE");
}

//?
//function eval_AND($fact1, $fact2, array $facts, array $rules)
function eval_AND($lstate, $rstate)
{
	if ($lstate == "TRUE" && $rstate == "TRUE")
		return ("TRUE");
	else
		return ("FALSE");
}

/*
**	Returns the key (position) of the char '$fact' in the array '$rfacts'
**
** Only for named keys
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
**	Returns the key of an array within another array.
**
**	array $arr:
**	The array to search through
**
**	$val:
**	The val of the array key '0' that resides in $arr
**
**	Eg.
**	$arr = array(array("key1", "val1"), array("key2", "val2"));
**	$key = array_array_key(arr, "key2");
**	print($key);
**
**	Output:
**	1
*/
function array_array_key(array $arr, $val)
{
	$k = null;

	foreach ($arr as $a)
	{
		if (strpos($a[0], $val) !== FALSE)
		{
			if ($a[0] === $val)
			{
				return ($k);
			}
		}
		$k++;
	}
	return (null);



	// $k = null;
	//
	// foreach ($arr as $a)
	// {
	// 	if ($a[0] === $key)
	// 		return ($k);
	// 	$k++;
	// }
	// return (null);
}

?>
