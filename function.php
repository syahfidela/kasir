<?php

session_start();

//bikin koneksi
$c = mysqli_connect('localhost', 'root', '', 'kasir1');

//login
if(isset($_POST['login'])){
    //initiate variable
    $username =$_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($c, "SELECT * FROM  user WHERE username='$username' and password='$password' ");
    $hitung = mysqli_num_rows($check);

    if($hitung>0){
        //jika datanya ditemukan
        //berhasil login

        $_SESSION['login'] = 'True';
        header('location:index.php');
    }else {
        //data tidak ditemukan
        //gagal login
        echo '
        <script>alert("Username atau Password salah");
        window.location.href="login.php"
        </script>
        ';
    }
}

//data pesanan
if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $insert = mysqli_query($c,"insert into produk (namaproduk,deskripsi,harga,stok) values ('$namaproduk','$deskripsi',
    '$harga','$stok')");

    if($insert){
        header('location:stok.php');
    } else {
        echo '
        <script>alert("Gagal menambah barang baru");
        window.location.href="stok.php"
        </script>
        ';
    }
}

//data pelanggan
if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelpon = $_POST['notelpon'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c,"insert into pelanggan (namapelanggan,notelpon,alamat) values ('$namapelanggan','$notelpon',
    '$alamat')");

    if($insert){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal menambah pelanggan baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

//tambah pesanan
if(isset($_POST['tambahpesanan'])){
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($c,"insert into pesanan (idpelanggan) values ('$idpelanggan')");

    if($insert){
        header('location:index.php');
    } else {
        echo '
        <script>alert("Gagal menambah pesanan baru");
        window.location.href="index.php"
        </script>
        ';
    }
}

//produk dipilih di pesanan
if(isset($_POST['tambahproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty']; //jumlah

    $insert = mysqli_query($c,"insert into detailpesanan (idpesanan,idproduk,qty) values ('$idp','$idproduk','$qty')");

    //hitung stok sekarang ada berapa
    $hitung1 = mysqli_query($c, "select * from produk where idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stoksekarang = $hitung2['stok']; //stok barang saaat ini

    if($stoksekarang>=$qty){

        //kurangi stoknya dengan jumlah yang akan di keluarkan
        $selisih = $stoksekarang-$qty;

        //stoknya cukup
        $insert = mysqli_query($c, "insert into detailpesanan (idpesanan,idproduk,qty) values ('$idp','$idproduk','$qty')");
        $update = mysqli_query($c,"update produk set stok='$selisih' where idproduk='$idproduk'");

        if($insert&&$update){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal menambah pesanan baru");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
        }

    } else {
        //stoknya tidak cukup
        echo '
        <script>alert("Stok barang tidak cukup");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';

    }
}

//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

    $insertb = mysqli_query($c, "insert into masuk (idproduk,qty) values('$idproduk','$qty')");

    if($insertb){
        header('location:masuk.php');
    } else {
        echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';
    }       
}

//hapus produk pesanan
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp']; //detailpesanan
    $idpr = $_POST['idpr'];
    $idpesanan = $_POST['idpesanan'];

    //cek qty sekarang
    $cek1 = mysqli_query($c,"select * from detailpesanan where iddetailpesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    //cek stok sekarang
    $cek3 = mysqli_query($c, "select *from produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stoksekarang = $cek4['stok'];

    $hitung = $stoksekarang+$qtysekarang;

    $update = mysqli_query($c,"update produk set stok='$hitung' where idproduk='$idpr'"); //update stok
    $hapus = mysqli_query($c, "delete from detailpesanan where idproduk='$idpr' and iddetailpesanan='$idp'");


    if($update&&$hapus){
        header('location:view.php?idp='.$idorder);
    } else {
        echo '
        <script>alert("Gagal menghapus barang");
        window.location.href="view.php?idp='.$idorder.'"
        </script>
        ';

    }
}

//edit barang
if(isset($_POST['editbarang'])){
    $np = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp']; //idproduk

    $query = mysqli_query($c,"update produk set namaproduk='$np', deskripsi='$desc', harga='$harga' where idproduk='$idp' ");

    if($query){
        header('location:stok.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stok.php"
        </script>
        ';
    }
}

//hapus barang
if(isset($_POST['hapusbarang'])){
    $idp = $_POST['idp'];

    $query = mysqli_query($c,"delete from produk where idproduk='$idp'");
    if($query){
        header('location:stok.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stok.php"
        </script>
        ';
    }
}

//edit pelanggan
if(isset($_POST['editpelanggan'])){
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelpon'];
    $a = $_POST['alamat'];
    $id = $_POST['idpl'];

    $query = mysqli_query($c,"update pelanggan set namapelanggan='$np', notelpon='$nt', alamat='$a' where idpelanggan='$id' ");

    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

//hapus pelanggan
if(isset($_POST['hapuspelanggan'])){
    $idpl = $_POST['idpl'];

    $query = mysqli_query($c,"delete from pelanggan where idpelanggan='$idpl'");
    if($query){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

//mengubah data barang masuk
if(isset($_POST['editdatabarangmasuk'])){
    $qty = $_POST['qty'];
    $idm = $_POST['idm']; //idmasuk

    //qty sekarang 
    $caritahu = mysqli_query($c, "select * from masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    if($qty >= $qtysekarang){
        //kalau inputan user lebih besar daripada qty yang tercatat
        //hitung selisih
        $selisih = $qty-$qtysekarang;

        $query1 = mysqli_query($c,"update masuk set qty='$qty', where idmasuk='$idm'");
        $query2 = mysqli_query($c,"update masuk set qty='$qty', where idmasuk='$idm'");
        
        if($query){
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';
        }
    }
}


?>