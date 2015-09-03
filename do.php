<?php

require_once (dirname(__FILE__) . '/lib.php');

require_once(dirname(__FILE__) . '/page.php');
require_once(dirname(__FILE__) . '/hypothesis.php');
require_once(dirname(__FILE__) . '/annotations.php');
require_once(dirname(__FILE__) . '/points.php');
require_once(dirname(__FILE__) . '/specimens.php');

// Hypothesis
$h = new HypothesisApi('rdmpage', 'peacrab');

$id = 145822;

$id = 145828; // Selection of a neotype for Apteryx mantelli Bartlett, 1852, with the support of genetic data

$id = 147608;

$id = 106413;

$url = 'http://localhost/~rpage/biostor/api.php?id=biostor/' . $id;

$json = get($url);

//echo $json;

$reference = json_decode($json);

$page_count = 1;
foreach ($reference->bhl_pages as $k => $PageID)
{
	// URI for page so we can locate annotations
	$uri = 'http://biostor.org/reference/' . $id . '/page/' . $page_count;
	
	// fetch page
	
	$url = 'http://localhost/~rpage/biostor/api.php?page=' . $PageID . '&format=html';
	
	$json = get($url);
	
	//echo $json;
	
	$obj = json_decode($json);
	
	if (isset($obj->html))
	{
		
		$page = html_to_page($obj->html);
		
		$hits = array();
		
		if (0)
		{
			$hits = array_merge($hits, find_specimens($page->text));
		}
				
		if (1)
		{
			$hits = array_merge($hits, find_points($page->text));
		}

		if (0)
		{
			$hits = array_merge($hits, find_genbank($page->text));
		}
		
		

		$annotations = annotations_from_hits($uri, $page, $hits);
		
		echo "annotations\n";
		print_r($annotations);
		
		foreach ($annotations as $annotation)
		{
			echo json_encode($annotation->data);
			$h->add_annotation($annotation->data);
			echo "\n";
		}		
				
	}

	$page_count++;
}


?>

