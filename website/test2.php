<?php
$VALUE="Ahoj, jak se mas?"; // the message you want to translate
$APPID="98BAFD350ACBE1FE601ABF6274820CC03BAAC1D4"; // your app ID
$FROM=""; // Initial Language
$TO="en"; // Destination language
$FORMAT = "audio/wav";

//GET LANGUAGE CODE

$FROM=file_get_contents('http://api.microsofttranslator.com/V2/Ajax.svc/Detect?appId='.$APPID.'&text='.urlencode($VALUE).'');

echo $FROM;


//TRANSLATE TEXT

$value=file_get_contents('http://api.microsofttranslator.com/V2/Ajax.svc/Translate?appId='.$APPID.'&from='.$FROM.'&to='.$TO.'&text='.urlencode($VALUE));
echo $value; // Translation printed


//SPEACH OUTPUT
//$soundOutput=http_build_query('http://api.microsofttranslator.com/V2/Ajax.svc/Speak?appId='.$APPID.'&text='.$value.'&language='.$TO.'&format='.$FORMAT);
//echo $soundOutput; // Translation printed




?>

<audio controls="controls">
<source src="http://api.microsofttranslator.com/V2/Http.svc/Speak?appId=98BAFD350ACBE1FE601ABF6274820CC03BAAC1D4&text=What%20is%20You%20name?&language=en&format=audio/wav" />
Your browser does not support this audio
</audio>


                