#!/usr/bin/php
<?php

require_once("exsys_facts.php");
require_once("Rule.Class.php");

//temp
require_once("exsys_dbg.php");

$reschars = array("+", "!", "^", "|", "=", "<", ">", "?", "(", ")", "#");
$facts = array();
$ifacts = array();
$queries = array();
$infile;
$rules = array();

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
	echo("WARNING: No rules specified in file!");

function exsys_error($message)
{
	echo($message . PHP_EOL);
	exit(1);
}

?>
