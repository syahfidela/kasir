<?php 

require 'function.php';

//jika terbentuk login maka bisa
if(isset($_SESSION['login'])){
//sudah login

}else{
    //belum login
    header('location:login.php');
}

