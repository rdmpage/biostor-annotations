<?php

// Handle extraction and annotation

//----------------------------------------------------------------------------------------
// Convert HTNL to a page object with a text stream
function html_to_page($html)
{
	$page = new stdclass;

	// http://stackoverflow.com/a/2671410/9684
	$html = mb_convert_encoding($html, 'utf-8', mb_detect_encoding($html));
	// if you have not escaped entities use
	$html = mb_convert_encoding($html, 'html-entities', 'utf-8'); 

	$dom = new DOMDocument('1.0', 'UTF-8');

	// http://stackoverflow.com/questions/6090667/php-domdocument-errors-warnings-on-html5-tags
	libxml_use_internal_errors(true);
	$dom->loadHTML($html);
	libxml_clear_errors();

	$xpath = new DOMXPath($dom);
	
	$nodeCollection = $xpath->query ('//div/div');

	$word_count = 0;
	$char_count = 0;
	
	// Individual words
	$page->words = array();

	// For each character in text stream, record number of the corresponding word
	// -1 indicates space between words
	$page->characters = array();
	
	// For each word, record position of first character of word relative to 
	// text stream for page
	$page->word_start = array();

	foreach ($nodeCollection as $node)
	{
		$word = $node->firstChild->nodeValue;
	
		$page->words[$word_count] = $word;
	
		$page->word_start[$word_count] = $char_count;
	
		$word_chars = str_split($word);
	
		if ($word_count > 0)
		{
			$page->characters[$char_count++] = '-1';
		}
	
		foreach ($word_chars as $char)
		{
			$page->characters[$char_count++] = $word_count;
		}
	
		$word_count++;
	}

	// Craete text stream for page by joining all the words together
	$page->text = join(' ', $page->words);

	
	return $page;
}


?>