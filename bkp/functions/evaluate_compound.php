<?php

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

	//CHECK IF CONTAINS OR FIRST??

	// print("STR: " . $str . PHP_EOL);

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
					//if ($cnt < ($slen - 3))		///?
					if ($cnt < ($slen - 2))		///?
					{
						if ($str[($cnt + 1)] === '!')
							$rhs = "!";
						if (ctype_upper($str[($cnt + 2)]) === TRUE)
							$rhs .= $str[($cnt + 2)];
					}
					//else if ($cnt < ($slen - 2))
					else if ($cnt < ($slen - 1))
					{
						if (ctype_upper($str[($cnt + 1)]) === TRUE)
							$rhs = $str[($cnt + 1)];
					}
					// print("LHS: " . $lhs . PHP_EOL);
					// print("RHS: " . $rhs . PHP_EOL);
					$state = eval_AND($lhs, $rhs, $facts, $rules);
					if ($state === "FALSE")
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
	return ($state);
}

?>
