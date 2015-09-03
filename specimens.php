<?php

//----------------------------------------------------------------------------------------
// find specimens in text
function find_specimens($text)
{
	$hits = array();
	
	$INSTITUTION_CODE 					= '([A-Z]{3,10}(-[A-Z]{1,2})?|QM|BM|BM\(NH\))';
	$CATALOGUE_NUMBER_PREFIX			= '([A-Z][\.|\-]?)?';
	$CATALOGUE_NUMBER					= $CATALOGUE_NUMBER_PREFIX . '[0-9]{2,}';
	$CATALOGUE_NUMBER_SUFFIX_DELIMITER	= '(\-|–|­|—|\.)';
	$CATALOGUE_NUMBER_SUFFIX			= '[0-9]{1,}((\.\d+)+)?';
	
	// Museum specific experiments
	//$INSTITUTION_CODE  = 'CM';

	if (preg_match_all(
		"/
		(?<code>
		$INSTITUTION_CODE
		\s*
		(?<catalogue>$CATALOGUE_NUMBER)
		(
			$CATALOGUE_NUMBER_SUFFIX_DELIMITER
			(?<extension>$CATALOGUE_NUMBER_SUFFIX)			
		)?
		)
		/x",  
	
		$text, $matches, PREG_OFFSET_CAPTURE))
	{
		print_r($matches);
	
		foreach ($matches['code'] as $match)
		{
			$hit = new stdclass;
	
			$hit->mid = $match[0];
	
			$hit->start = $match[1];
			$hit->end = $hit->start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
			
			$hit->type = "specimen";
	
			$hits[] = $hit;
		}
	}
	
	return $hits;

}

//----------------------------------------------------------------------------------------
// find GenBank accessions in text
function find_genbank($text)
{
	$hits = array();
	
	if (preg_match_all(
		"/
		(?<accession>
		[A-Z]
		[A-Z]?
		[0-9]{5,6}
		(?<extension>[\-|–][0-9]{1,})?
		)
		/ux",  	
	
		$text, $matches, PREG_OFFSET_CAPTURE))
	{
		print_r($matches);
	
		foreach ($matches['accession'] as $match)
		{
			$hit = new stdclass;
	
			$hit->mid = $match[0];
	
			$hit->start = $match[1];
			$hit->end = $hit->start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
			
			if (preg_match('/^[A-Z][A-Z]?[0-9]{5,6}$/', $hit->mid))
			{
				$hit->text = '[' . $hit->mid . ']'
				. '('
				. 'http://www.ncbi.nlm.nih.gov/nuccore/' 
				. $hit->mid 
				. ')';
			}

			
			$hit->type = "genbank";
	
			$hits[] = $hit;
		}
	}
	
	return $hits;

}
?>