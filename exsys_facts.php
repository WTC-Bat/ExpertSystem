<?php

function clean_line($str)
{
	$compos = strpos($str, "#");
	$nstr = substr($str, 0, $compos);
	return (preg_replace("/\s+/", "", $nstr));
}

// function get_rules($filename)
// {
// 	$rules = array();
// }

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
	fclose($file);
	if (count($facts) == 0)
		return (null);
	else
		return ($facts);
}

?>
