<?php
//print_r($_FILES);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "df";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
foreach ($_FILES as $key => $value) {
$validextensions = array("csv", "svg");
$temporary = explode(".", $_FILES[$key]["name"]);
$file_extension = end($temporary);
$c = 0;
if ($_FILES[$key]["size"] < 100000 && in_array($file_extension, $validextensions)) {
   if ($_FILES[$key]["error"] > 0)
   {
       echo "Return Code: " . $_FILES[$key]["error"] . "<br/><br/>";
   }
   else
   {
       if (file_exists("upload/" . $_FILES[$key]["name"])) {
           echo $_FILES[$key]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
       }
       else
       {
           $sourcePath = $_FILES[$key]['tmp_name']; // Storing source path of the file in a variable
           $targetPath = "upload/".$_FILES[$key]['name']; // Target path where file is to be stored
           move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
           $file = "upload/".$_FILES[$key]['name'];
           $array = $fields = array();
           $i = 0;
           $handle = fopen($file, "r");
           if ($handle) {
              while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                  $fields = $row;
                   continue;
                }
                foreach ($row as $k=>$value) {
                   $array[$i][$fields[$k]] = $value;
                }
                $i++;
              }
              if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
              }
              fclose($handle);
           }
           //print_r($array);
           foreach ($array as $key => $value) {
               $name = $value['Name'];
               $type = $value['Type'];
               $age = $value['age'];
               $sql = "INSERT INTO `users` (`Name`,`Type`,`age`) VALUES ('$name', '$type','$age')";
               $conn->query($sql);
           }
       }
   }
}
else
{
   echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}
}
