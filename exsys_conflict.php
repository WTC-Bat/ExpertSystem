<?php

/*
**	Runs through the gamut of conflict tests
**	Each test will have it's own message and will exit if an error is found
*/
function check_for_conflicts($rule, array $rules)
{
	//	A <=> B
	//	C  => B
	if_and_only_if($rule, $rules);

	//	A => B
	//	A => !B
	//...

	//	A	=> !A
	//	!B	=> B
	same_fact($rule);

	//	-> And this?
	//		-> A	=> B
	//		-> !A	=> B
}

/*
**	Checks for a 'Rule' that contains '<=>' as an 'operator' and, if found,
**	checks that no other 'Rules' try change the fact. If a 'Rule' other than
**	the one containing '<=>' tries to change the fact in quesion, a message will
**	be printed and the program will exit.
*/
function if_and_only_if($rule, array $rules)
{
	$inf = $rule->getInference();
	$len = strlen($inf);
	$err = FALSE;
	$rerr = null;

	foreach ($rules as $r)
	{
		if ($r != $rule)
		{
			for ($cnt = 0; $cnt < strlen($len); $cnt++)
			{
				$char = substr($inf, $cnt, 1);
				if (ctype_upper($char) === TRUE)
				{
					if (strpos($r->getInference(), $char) !== FALSE)
					{
						if ($rule->getIOperator() === "<=>")
						{
							$rerr = $rule;
							$err = TRUE;
						}
						else if ($r->getIOperator() === "<=>")
						{
							$rerr = $r;
							$err = TRUE;
						}
						if ($err === TRUE)
						{
							$msg = sprintf("ERROR: \"%s\" can only be " .
											"modified by the rule " .
											"\"%s\"\n\t-> %s",
											$rerr->getInference(),
											$rerr->getRequirement(),
											$rerr);
							conflict_error($msg);
						}
					}
				}
			}
		}
		// if ($r != $rule)
		// {
		// }
	}
}
// function if_and_only_if(array $rules)
// {
// 	$req;
// 	$op;
// 	$inf;
//
// 	foreach ($rules as $rule)
// 	{
// 		if ($rule->getIOperator() === "<=>")
// 		{
// 			foreach ($rules as $rule2)
// 			{
// 				if (($rule != $rule2) &&
// 					($rule->getInference() === $rule2->getInference()))
// 				{
// 					$msg = sprintf("ERROR: \"%s\" can only be modified by the ".
// 									"rule \"%s\"\n\t-> %s",
// 									$rule->getInference(),
// 									$rule->getRequirement(),
// 									$rule);
// 					conflict_error($msg);
// 				}
// 			}
// 		}
// 	}
// }

/*
**	Checks that there are no 'Rules' that have the same fact in the
**	'requirement' (lhs) and in the 'inference' (rhs.) If there is, an error
**	message will be shown and the program will exit.
*/
function same_fact($rule)
{
	$inf;
	$req;

	$inf = $rule->getInference();
	$req = $rule->getRequirement();
	for ($cnt = 0; $cnt < strlen($inf); $cnt++)
	{
		$char = substr($inf, $cnt, 1);
		if (ctype_upper($char) === TRUE)
		{
			if (strpos($req, $char) !== FALSE)
			{
				$msg = sprintf("ERROR: A fact cannot be used to change ".
								"it's own state.\n\t-> %s", $rule);
				conflict_error($msg);
			}
		}
	}
}
// function same_fact(array $rules)
// {
// 	$inf;
// 	$req;
//
// 	foreach ($rules as $rule)
// 	{
// 		$inf = $rule->getInference();
// 		$req = $rule->getRequirement();
// 		for ($cnt = 0; $cnt < strlen($inf); $cnt++)
// 		{
// 			$char = substr($inf, $cnt, 1);
// 			if (ctype_upper($char) === TRUE)
// 			{
// 				if (strpos($req, $char) !== FALSE)
// 				{
// 					$msg = sprintf("ERROR: A fact cannot be used to change ".
// 									"it's own state.\n\t-> %s", $rule);
// 					conflict_error($msg);
// 				}
// 			}
// 		}
// 	}
// }

function c2($rule, array $rules)
{
	$inf = $rule->getInferece();
	$pos;

	foreach ($rules as $r)
	{
		for ($cnt = 0; $cnt <strlen($inf); $cnt++)
		{
			$char = substr($inf, $cnt, 1);
			if (ctype_upper($char) === TRUE)
			{
				$rinf = $r->getInferece();
				if (($pos = strpos($rinf, $char)) !== FALSE)
				{
					// if ((($cnt > 0) && ($inf[($cnt - 1)] === '!')) ||

					//(($pos > 0) && ($rinf[($pos - 1)]) === '!')
				}
			}
		}
	}
}

function conflict_error($message)
{
	print($message . PHP_EOL);
	exit(1);
}

?>
