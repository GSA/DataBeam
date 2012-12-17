<?php

/**
 * Modifies a string to remove all non-ASCII characters and spaces.
 * http://snipplr.com/view.php?codeview&id=22741
 */
 function slugify( $text ) {

	// replace non-alphanumeric characters with a hyphen
	$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

	// trim off any trailing or leading hyphens
	$text = trim($text, '-');

	// transliterate from UTF-8 to ASCII
	if (function_exists('iconv')) {
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	}

	// lowercase
	$text = strtolower($text);

	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);


	return $text;
}


function properize($string) {
  return $string.'\''.($string[strlen($string) - 1] != 's' ? 's' : '');
}

?>