  <style>
    @media print {
      .thcls1 {
        font-size:18px!important; 
        text-align:center!important;
      }
    }
    .a {
      /* border: 2px solid black; */ */
    }
    .thcls {
      width:200px!important;
      font-size:18px!important; 
      text-align:left!important;
    }
    .thcls1 {
      font-size:18px!important; 
      text-align:center!important;
      background: lightgray;
    }
    .tblcls{
      border: 2px solid black; 
    }

    table, th, td {
      border-collapse: collapse;
    }
    </style>
    <html>
      <head>
        <title>Cetak Tanda Terima #{{$LubangTanamDetail['nama_petani']}}</title>
      </head>
      
      <body style="margin:15px;" onload="window.print();" >
       
          <div class="a" style="margin-bottom: 5px !important">
            <table style="width: 90%; margin-bottom:20px">
              <tr>
                <th style="text-align: center; font-size:20px">              
                  Tanda Terima Petani
                </th>
              </tr>
            </table >
            <table style="width: 100%; margin-left:20px; padding-left: 7px;">
              <tr>
                <th class="thcls">              
                  Nama Petani 
                </th>
                <td>:</td>
                <td style="font-size: 16px; font-weight: 900;text-decoration: underline;">
                  {{$LubangTanamDetail['nama_petani']}}
                </td>
              </tr>
              <tr>
                <th class="thcls">              
                  Nama FF 
                </th>                
                <td>:</td>
                <td>
                  {{$LubangTanamDetail['ff_name']}}
                </td>
              </tr>
              <tr>
                <th class="thcls">              
                  Tanggal Distribusi 
                </th>          
                <td>:</td>
                <td>
                  {{$LubangTanamDetail['newDateformatdistribution']}}
                </td>
              </tr>
              <tr>
                <th class="thcls">              
                  Alamat Distribusi 
                </th>          
                <td>:</td>
                <td>
                  {{$LubangTanamDetail['distribution_location']}}
                </td>
              </tr>
            </table >
            <table  style="margin-top:20px !important;margin-bottom:10px !important; margin-left:10px; padding-left: 7px;">
              <tr  class="tblcls">
                <th class="thcls1 tblcls" style="width:120px!important; ">              
                  No. Kantong
                </th>
                <th class="thcls1 tblcls" style="width:210px!important; height: 20px;">              
                  Spesies Bibit
                </th>
                <th class="thcls1 tblcls" style="width:75px!important; height: 20px;">              
                  Jumlah
                </th>
                <th class="thcls1 tblcls" style="width:150px!important; height: 20px;">              
                  Tanda Tangan
                </th>
              </tr>
              <tbody  >
                @foreach ($LubangTanamDetail['listvalbag'] as $val)
                <tr  class="tblcls">
                  <td  class="tblcls" style="text-align: center">              
                    {{$val['no_bag']}}
                  </td>
                  <td  class="tblcls" style="padding: 5px">              
                    @foreach ($val['listvaltemp'] as $valpohon)
                      <span style="text-align: end">{{$valpohon['pohon']}}</span> <br>
                    @endforeach
                  </td>
                  <td  class="tblcls" style="padding: 5px; text-align: right">              
                    @foreach ($val['listvaltemp'] as $valpohon)
                      <span style="text-align: end">{{$valpohon['amount']}}</span> <br>
                    @endforeach
                  </td>
                  <td  class="tblcls">              
                    
                  </td>
                </tr>
                
                @endforeach
              </tbody>
            </table >
          </div>
          <p style="page-break-after: always"></p>
        
        
      </body>
    </html>