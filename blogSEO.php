<?php

//PHP script for finding important event information within blog posts. The script adds this information to the database for easier access in other activities. In addition, it places this event information into schema microdata tags for SEO optimization.

function findAddress($lines) {
  //Finds address by searching for finding line with a zipcode, and then concatenating the previous line with that line. This is prone to errors, however, and is only used as an example for showing how the script would work. A better method would involve leveraging existing APIs, such as Google's GeoCoding API.
  for($i = 0; $i<count($lines); $i++) {
    if(preg_match('/[0-9]{5}/', $lines[$i])) {
      return [$lines[$i-2], $lines[$i]];
    }
  }
  return "";
}

function findDateTime($lines) {
  //Finds dates that are in format MM-DD-YYYY and times that are in the format {H}H:MM {AM|PM}. Again, this is only used an examples. External APIs will have to be leveraged in order to be comprehensive in date/time extraction.
  $date = "";
  $time = "";
  $date_regex = '/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/';
  $time_regex = '/([1-9]|1[012]):[0-5][0-9] (AM|am|PM|pm)/';
  for($i = 0; $i<count($lines); $i++) {
    if(preg_match($date_regex, $lines[$i])) {
      preg_match($date_regex, $lines[$i], $matches);
      $date = $matches[0];
    }
    if(preg_match($time_regex, $lines[$i])) {
      preg_match($time_regex, $lines[$i], $matches);
      $time = $matches[0];
    }
  }
  return [$date, $time];
}

function main() {

  date_default_timezone_set('America/New_York');
  //$conn = mysqli_connect("127.0.0.1", "gopalr", "", "TempDB");
  $myfile = "test.HTML";

  //Get address, date, and time from the post.
  $lines = file($myfile);
  $fp = fopen($myfile, 'w'); 
  $address = findAddress($lines);
  $dateTime = findDateTime($lines);
  $date = date("m-d-Y", strtotime($dateTime[0]));
  $time = date("H:i", strtotime($dateTime[1]));

  //Add schema microdata information to file.
  unset($lines[sizeof($lines)-1]);
  unset($lines[sizeof($lines)-1]);

  array_push($lines, '<div itemscope itemtype="http://schema.org/Event">'."\n");
  array_push($lines, '  <div itemprop="name">Piano Performance</div>'."\n");
  array_push($lines, '  <time itemprop="startDate" datetime="'.$date.'T'.$time.'"></time>'."\n");
  array_push($lines, '  <span> itemprop="location"'."\n".'  '.$address[0].'  '.$address[1].'  </span>'."\n");
  array_push($lines, '</div>'."\n");
  array_push($lines, '</body>'."\n");
  array_push($lines, '</html>'."\n");

  fwrite($fp, implode('', $lines)); 
  fclose($fp);

  //Write event info into database.
  //$query = "INSERT INTO eventInfo('ID', 'Date', 'Time', 'Address') VALUES (1, '{$date}', '{$time}', '{$address}')";
  //mysqli_query($conn, $query) or die("Query not executed successfully.");
}

main();
  
?>
