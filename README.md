# ExpertSystem

### TODO:
	- PROBLEM WITH ARRAY (PARENTHESES)! see ./tests/test_
	- NEGATION
		- Seems good. Needs more testing though
	- CONFLICTS
		- Possible Conflicts: (Or Not?)
			- !A => A:
				- Is this a conflict or is it okay to say "If
					'A' is False, make it True"?
					(Maybe not?)
			-
	- "IF AND ONLY IF" (<=>)...?
	- Check for any other possible "UNDETERMINED" states
	- More tests. Parentheses, XOR, more negation tests,
		more complex patterns


### OLDTODO
	- NEGATION
		- Negation for 'inference' (test)
			- Somewhat done, but a compounded inference has a bit of
				issues. See comment in ./tests/test_neg_inf (FIXED?)
		- Negation for 'requirement' (test)
			- Seems okay, needs more testing. Don't know if it will work with
				compounded requirements and 'evaluate_array()' yet though (FIXED?)
		- NEGATION FOR 'requirement'
