<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Date timezone--------------------------------------------------------------------------
date_default_timezone_set('UTC');
mb_internal_encoding("UTF-8");

// Proxy settings for connecting to the web----------------------------------------------- 
// Set these if you access the web through a proxy server. 
$config['proxy_name'] 	= '';
$config['proxy_port'] 	= '';

//$config['proxy_name'] 	= 'wwwcache.gla.ac.uk';
//$config['proxy_port'] 	= '8080';

// Image source---------------------------------------------------------------------------
//$config['image_source']		= 'biostor'; // bhl to use remote images, biostor for local
$config['image_source']		= 'bhl'; // bhl to use remote images, biostor for local

// Hypothesis usernam and password--------------------------------------------------------
$config['hypothesis_username'] = 'rdmpage';
$config['hypothesis_password'] = 'peacrab';

	
?>