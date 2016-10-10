#!/usr/bin/php
<?php

require_once("exsys_funcs.php");
require_once("exsys_evaluate.php");
require_once("Rule.class.php");

require_once("exsys_dbg.php");

// $reschars = array("+", "!", "^", "|", "=", "<", ">", "?", "(", ")", "#");
$facts = array();
$ifacts = array();
$queries = array();
$rules = array();
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
// print_ifacts($ifacts);
if (($facts = get_facts($infile, $ifacts)) == null)
	exsys_error("ERROR: No facts found in file.");
// print_facts($facts);
if (($rules = get_rules($infile)) == null)
	echo("WARNING: No rules specified in file!" . PHP_EOL);
// print_rules($rules);
// foreach ($queries as $query)
// {
// 	$state = "FALSE";
//
// 	if ((evaluate_simple($query, $facts, $rules)) == TRUE)
// 		$state = "TRUE";
// 	printf("%s: %s" . PHP_EOL, $query, $state);
// }
foreach ($queries as $query)
{
	// $state = "FALSE";
	//
	// if ((evaluate($query, $facts, $rules)) == "TRUE")
	// 	$state = "TRUE";
	$state = evaluate($query, $facts, $rules);
	printf("%s: %s" . PHP_EOL, $query, $state);
}
exit(0);

function exsys_error($message)
{
	echo($message . PHP_EOL);
	exit(1);
}

?>
