<?php

//----------------------------------------------------------------------------------------
// express hits as annotations, we need sufficient details to locate them on page
function annotations_from_hits($uri, $page, $hits)
{
	$EXTEND_BY = 3;
	
	$annotations = array();
	foreach ($hits as $hit)
	{
		print_r($hit);
	
		// what word does hit start in?
		$starting_word = $page->characters[$hit->start];
	
		// offset with respect to the starting word 
		$starting_position = $hit->start - $page->word_start[$starting_word] - 1;
	
		// what word does hit end in?
		$ending_word = $page->characters[$hit->end];

		// offset  with respect to the ending word 
		$ending_position = $hit->end - $page->word_start[$ending_word];
	
		echo $starting_word . ' ' . $starting_position . "\n";
		echo $ending_word . ' ' . $ending_position . "\n";

		$prefix_words = array();
		if ($starting_word > 0)
		{
			$prefix_pos = max(0, $starting_word - $EXTEND_BY);
			$prefix_count = $starting_word - $prefix_pos;
		
			$prefix_words = array_slice($page->words, $prefix_pos, $prefix_count);
		
			$prefix = substr($page->words[$starting_word], 0, $starting_position);
			if ($prefix != '')
			{
				$prefix_words[] = $prefix;
			}
		}

		$num_words = count($page->words);
	
		$suffix_words = array();
		if ($ending_word < $num_words - 1)
		{
			$suffix_pos = min($ending_word + $EXTEND_BY, $num_words);
			$suffix_count = $suffix_pos - $ending_word;
	
			$suffix_words = array_slice($page->words, $ending_word + 1, $suffix_count);
		
			$suffix = substr($page->words[$ending_word], $ending_position);
			if ($suffix != '')
			{
				array_unshift($suffix_words, $suffix);
			}	
		}
	
		$a = new Annotation($uri);

		$a->add_permissions("acct:rdmpage@hypothes.is");
	
		$a->add_text_quote(
			str_replace(" ", "\n", $hit->mid),
			join("\n", $prefix_words),
			join("\n", $suffix_words)
			);
	
		// Not sure that we need this, and doesn't seme to be working
		/*
		$a->add_range(
			"//div[@id='" . ($starting_word + 1) . "']",
			$starting_position,
			"//div[@id='" . ($ending_word + 1) . "']",
			$ending_position
			);
		*/
	
		$a->add_tag($hit->type);
		
		if (isset($hit->text))
		{
			$a->set_text($hit->text);
		}
	
		$annotations[] = $a;
	

	}
	
	return $annotations;
}

?>