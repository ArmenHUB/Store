<?php
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
            $handle = fopen($file, "r");
            while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
            {
                $name = $filesop[0];
                $type = $filesop[1];
                $age = $filesop[2];
                 if($c > 0){
                   echo $name;
                   echo "</br>";
                   echo $type;
                   echo "</br>";
                   echo $age;
                   echo "</br>";
                 }
                 $c++;
            }
        }
    }
}
else
{
    echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}
