<?php

// Extract point localities from text


//----------------------------------------------------------------------------------------
/**
 * @brief Convert a decimal latitude or longitude to deg° min' sec'' format in HTML
 *
 * @param decimal Latitude or longitude as a decimal number
 *
 * @return Degree format
 */
function decimal_to_degrees($decimal)
{
	$decimal = abs($decimal);
	$degrees = floor($decimal);
	$minutes = floor(60 * ($decimal - $degrees));
	$seconds = round(60 * (60 * ($decimal - $degrees) - $minutes));
	
	if ($seconds == 60)
	{
		$minutes++;
		$seconds = 0;
	}
	
	// &#176;
	$result = $degrees . '&deg;' . $minutes . '&rsquo;';
	if ($seconds != 0)
	{
		$result .= $seconds . '&rdquo;';
	}
	return $result;
}

//----------------------------------------------------------------------------------------
/**
 * @brief Convert decimal latitude, longitude pair to deg° min' sec'' format in HTML
 *
 * @param latitude Latitude as a decimal number
 * @param longitude Longitude as a decimal number
 *
 * @return Degree format
 */
function format_decimal_latlon($latitude, $longitude)
{
	$html = decimal_to_degrees($latitude);
	$html .= ($latitude < 0.0 ? 'S' : 'N');
	$html .= '&nbsp;';
	$html .= decimal_to_degrees($longitude);
	$html .= ($longitude < 0.0 ? 'W' : 'E');
	return $html;
}

//----------------------------------------------------------------------------------------
/**
 * @brief Convert degrees, minutes, seconds to a decimal value
 *
 * @param degrees Degrees
 * @param minutes Minutes
 * @param seconds Seconds
 * @param hemisphere Hemisphere (optional)
 *
 * @result Decimal coordinates
 */
function degrees2decimal($degrees, $minutes=0, $seconds=0, $hemisphere='N')
{
	$result = $degrees;
	$result += $minutes/60.0;
	$result += $seconds/3600.0;
	
	if ($hemisphere == 'S')
	{
		$result *= -1.0;
	}
	if ($hemisphere == 'W')
	{
		$result *= -1.0;
	}
	// Spanish
	if ($hemisphere == 'O')
	{
		$result *= -1.0;
	}
	// Spainish OCR error
	if ($hemisphere == '0')
	{
		$result *= -1.0;
	}
	
	return $result;
}

//----------------------------------------------------------------------------------------
// Convert string matches to decimal lat,long values
function toPoint($matches)
{
	$point = array();
			
	$degrees = $minutes = $seconds = 0;		
		
	if (isset($matches['latitude_seconds']))
	{
		$seconds = $matches['latitude_seconds'];
	}
	$minutes = $matches['latitude_minutes'];
	$degrees = $matches['latitude_degrees'];
	
	$point['latitude'] = degrees2decimal($degrees, $minutes, $seconds, $matches['latitude_hemisphere']);

	$degrees = $minutes = $seconds = 0;	
	
	if (isset($matches['longitude_seconds']))
	{
		$seconds = $matches['longitude_seconds'];
	}
	$minutes = $matches['longitude_minutes'];
	$degrees = $matches['longitude_degrees'];
	
	$point['longitude'] = degrees2decimal($degrees, $minutes, $seconds, $matches['longitude_hemisphere']);

	return $point;
}

//----------------------------------------------------------------------------------------
// Series of regular expressions to extract point localities from text
function find_points($text)
{
	$DEGREES_SYMBOL 		=  '[°|º]';
	$MINUTES_SYMBOL			= '(\'|’)';
	$SECONDS_SYMBOL			= '("|\'\'|’’|”)';
	
	$INTEGER				= '\d+';
	$FLOAT					= '\d+(\.\d+)?';
	
	$LATITUDE_DEGREES 		= '[0-9]{1,2}';
	$LONGITUDE_DEGREES 		= '[0-9]{1,3}';
	
	$LATITUDE_HEMISPHERE 	= '[N|S]';
	$LONGITUDE_HEMISPHERE 	= '[W|E]';

	$hits = array();
	
	if (preg_match_all("/
		(?<latlon>
		(?<latitude_degrees>$LATITUDE_DEGREES)
		$DEGREES_SYMBOL
		\s*
		(?<latitude_minutes>$FLOAT)
		$MINUTES_SYMBOL?
		\s*
		(
		(?<latitude_seconds>$FLOAT)
		$SECONDS_SYMBOL
		)?
		\s*
		(?<latitude_hemisphere>$LATITUDE_HEMISPHERE)
		,?
		(\s+-)?
		;?
		\s*
		(?<longitude_degrees>$LONGITUDE_DEGREES)
		$DEGREES_SYMBOL
		\s*
		(?<longitude_minutes>$FLOAT)
		$MINUTES_SYMBOL?
		\s*
		(
		(?<longitude_seconds>$FLOAT)
		$SECONDS_SYMBOL
		)?
		\s*
		(?<longitude_hemisphere>$LONGITUDE_HEMISPHERE)
		)
		
	/xu",  $text, $matches, PREG_OFFSET_CAPTURE))
	{
		print_r($matches);
		
		$n = count($matches[0]);
		for ($i = 0; $i < $n; $i++)
		{
			$hit = new stdclass;
	
			$hit->mid = $matches['latlon'][$i][0];
	
			$hit->start = $matches['latlon'][$i][1];
			$hit->end = $hit->start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
			
			// convert to pair of decimal (lat,long) coordinates
			
			$keys = array(
				'latitude_degrees',
				'latitude_minutes',
				'latitude_seconds',
				'latitude_hemisphere',
				'longitude_degrees',
				'longitude_minutes',
				'longitude_seconds',
				'longitude_hemisphere'
				);
				
			$m = array();
			foreach ($keys as $k)
			{
				$m[$k] = $matches[$k][$i][0];
			}
			
			$point = toPoint($m);
			$hit->text = $point['latitude'] . ',' . $point['longitude'];
			
	
			$hit->text .= " [Open Street Map]"
				. '('
				. 'http://www.openstreetmap.org/?mlat=' 
				. $point['latitude'] 
				. '&mlon=' 
				. $point['longitude']
				. '&zoom=4'
				. ')';

			$hit->text .= " [Google Maps]"
				. '('
				. 'http://maps.google.com/?q=' 
				. $point['latitude'] 
				. ',' 
				. $point['longitude']
				. '&ll='
				. $point['latitude'] 
				. ',' 
				. $point['longitude']				
				. '&z=6'
				. ')';

			$hit->text .= "\n![Map]"
				. '('
				. 'https://maps.googleapis.com/maps/api/staticmap?' 
				. 'size=200x200'
				. '&maptype=terrain'
				. '&markers='
				. $point['latitude'] 
				. ',' 
				. $point['longitude']
				. '&zoom=6'
				. ')';
		
			$hit->type = "latlon";
			$hits[] = $hit;
		}
	}
	
	//print_r($hits);	
	return $hits;
}

?>