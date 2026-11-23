<?php

$host = "localhost";
$user = "root";
$pass = "";
$database = "kasir";

$conn = mysqli_connect($host,$user,$pass,$database);

if (!$conn) {
    die("gagal koneksi ke databse" . mysqli_connect_error());
}