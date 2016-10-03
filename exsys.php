#!/usr/bin/php
<?php

require_once("exsys_facts.php");

$reschars = array("+", "!", "^", "|", "=", "<", ">", "?", "(", ")", "#");
$facts = array();
$ifacts = array();
$rules;
$queries;
$infile;

if ($argc == 1)
	exsys_error("ERROR: No input file specified.");
$infile = $argv[1];
if (!file_exists($infile))
	exsys_error("ERROR: File \"" . $infile . "\" not found. Check that it exists.");
if (filesize($infile) == 0)
	exsys_error("ERROR: File \"" . $infile . "\" appears to be empty.");
if (($queries = get_queries($infile)) == null)
	exsys_error("ERROR: No queries specified.");
// print_queries($queries);
if (($ifacts = get_initial_facts($infile)) == null)
	echo("WARNING: No facts are initially true!" . PHP_EOL);
if (($facts = get_facts($infile, $ifacts)) == null)
	exsys_error("ERROR: No facts found in file.");
// print_facts($facts);


function exsys_error($message)
{
	echo($message . PHP_EOL);
	exit(1);
}


/*TEST FUNCS*/
function print_facts(array $facts)
{
	foreach ($facts as $f)
	{
		print(key($f) . " = " . $f[key($f)] . PHP_EOL);
	}
}

function print_queries(array $queries)
{
	print("Queries:" . PHP_EOL);
	foreach ($queries as $q)
	{
		print($q . PHP_EOL);
	}
}

function print_ifacts(array $ifacts)
{
	print("Initial Facts:" . PHP_EOL);
	foreach ($ifacts as $if)
	{
		print($if . PHP_EOL);
	}
}

?>
