<?php

/*
Copyright (C) 2007  Richard Ishida ishida@w3.org

Derived from PHP code and CSS styling by Thomas Gruner icspace.org tom.gruner@gmail.com

Script displays or allows you to search the language codes from the iana, readable for people.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
http://www.gnu.org/licenses/gpl.html

*/

?>


<?php
define('none', 0);
define('check', 1);
define('find', 2);
define('lookup', 3);
define('language', 4);
define('extlang', 5);
define('script', 6);
define('region', 7);
define('variant', 8);
define('grand', 9);
define('redundant', 10);

$searchtype = none;
$searchtext = '';
$searchtags = '';
$errormsg = '';
$warnings = '';
$info = '';


?>

<?php

####################  READ IN DATA #######################################

# Reads in the subtag registry and sorts all the entries into arrays, one for language tags, one for region tags, etc.

$languages 		= array();
$scripts   		= array();
$regions    	= array();
$grandfathered 	= array();
$redundant   	= array();
$variant    	= array();
$extlang    	= array();


#$registry = file_get_contents('http://www.iana.org/assignments/language-subtag-registry');
$registry = file_get_contents('registry.txt');
	
	
$registryItems = explode('%%', $registry);
$regLength = count($registryItems);
	
foreach ($registryItems as $item) {
	$itemLines = explode("\n", $item);
	$item 	   = array();
	foreach($itemLines as $line) {
		if(substr($line,0,2) == '  ') {
			$item[$property] .= ' ' . trim($line);
			}
		elseif (strpos($line, ':') !== false) {
			list($property, $value) = explode(':', $line);
			if(!isset($item[$property])) {
				$item[$property] = trim($value);
				}
			else {
				$item[$property] .= ', ' . trim($value);
				}
			}
		}
		
	if(isset($item['Type'])) {
		switch($item['Type']) {
			case 'language' 		: $languages[] 		= $item; break;
			case 'script'   		: $scripts[]   		= $item; break;
			case 'region'   		: $regions[]   		= $item; break;
			case 'grandfathered' 	: $grandfathered[]	= $item; break;
			case 'redundant'	    : $redundant[]		= $item; break;
			case 'variant'          : $variant[]		= $item; break;
			case 'extlang'          : $extlang[]		= $item; break;
				
			default                 : echo "Unkown Type: " . $item['Type'] . "</br />";
			}
		}
	}

#change the Tag field in Grandfathered and Redundant tags to Subtag, just to make lookup easier
for ($i=0; $i<count($grandfathered); $i++) {
	$grandfathered[$i]['Subtag'] = $grandfathered[$i]['Tag'];
	}
for ($i=0; $i<count($redundant); $i++) {
	$redundant[$i]['Subtag'] = $redundant[$i]['Tag'];
	}
	
// create an array to hold information about macrolanguages
$macrolanguages = array();
foreach ($languages as $subtag) {
	if (isset($subtag['Macrolanguage'])) {
		$macrolanguages[$subtag['Macrolanguage']][] = $subtag['Subtag'];
		}
	}
#print_r($macrolanguages);

/*
$filename = 'languages.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $languages='.var_export($languages, true).'?>' );

$filename = 'extlang.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $extlang='.var_export($extlang, true).'?>' );

$filename = 'scripts.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $scripts='.var_export($scripts, true).'?>' );

$filename = 'regions.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $regions='.var_export($regions, true).'?>' );

$filename = 'grandfathered.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $grandfathered='.var_export($grandfathered, true).'?>' );

$filename = 'redundant.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $redundant='.var_export($redundant, true).'?>' );

$filename = 'variant.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $variant='.var_export($variant, true).'?>' );

$filename = 'macrolanguages.php';
$fp = fopen( $filename, 'w' );
fwrite( $fp, '<?php $macrolanguages='.var_export($macrolanguages, true).'?>' );
*/

$out = "var languages = [";
for ($r=0;$r<count($languages);$r++) {
	$out .= "{";
	$first = true;
	foreach ($languages[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'languages.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var extlang = [";
for ($r=0;$r<count($extlang);$r++) {
	$out .= "{";
	$first = true;
	foreach ($extlang[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'extlang.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var scripts = [";
for ($r=0;$r<count($scripts);$r++) {
	$out .= "{";
	$first = true;
	foreach ($scripts[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'scripts.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var regions = [";
for ($r=0;$r<count($regions);$r++) {
	$out .= "{";
	$first = true;
	foreach ($regions[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'regions.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var grandfathered = [";
for ($r=0;$r<count($grandfathered);$r++) {
	$out .= "{";
	$first = true;
	foreach ($grandfathered[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'grandfathered.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var variant = [";
for ($r=0;$r<count($variant);$r++) {
	$out .= "{";
	$first = true;
	foreach ($variant[$r] as $key => $value) {
		if ($first == false) { $out .= ','; } else { $first = false; } 
		$thekey = strtolower($key);
		$thekey = str_replace('-','',$thekey);
		$thevalue = str_replace('"',"'", $value);
		$out .= $thekey.':"'.$thevalue.'"';
		}
	$out .= "},\n";
	}
$out .= "]";
$filename = 'variant.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


$out = "var macrolanguages = {\n";
$first = true;
foreach ($macrolanguages as $key => $value) {
	if ($first == false) { $out .= ",\n"; } else { $first = false; } 
	$thekey = strtolower($key);
	$out .= $thekey.':[';
	$firstarray = true;
	for ($i=0;$i<count($value);$i++) {
		if ($firstarray == false) { $out .= ','; } else { $firstarray = false; }
		$out .= "'".$value[$i]."'";
		}
	$out .= "]";
	}
$out .= "}";
$filename = 'macrolanguages.js';
$fp = fopen( $filename, 'w' );
fwrite( $fp, $out );


?>

<html>
<body>
<p>Registry length: <? echo $regLength ?></p>
</body>
</html>
