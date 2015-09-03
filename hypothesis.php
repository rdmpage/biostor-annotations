<?php

// Reading/writing to the hypothes.is API

require_once(dirname(__FILE__) . '/config.inc.php');

//----------------------------------------------------------------------------------------
/**
 * @brief GET a resource
 *
 * Make the HTTP GET call to retrieve the record pointed to by the URL. 
 *
 * @param url URL of resource
 *
 * @result Contents of resource
 */
function hypothesis_get($url, $userAgent = '', $content_type = '')
{
	
	$data = '';
	
	$ch = curl_init(); 
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION,	1); 
	curl_setopt ($ch, CURLOPT_HEADER,		  1);  

	curl_setopt ($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');	
	
	if ($userAgent != '')
	{
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	}	
		
	if ($content_type != '')
	{
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array ("Accept: " . $content_type));
    }
	
			
	$curl_result = curl_exec ($ch); 
	
	//echo $curl_result;
	
	if (curl_errno ($ch) != 0 )
	{
		echo "CURL error: ", curl_errno ($ch), " ", curl_error($ch);
	}
	else
	{
		$info = curl_getinfo($ch);
		
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    	$header_string = substr($curl_result, 0, $header_size);
    	
    	echo $header_string;
   		 				
		$http_code = $info['http_code'];
		
		//echo "<p><b>HTTP code=$http_code</b></p>";
		
		if ( ($http_code == '200') || ($http_code == '302') || ($http_code == '403'))
		{
			$data = substr($curl_result, $header_size);
		}
	}
	return $data;
}


//----------------------------------------------------------------------------------------
function hypothesis_post($url, $csrf, $post_data, $token = '')
{
	$ch = curl_init(); 
	
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_HEADER,		  1);  
	
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	curl_setopt ($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');	
	curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');	

	// Set HTTP headers
	$headers = array();
	$headers[] = 'Content-type: application/json;charset=UTF-8';
	$headers[] = 'Accept: application/json;charset=UTF-8';
	$headers[] = 'x-csrf-token: ' . $csrf;
	if ($token != '')
	{
		$headers[] = 'X-Annotator-Auth-Token: ' . $token;
	}
	
	// Override Expect: 100-continue header (may cause problems with HTTP proxies
	// http://the-stickman.com/web-development/php-and-curl-disabling-100-continue-header/
	$headers[] = 'Expect:'; 
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
	
	
	curl_setopt($ch, CURLOPT_POST, TRUE);
	if (!empty($post_data)) {
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
	}
	
   	$response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    	
    echo $response;
}	

//----------------------------------------------------------------------------------------
function hypothesis_update($url, $csrf, $post_data, $token = '')
{
	$ch = curl_init(); 
	
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_HEADER,		  1);  
	
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	curl_setopt ($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');	
	curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');	

	// Set HTTP headers
	$headers = array();
	$headers[] = 'Content-type: application/json;charset=UTF-8';
	$headers[] = 'Accept: application/json;charset=UTF-8';
	$headers[] = 'x-csrf-token: ' . $csrf;
	if ($token != '')
	{
		$headers[] = 'X-Annotator-Auth-Token: ' . $token;
	}
	
	// Override Expect: 100-continue header (may cause problems with HTTP proxies
	// http://the-stickman.com/web-development/php-and-curl-disabling-100-continue-header/
	$headers[] = 'Expect:'; 
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
	
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	if (!empty($post_data)) {
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
	}
	
	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	echo $response;
}


//----------------------------------------------------------------------------------------
function hypothesis_delete($url, $csrf, $token )
{
	$ch = curl_init(); 
	
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_HEADER,		  1);  
	
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	curl_setopt ($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');	
	curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');	

	// Set HTTP headers
	$headers = array();
	$headers[] = 'Content-type: application/json;charset=UTF-8';
	$headers[] = 'Accept: application/json;charset=UTF-8';
	//$headers[] = 'x-csrf-token: ' . $csrf;
	$headers[] = 'X-Annotator-Auth-Token: ' . $token;
	
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);	
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	
	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    	
   	echo $response;
}

