<?php

/*
**	Runs through the gamut of conflict tests
**	Each test will have it's own message and will exit if an error is found
**
**	Maybe the program shouldn't necessarily exit on conflicts?!
*/
function check_for_conflicts($rule, array $rules)
{
	//	A <=> B
	//	C  => B
	if_and_only_if($rule, $rules);

	//	A => B
	//	A => !B
	same_req_diff_infstate($rule, $rules);

	//	A	=> !A
	//	!B	=> B
	same_fact($rule);

	//	-> And this?	-Not working
	//same_infstate_diff_reqstate
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
	}
}

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

/*
**	Check for 'Rule' that contains the same 'requirement' as '$rule' and the
**	the same 'inference', but the inference in the checked 'Rule' is the
**	opposite as the fact in the inferenec of '$rule'
*/
function same_req_diff_infstate($rule, array $rules)
{
	$inf = $rule->getInference();
	$pos;
	$rerr;
	$err = FALSE;

	foreach ($rules as $r)
	{
		for ($cnt = 0; $cnt <strlen($inf); $cnt++)
		{
			$char = substr($inf, $cnt, 1);
			if (ctype_upper($char) === TRUE)
			{
				$rinf = $r->getInference();
				if (($pos = strpos($rinf, $char)) !== FALSE)
				{
					if ($r->getRequirement() == $rule->getRequirement())
					{
						if ($cnt > 0)
						{
							if ($inf[($cnt - 1)] === '!')
							{
								if ($pos > 0)
								{
									if ($rinf[($pos - 1)] !== '!')
									{
										$err = TRUE;
									}
								}
								else
								{
									$err = TRUE;
								}
							}
							else if ($inf[($cnt - 1)] !== '!')
							{
								if ($pos > 0)
								{
									if ($rinf[($pos - 1)] === '!')
									{
										$err = TRUE;
									}
								}
							}
						}
						else
						{
							if ($pos > 0)
							{
								if ($rinf[($pos - 1)] === '!')
								{
									$err = TRUE;
								}
							}
						}
						if ($err === TRUE)
						{
							$msg = sprintf("ERROR: The same requirement " .
											"cannot be used the change the " .
											"same fact to a differenct state".
											"\n\t-> %s\n\t-> %s",
											$rule,
											$r);
							conflict_error($msg);
						}
					}
				}
			}
		}
	}
}

function same_infstate_diff_reqstate($rule, array $rules)
{
	$inf = $rule->getInference();
	$req = $rule->getRequirement();
	$err = FALSE;

	foreach ($rules as $r)
	{
		if ($r != $rule)
		{
			if ($r->getInference() === $inf)
			{
				$rreq = $r->getRequirement();
				for ($cnt = 0; $cnt < strlen($inf); $cnt++)
				{
					$char = substr($inf, $cnt, 1);
					if (ctype_upper($char) === TRUE)
					{
						for ($cnt2 = 0; $cnt2 < strlen($rreq); $cnt++)
						{
							$char2 = substr($rreq, $cnt2, 1);
							if ($char2 === $char)
							{
								if ($cnt2 > 0)
								{
									if ($cnt > 0)
									{
										if ($rreq[($cnt2 - 1)] === '!' &&
											$inf[($cnt - 1)] !== '!')
										{
											$err = TRUE;
										}
										else if ($rreq[($cnt2 - 1)] !== '!' &&
													$inf[($cnt - 1)] === '!')
										{
											$err = TRUE;
										}
									}
									else
									{
										if ($rreq[($cnt2 - 1)] === '!')
										{
											$err = TRUE;
										}
									}
								}
								else
								{
									if ($inf[($cnt - 1)] === '!')
									{
										$err = TRUE;
									}
								}
								if ($err === TRUE)
								{
									$msg = sprintf("ERROR: Cannot have a " .
											"negated version of requirement " .
											"change the same fact\n\t-> %s" .
											"\n\t-> %s",
											$req,
											$rreq);
									conflict_error($msg);

								}
							}
						}
					}
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
