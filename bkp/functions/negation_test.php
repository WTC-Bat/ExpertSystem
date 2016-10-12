<?php

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

?>
