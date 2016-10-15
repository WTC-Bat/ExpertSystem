<?php

require_once("Rule.class.php");

/*
**	Removes ALL whitespace from the specified string ('$str')
*/
function clean_line($str)
{
	$compos = strpos($str, "#");
	$nstr = $str;

	if ($compos != FALSE)
		$nstr = substr($str, 0, $compos);
	return (preg_replace("/\s+/", "", $nstr));
}

/*
**	Returns an array of 'Rule' objects fo all the rules specified in the
**	provided input document.
*/
function get_rules($filename)
{
	$rules = array();
	$rule;
	$file;

	$file = fopen($filename, "r");
	if ($file)
	{
		while (($line = fgets($file)) == TRUE)
		{
			if (($line[0] != '=') && ($line[0] != '?') &&
				($line[0] != '#') && (strlen(trim($line)) != 0))
			{
				$line = clean_line($line);
				$rule = new Rule($line);
				if (count($rules) == 0)
					$rules[0] = $rule;
				else
					array_push($rules, $rule);
			}
		}
	}
	fclose($file);
	if (count($rules) == 0)
		return (null);
	else
		return ($rules);
}

/*
**	Returns an array of all the facts in the provided input document that are
**	initiallt true.
*/
function get_initial_facts($filename)
{
	$file;
	$line;
	$ifacts = array();

	$file = fopen($filename, "r");
	if ($file)
	{
		while (($line = fgets($file)) == TRUE)
		{
			if ($line[0] == '=')
			{
				$line = clean_line($line);
				for ($cnt = 0; $cnt < strlen($line); $cnt++)
				{
					$char = substr($line, $cnt, 1);
					if (ctype_upper($char) == TRUE)
					{
						if (count($ifacts) == 0)
							$ifacts[0] = $char;
						else
							array_push($ifacts, $char);
					}
				}
			}
		}
	}
	fclose($file);
	if (count($ifacts) == 0)
		return (null);
	else
		return ($ifacts);
}

/*
**	Returns an array containing all queries specified in the provided input
**	document.
*/
function get_queries($filename)
{
	$file;
	$line;
	$queries = array();
	$char;

	$file = fopen($filename, "r");
	if ($file)
	{
		while (($line = fgets($file)) == TRUE)
		{
			if ($line[0] == '?')
			{
				$line = clean_line($line);
				for ($cnt = 0; $cnt < strlen($line); $cnt++)
				{
					$char = substr($line, $cnt, 1);
					if (ctype_upper($char) == TRUE)
					{
						if (count($queries) == 0)
							$queries[0] = $char;
						else
							array_push($queries, $char);
					}
				}
			}
		}
	}
	fclose($file);
	if (count($queries) == 0)
		return (null);
	else
		return ($queries);
}

/*
**	Returns an array containing arrays of all facts in the provided input
**	document and their initial state.
*/
function get_facts($filename, $ifacts)
{
	$file;
	$line;
	$facts = array();
	$fact = array();
	$char;
	$state = 0;

	$file = fopen($filename, "r");
	if ($file)
	{
		while (($line = fgets($file)) == TRUE)
		{
			if ($line[0] != '#')
			{
				$line = clean_line($line);
				for ($cnt = 0; $cnt < strlen($line); $cnt++)
				{
					$char = substr($line, $cnt, 1);
					if (ctype_upper($char) == TRUE)
					{
						if (($ifacts != null) && (in_array($char, $ifacts)) == TRUE)
							$state = 1;
						else
							$state = 0;
						$fact = array($char => $state);
						if (in_array($fact, $facts) == FALSE)
						{
							if (count($facts) == 0)
								$facts[0] = $fact;
							else
								array_push($facts, $fact);
						}
					}
				}
			}
		}
	}
	fclose($file);
	if (count($facts) == 0)
		return (null);
	else
		return ($facts);
}

/*
**	Adds the value specified in '$val' to the array specified in 'array $arr'
**	and returns '$arr' without the value added to it
*/
function add_to_array(array $arr, $val)
{
	if (count($arr) == 0)
		$arr[0] = $val;
	else
	array_push($arr, $val);
	return ($arr);
}

/*
**	Returns the string held in '$str' without the character specified in
**	'$exchar'.
**
**	Eg.
**	strwithout("-TE--ST---", '-') == "TEST"
*/
function strwithout($str, $exchar)
{
	$retstr = "";

	for ($cnt = 0; $cnt < strlen($str); $cnt++)
	{
		$char = substr($str, $cnt, 1);
		if ($char !== $exchar)
			$retstr .= $char;
	}
	return ($retstr);
}

