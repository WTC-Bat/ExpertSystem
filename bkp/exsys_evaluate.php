<?php

require_once("Rule.class.php");

//?
function fact_state($requirement, array $facts)
{
	//foreach ($facts as $key => $value)
	foreach ($facts as $fact)
	{
		if ($requirement === (key($fact)));
		{
			print("REQ: " . $requirement . PHP_EOL);
			print("FACT: " . key($fact) . PHP_EOL);
			print($fact[key($fact)] . PHP_EOL);
			if ($fact[key($fact)] == 1)
			{
				// print("IS TRUE");
				return (TRUE);
			}
		}
	}
	return (FALSE);
}

function initial_state($query, array $facts)
{
	$state;
	foreach ($facts as $fact)
	{
		if ((key($fact)) == $query)
		{
			$state = $fact[key($fact)];
			if ($state == 1)
				return (TRUE);
			else
				return (FALSE);
		}
	}
	return (FALSE);
}

//?
function change_state(array $facts, $state, $query)
{
	foreach ($facts as $fact)
	{
		if ((key($fact)) == $query)
		{
			$fact[key($fact)] = $state;
		}
	}
}

function req_state(array $facts, $requirement, $inference)
{
	$state;
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
**	This is a 'naive' implementation and will only work on the simplest
**	of rule sets (ie. ./tests/simple_test)
*/
// function evaluate($query, array $facts, array $rules)
// {
// 	//initial state of query;?
// 	$state;
//
// 	foreach ($rules as $rule)
// 	{
// 		if ((strstr($rule->getInference(), $query)) != FALSE)
// 		// if ((strpos($rule->getInference(), $query)) != FALSE)
// 		{
// 			//check state of requirement (SINGLE FACT ONLY)
// 			$req = $rule->getRequirement();
// 			if ((fact_state($req, $facts)) == TRUE)
// 			{
// 				// print("IS TRUE");
// 				return (TRUE);
// 			}
// 			else
// 			{
// 				//???
// 				return (evaluate($rule->getRequirement(), $facts, $rules));
// 			}
// 		}
// 	}
// 	return (FALSE);
// }

function evaluate($query, array $facts, array $rules)
{
	$state = initial_state($query, $facts);
	$inf;
	$req;

	foreach ($rules as $rule)
	{
		$inf = $rule->getInference();
		if ($inf == $query)
		{
			$req = $rule->getRequirement();
			if ((req_state($facts, $req, $inf)) == TRUE)
			{
				return (TRUE);
			}
			else
			{
				return (evaluate($req, $facts, $rules));
			}
		}
	}
	return ($state);

}

?>
