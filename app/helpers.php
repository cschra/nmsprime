<?php

/**
 * An improved version of laravel's dd() function
 * This will first print some meta information about the caller of dd
 * and then passes all given arguments to the original dd() function
 *
 * Background: I tend to have multiple dd() calls on debugging. Sometimes
 * it is hard to find all of those again ;-)
 *
 * @author Patrick Reichel
 */
function d() {

	// write meta information about the caller
	$td = '<td style="font-size: 11px; font-family:monospace; color:#444">';
	$bt = debug_backtrace();
	echo '<table>';
	echo '<tr>';
	echo $td.'File: </td>';
	echo $td.array_get($bt[0], 'file', 'n/a').', line '.array_get($bt[0], 'line', 'n/a').'</td>';
	echo '</tr>';
	echo '<tr>';
	echo $td.'Method: </td>';
	echo $td.array_get($bt[1], 'class', 'n/a').'::'.array_get($bt[1], 'function', 'n/a').'()</td>';
	echo '</tr>';
	echo '</table>';

	echo '<hr size="1" noshade>';

	// call laravel's dd function and pass all given params
	call_user_func_array('dd', func_get_args());
}

