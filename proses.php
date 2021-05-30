<?php
//koneksi data base
error_reporting(0);
$koneksi = new mysqli("localhost","root","","k-means");

//cek
if(mysqli_connect_errno()){
    echo"gagal konek";
    exit;
}

function read_data($koneksi, $query){
    $row = $koneksi->query($query)->fetch_array();
    return $row[0];
}

// proses insialisasi
// inisialisasi cluster awal

$total_data = read_data($koneksi,"SELECT count(*) FROM mahasiswa");

for ($i=0;$i<$total_data;$i++){
    $cl_awal[$i]="1";
}

//centeroid awal
$cn1[0] = array('3.15','3.9');
$cn2[0] = array('2.93','4.1');
$cn3[0] = array('3.19','5.1');
$cn4[0] = array('3.63','3.9');

$status = 'false';
$loop = '0';
$index = 0;
while($status=='false'){
    echo "<hr>";
    echo "<pre>";
    echo "Centeroid iterasi ke-$loop <br>";
    print_r($cn1[$loop]);
    print_r($cn2[$loop]);
    print_r($cn3[$loop]);
    print_r($cn4[$loop]);
    echo "</pre>";
    

    //Proses k-means
    $query = "SELECT * FROM mahasiswa";
    $hasil = $koneksi->query($query);
    while ($data = mysqli_fetch_assoc($hasil)){
        if ($index > 9){
            $index = 0;
        }
        extract($data);
        $hcn1 = 0;
        $hcn2 = 0;
        $hcn3 = 0;
        $hcn4 = 0;

        //centeroid 1
        $hcn1 = sqrt(pow($ipk-$cn1[$loop][0],2) + pow($lama_study-$cn1[$loop][1],2));
        //echo number_format($hcn1,2);
        //echo " centro 1 <br>";
        //ceteroid 2
        $hcn2 = sqrt(pow($ipk-$cn2[$loop][0],2) + pow($lama_study-$cn2[$loop][1],2));
        //echo number_format($hcn2,2);
        //echo " centro 2 <br>";
        //ceteroid 3
        $hcn3 = sqrt(pow($ipk-$cn3[$loop][0],2) + pow($lama_study-$cn3[$loop][1],2));
        //echo number_format($hcn3,2);
        //echo " centro 3 <br>";
        //ceteroid 4
        $hcn4 = sqrt(pow($ipk-$cn4[$loop][0],2) + pow($lama_study-$cn4[$loop][1],2));
        //echo number_format($hcn4,2);
        //echo " centro 4 <br>";        
        // cari nilai paling kecil
        if($hcn1 < $hcn2 && $hcn1 < $hcn3 && $hcn1 < $hcn4){
            $cluster[$index] = 'C1';
            update_tabel($koneksi,$no_urut,'C1');
        }
        elseif($hcn2 < $hcn1 && $hcn2 < $hcn3 && $hcn2 < $hcn4){
            $cluster[$index] = 'C2';
            update_tabel($koneksi,$no_urut,'C2');
        }
        elseif($hcn3 < $hcn1 && $hcn3 < $hcn2 && $hcn3 < $hcn4){
            $cluster[$index] = 'C3';
            update_tabel($koneksi,$no_urut,'C3');
        }
        else{
            $cluster[$index] = 'C4';
            update_tabel($koneksi,$no_urut,'C4');
        }
        $index++;

    }
    $loop++;
    //process centeroid baru
    //centeroid 1
    $cn1[$loop][0] = read_data($koneksi,"SELECT avg(ipk) FROM mahasiswa WHERE set_temp = 'C1'");
    $cn1[$loop][1] = read_data($koneksi,"SELECT avg(lama_study) FROM mahasiswa WHERE set_temp = 'C1'");
    //centeroid 2
    $cn2[$loop][0] = read_data($koneksi,"SELECT avg(ipk) FROM mahasiswa WHERE set_temp = 'C2'");
    $cn2[$loop][1] = read_data($koneksi,"SELECT avg(lama_study) FROM mahasiswa WHERE set_temp = 'C2'"); 

    //centeroid 3
    $cn3[$loop][0] = read_data($koneksi,"SELECT avg(ipk) FROM mahasiswa WHERE set_temp = 'C3'");
    $cn3[$loop][1] = read_data($koneksi,"SELECT avg(lama_study) FROM mahasiswa WHERE set_temp = 'C3'"); 

    //centeroid 4
    $cn4[$loop][0] = read_data($koneksi,"SELECT avg(ipk) FROM mahasiswa WHERE set_temp = 'C4'");
    $cn4[$loop][1] = read_data($koneksi,"SELECT avg(lama_study) FROM mahasiswa WHERE set_temp = 'C4'"); 

    $status = 'true';

    for ($i=0;$i<$total_data;$i++){
        if($cl_awal[$i] != $cluster[$i]){
            $status = 'false';
        }
    }
    if($status == 'false'){
        $cl_awal = $cluster;
    }
}
echo "proses selesai sebanyak $loop";

function update_tabel($koneksi, $no_urut, $nilai){
    $stmt = $koneksi->prepare("UPDATE mahasiswa set set_temp =? WHERE no_urut =?");
    $stmt -> bind_param("ss", mysqli_real_escape_string($koneksi, $nilai), 
    mysqli_real_escape_string($koneksi, $no_urut));
    $stmt->execute();
}

$show  = mysqli_query($koneksi, "SELECT * FROM mahasiswa");
?>

<body>
<table>
    <thead>
        <th class="text-center">Mahasiswa</th>
        <th class="text-center">IPK</th>
        <th class="text-center">Lama Studi</th>
        <th class="text-center">Cluster</th>
    </thead>
    <tbody style="height: 100vh;">
    <?php if(mysqli_num_rows($show)) {?>
        <?php while($row = mysqli_fetch_array($show)) {?>
            <tr>
               <td class="text-center"><?php echo $row['no_urut'] ?></td>
               <td class="text-center"><?php echo $row['ipk'] ?></td>
               <td class="text-center"><?php echo $row['lama_study'] ?></td>
               <td class="text-center"><?php echo $row['set_temp'] ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
</body>
