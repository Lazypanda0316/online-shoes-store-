<?php
$server="localhost";
$user="root";
$password="";
$database="ecom";

$conn=new mysqli($server,$user,$password,$database);
if($conn->connect_error)
{
    die("Connecion Error!!!".$conn->connect_error);
}
// Set character set to UTF-8
$conn->set_charset("utf8");
?>