<?php
//koneksi data base
$koneksi = new mysqli("localhost","root","","k-means");
$query  = mysqli_query($koneksi, "SELECT * FROM mahasiswa");

//cek
if(mysqli_connect_errno()){
    echo"gagal konek";
    exit;
}
?>

<!DOCTYPE html>
<html>
	
<head>
	<title>
		Tugas Data Mining
	</title>
</head>

<body style="text-align:center;">
	
	<h1 style="color:green;">
		K-Means Berbasis Web
	</h1>
	
	<h4>
		I Kadek Aldy Oka Ardita
        1808561091
	</h4>
	<a href="proses.php">
    Lanjut ke proses K-means
    </a>

</head>
<br>
<br>
<body>
<p> data yang digunakan </p>
<table>
    <thead>
        <th class="text-center">Mahasiswa</th>
        <th class="text-center">IPK</th>
        <th class="text-center">Lama Studi</th>
    </thead>
    <tbody style="height: 100vh;">
    <?php if(mysqli_num_rows($query)) {?>
        <?php while($row = mysqli_fetch_array($query)) {?>
            <tr>
               <td class="text-center"><?php echo $row['no_urut'] ?></td>
               <td class="text-center"><?php echo $row['ipk'] ?></td>
               <td class="text-center"><?php echo $row['lama_study'] ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
</body>
            


</html>
