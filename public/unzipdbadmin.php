<?php


// Assuming `file.zip` is in the same directory as the executing script.
$file = 'dbadmin.zip';

// Get the absolute path to $file.
$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

$zip = new ZipArchive;
$res = $zip->open($file);

if ($res === TRUE) {
  // Extract it to the path we determined above
  $zip->extractTo($path);
  $zip->close();
  echo "WOOT! $file extracted to $path";
} else {
  echo "Doh! I couldn't open $file";
}

?>