<?php

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
