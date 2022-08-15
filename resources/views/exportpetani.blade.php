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

        $nama = 'Stock_Opname_'.date("Ymd_h-i-s").'.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$nama);
	?>
    <div class="flex-center position-ref full-height">
        <div class="content" style="margin:50px">
            <h2>Cetak Stock Gudang {{$nama_gudang}}</h2>
            
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">KodeBuku</th>
                    <th scope="col">JudulBuku</th>
                    <th scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="tableSO">
                    {{-- @php
                        dd($result);
                    @endphp --}}
                    @foreach ($result as $rslt)
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$rslt->code_barang}}</td>
                            <td>{{$rslt->judul_buku}}</td>
                            @if ($rslt->stock == null)
                                <td>0</td>
                            @else
                                <td>{{$rslt->stock}}</td>
                            @endif
                            
                        </tr>
                    @endforeach 
                </tbody>
              </table>
        </div>
    </div>
</body>
</html>