//----------------------------------------------------------------------------------------
// Annotation class expected by hypothesis
class Annotation
{
	var $data;
	
	//------------------------------------------------------------------------------------
	function __construct($uri)
	{
		$this->data = new stdclass;
		
		$this->data->uri = $uri;
		
		$this->data->document = new stdclass;
		$this->data->tags = array();
		
		$this->data->target = array();

		$target = new stdclass;
		$target->source = $uri;
		$target->selector = array();
				
		$type = new stdclass;
		$type->conformsTo = "https://tools.ietf.org/html/rfc3236";
		$type->type = "FragmentSelector";
		$type->value = "page";
		
		$target->selector[] = $type;
		
		$this->data->target[] = $target;
		
		$this->set_tags(array('api'));

	}
	
	//------------------------------------------------------------------------------------
	function add_permissions($user)
	{
		// Ensure that we have acct prefix
		if (!preg_match('/^acct:/', $user))
		{
			$user = 'acct:' . $user;
		}
		$this->data->user = $user;
		$this->data->permissions = new stdclass;
		$this->data->permissions->read = array("group:__world__");
		$this->data->permissions->update = array($user);
		$this->data->permissions->delete = array($user);
		$this->data->permissions->admin = array($user);	
	}
	
	//------------------------------------------------------------------------------------
	function add_range($startContainer, $startOffset, $endContainer, $endOffset)
	{
		$range = new stdclass;
		$range->type = "RangeSelector";				
		
		$range->startContainer  = $startContainer;
		$range->startOffset  	= $startOffset;
		$range->endContainer  	= $endContainer;
		$range->endOffset  		= $endOffset;
		
		$this->data->target[0]->selector[] = $range;	
	}
	
	//------------------------------------------------------------------------------------
	function add_text_quote($exact, $prefix = '', $suffix = '')
	{
		$quote = new stdclass;
		$quote->type = "TextQuoteSelector";		
		
		$quote->exact = $exact;
		if ($prefix != '')
		{
			$quote->prefix = $prefix;
		}
		if ($suffix != '')
		{
			$quote->suffix = $suffix;
		}
		
		$this->data->target[0]->selector[] = $quote;
	}
	
	//------------------------------------------------------------------------------------
	function add_tag($tag)
	{
		$this->data->tags[] = $tag;
	}
	
	
	//------------------------------------------------------------------------------------
	function set_tags($tags)
	{
		$this->data->tags = $tags;
	}

	//------------------------------------------------------------------------------------
	function set_text($text)
	{
		$this->data->text = $text;
	}
	

}

//----------------------------------------------------------------------------------------
// Enscapsulate API
class HypothesisApi
{
	var $csrf;
	var $token;
	
	//------------------------------------------------------------------------------------
	function __construct($username, $password)
	{
		$url = 'https://hypothes.is/app';

		$json = hypothesis_get($url);
				
		$obj = json_decode($json);

		$this->csrf = $obj->model->csrf;
		
		$this->login($username, $password);
	}

	//------------------------------------------------------------------------------------
	function login()
	{
		global $config;
		
		// Login 
		$post_data = new stdclass;
		$post_data->username = $config['hypothesis_username'];
		$post_data->password = $config['hypothesis_password'];

		hypothesis_post('https://hypothes.is/app?__formid__=login', $this->csrf, $post_data);
		
		// Get token
		$url = 'https://hypothes.is/api/token?assertion=' . $this->csrf;
		$this->token = hypothesis_get($url);

	}

	//-------------------------------------------------------------------------------------
	function add_annotation($annotation)
	{
		hypothesis_post('https://hypothes.is/api/annotations', $this->csrf, $annotation, $this->token);
	}
}



?>