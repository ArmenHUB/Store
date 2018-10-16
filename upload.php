<?php
$validextensions = array("csv", "svg");
$temporary = explode(".", $_FILES["fileToUpload"]["name"]);
$file_extension = end($temporary);

if ($_FILES["fileToUpload"]["size"] < 100000 && in_array($file_extension, $validextensions)) {
    if ($_FILES["fileToUpload"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br/><br/>";
    }
    else
    {
        if (file_exists("../upload/" . $_FILES["fileToUpload"]["name"])) {
            echo $_FILES["fileToUpload"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
        }
        else
        {
            $sourcePath = $_FILES['fileToUpload']['tmp_name']; // Storing source path of the file in a variable
            $targetPath = "upload/".$_FILES['fileToUpload']['name']; // Target path where file is to be stored
            move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
            echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
            echo "<br/><b>File Name:</b> " . $_FILES["fileToUpload"]["name"] . "<br>";
            echo "<b>Type:</b> " . $_FILES["fileToUpload"]["type"] . "<br>";
            echo "<b>Size:</b> " . ($_FILES["fileToUpload"]["size"] / 1024) . " kB<br>";
            echo "<b>Temp file:</b> " . $_FILES["fileToUpload"]["tmp_name"] . "<br>";
        }
    }
}
else
{
    echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}


$file = $_FILES['fileToUpload']['name'];
$handle = fopen($file, "r");
$c = 0;
while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
{
    $name = $filesop[0];
    $email = $filesop[1];
   echo $name;
   echo "</br>";
   echo $email;

}