/*
**	Returns a string ('$retstr') without the characters between the positions
**	specified in '$start' and '$stop'
**
**	Eg.
**	"I like cheese and crackers"
**	        ^         ^
**	        7         17
**
**	anti_substr("I like cheese and crackers", 7, 17) == "I like crackers"
*/
function anti_substr($str, $start, $stop)
{
	$retstr = "";

	for ($cnt = 0; $cnt < strlen($str); $cnt++)
	{
		$char = substr($str, $cnt, 1);
		if ($cnt < $start || $cnt > $stop)
		{
			$retstr .= $char;
		}
	}
	return ($retstr);
}

/*
**	Returns a string with the value specifed in '$insert' inserted at the
**	position specified by '$pos'
**
**	Eg.
**	$str = "How you?";
**	$ins = str_insert($str, "are ", 4);
**	print($ins . PHP_EOL);
**
**	Output:
**	"How are you?"
*/
function str_insert($str, $insert, $pos)
{
	$retstr = "";

	for ($cnt = 0; $cnt < strlen($str); $cnt++)
	{
		if ($cnt == $pos)
			$retstr .= $insert;
		$retstr .= substr($str, $cnt, 1);
	}
	return ($retstr);
}

/*
**	Returns a string which is the same as '$str' with all characters in
**	'array $chars' removed
**
**	Eg.
**	$str = "I !-!Am00 Here";
**	$str2 = "Extraordinary";
**	$strip = str_strip($str, array("!", "-", "0"));
**	$strip2 = str_strip($str, array("E", "x", "t", "d"));
**	print($strip . PHP_EOL);
**	print($strip2 . PHP_EOL);
**
**	Output:
**	"I Am Here"
**	"raorinary"
*/
function str_strip($str, array $chars)
{
	$retstr = "";

	for ($cnt = 0; $cnt < strlen($str); $cnt++)
	{
		$subchar = substr($str, $cnt, 1);
		if (in_array($subchar, $chars) === FALSE)
			$retstr .= $subchar;
	}
	return ($retstr);
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
}

/*
**	Returns a TRUE (boolean) if '$str' contains one of the characters specified
**	in 'array $chars'
**
**	Eg.
**	$str = "I like cheese";
**	$arr = array("e", "Z", "x", "O");
**	if (str_contains($str, $arr) === TRUE)
**		print("\$str contains one of the characters in \$arr");
**	else
**		print("\$str does not contain one of the characters in \$arr");
**
**	Output:
**	"$str contains one of the characters in $arr"
*/
function str_contains($str, array $chars)
{
	foreach ($chars as $char)
	{
		if (strpos($str, $char) !== FALSE)
			return (TRUE);
	}
	return (FALSE);
}

/*
**	Returns the number of times the character specified in '$char' occurs in
**	the string '$str'
**
**	Eg.
**	$str = "There are but few truths";
**	$cnt1 = char_count($str, "e");
**	$cnt2 = char_count($str, "t");
**	print($cnt1);
**	print($cnt2);
**
**	Output:
**	4
**	3
*/
function char_count($str, $char)
{
	$ccnt = 0;

	for ($cnt = 0; $cnt < strlen($str); $cnt++)
	{
		$ch = substr($str, $cnt, 1);
		if ($ch == $char)
			$ccnt++;
	}
	return ($ccnt);
}

/*
**	Returns the position of '$fact' in '$str'
*/
function find_fact_in_str($fact, $str)
{
	$len = strlen($str);
	for ($cnt = 0; $cnt < $len; $cnt++)
	{
		$char = substr($str, $cnt, 1);
		// if ($char == $fact)
		if ($char === $fact)
		{
			if ($cnt > 0 && $cnt < ($len - 1))
			{
				// if (in_array()) $opchars ?
				if ((ctype_upper($str[($cnt - 1)]) === FALSE) &&
					(ctype_upper($str[$cnt + 1]) === FALSE))
				{
					return ($cnt);
				}
			}
			else if ($cnt === 0)
			{
				if (ctype_upper($str[($cnt + 1)]) === FALSE)
					return ($cnt);
			}
			else if ($cnt === ($len - 1))
			{
				if (ctype_upper($str[($cnt - 1)]) === FALSE)
					return ($cnt);
			}
			//return ($cnt);
		}
	}
	return (FALSE);
}

?>
