<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "df";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$validextensions = array("csv", "svg");
$temporary = explode(".", $_FILES["fileToUpload"]["name"]);
$file_extension = end($temporary);
$c = 0;
if ($_FILES["fileToUpload"]["size"] < 100000 && in_array($file_extension, $validextensions)) {
    if ($_FILES["fileToUpload"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br/><br/>";
    }
    else
    {
        if (file_exists("upload/" . $_FILES["fileToUpload"]["name"])) {
            echo $_FILES["fileToUpload"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
        }
        else
        {
            $sourcePath = $_FILES['fileToUpload']['tmp_name']; // Storing source path of the file in a variable
            $targetPath = "upload/".$_FILES['fileToUpload']['name']; // Target path where file is to be stored
            move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
            $file = "upload/".$_FILES['fileToUpload']['name'];
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
                $sql = "INSERT INTO `users` (`Name`,`Type`,`age`)
VALUES ('$name', '$type','$age')";
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

            }
        }
    }
}
else
{
    echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}
