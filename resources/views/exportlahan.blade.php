<html>
<head>
</head>
<style>
table, th, td {
  /* border: 3px solid black; */
  border-collapse: collapse;
  font-size:20px;
}
</style>
<body>

    <?php
               
        date_default_timezone_set("Asia/Bangkok");

        $nama = 'Export_T4T_'.date("Ymd_h-i-s").'.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$nama);
	?>
    
    <div class="flex-center position-ref full-height">
        <div class="content" style="margin:50px">
            <h2>{{$nama_title}}</h2>
            
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">No_Lahan</th>
                    <th scope="col">Petani</th>
                    <th scope="col">Desa</th>
                    <th scope="col">kecamatan</th>
                    <th scope="col">Management Unit</th>
                    <th scope="col">Location</th>
                    <th scope="col">Luas Area/Tanam</th>
                    <th scope="col">Pohon Kayu/MPTS</th>
                    <th scope="col">Status</th>
                    <th scope="col">Nama FF</th>
                    <th scope="col">Nama FC</th>
                  </tr>
                </thead>
                <tbody id="tableSO">
                    {{-- @php
                        dd($result);
                    @endphp --}}
                    @foreach ($listval as $rslt)
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$rslt['lahanNo']}}</td>
                            <td>{{$rslt['petani']}}</td>
                            <td>{{$rslt['desa']}}</td>
                            <td>{{$rslt['nama_kec']}}</td>
                            <td>{{$rslt['nama_mu']}}</td>
                            <td>{{$rslt['location']}}</td>
                            <td>{{$rslt['land_area']}}m<sup>2</sup> / {{$rslt['planting_area']}} m<sup>2</sup></td>
                            <td>{{$rslt['pohon_kayu']}} pcs/ {{$rslt['pohon_mpts']}} pcs</td>
                            <td>{{$rslt['status']}}</td>
                            <td>{{$rslt['ff']}}</td>
                            <td>{{$rslt['nama_fc_lahan']}}</td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>