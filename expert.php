#!/usr/bin/php
<?php

require_once("exsys_funcs.php");
require_once("exsys_evaluate.php");
require_once("Rule.class.php");

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
if (($ifacts = get_initial_facts($infile)) == null)
	print("WARNING: No facts are initially true!" . PHP_EOL);
if (($facts = get_facts($infile, $ifacts)) == null)
	exsys_error("ERROR: No facts found in file.");
if (($rules = get_rules($infile)) == null)
	print("WARNING: No rules specified in file!" . PHP_EOL);

print(PHP_EOL . "Query Results:" . PHP_EOL);
print("--------------" . PHP_EOL);
foreach ($queries as $query)
{
	$state = evaluate($query, $facts, $rules);
	printf("%s: %s" . PHP_EOL, $query, $state);
}
exit(0);

function exsys_error($message)
{
	print($message . PHP_EOL);
	exit(1);
}

?>
