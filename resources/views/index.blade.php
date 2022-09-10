<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Skripsi - Testing</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>
  @php
    $awal=array();
    $tujuan=array();
    $nama=array();
    $cabang=array();
    $buntu=array();
    $start=0;
  @endphp

  <!-- ======= Header ======= -->

    <header id="header" class="d-flex align-items-center">
      <div class="container d-flex flex-column align-items-center">

        <span><h1>Rekomendasi Kasus Uji</h1></span>
          <h1>Diagram Aktivitas</h1>
            <h2>Skripsi</h2>

        <div class="subscribe">
          <h4>Upload File *ptl</h4>
            <form action="{{route('upload.store')}}" method="post" enctype="multipart/form-data">
              @csrf 
              <div class="form-control">
                <input type="file" name="file" accept=".ptl" required>
              </div>
                <button type="submit" class="btn btn-primary">Proses</button>
            </form>
        </div>
      </div>
    </header>

  <!-- End #header -->

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container">
        <div class="section-title">
          @if(session()->has('success'))
            {{ session()->get('success')}}

            <?php
              $cek=1;
              $nama_file=session()->get('success');
              $data = file('file/'.$nama_file);
              $hasil = array();
              $counter = 0;
              $no=1;
                
              for ($i = 0; $i < count($data); $i++){
                // Activity State
                if( (str_contains($data[$i], '(object ActivityStateView ')) ){ 
                    $activity_state = "ActivityState";
                    $activity_name = str_replace('"', '', trim($data[$i],"\t     (object ActivityStateView "));
                    $activity_name = str_replace('@', '', trim($activity_name));
                    $id = substr($activity_name,-2);
                    $activity_name = preg_replace('/[0-9]+/', '', $activity_name);
                    $counter++;
  
                    $hasil[$counter] = array(
                        "State" => $activity_state,
                        "Activity Name" => $activity_name,
                        "ID" => $id,
                        "Dependency" => ""
                    );
                  }
                    // DecisionState
                  elseif( (str_contains($data[$i], '(object DecisionView ')) ){
                    $activity_state = "DecisionState";
                    $activity_name = str_replace('"', '', trim($data[$i],"\t     (object DecisionView "));
                    $activity_name = str_replace('@', '', trim($activity_name));
                    $id = substr($activity_name,-2);
                    $activity_name = preg_replace('/[0-9]+/', '', $activity_name);
                    $counter++;

                    $hasil[$counter] = array(
                        "State" => $activity_state,
                        "Activity Name" => $activity_name,
                        "ID" => $id,
                        "Dependency" => ""
                    );
                  }
  
                  //StartState & EndState
                  elseif( (str_contains($data[$i], '(object StateView ')) ) {
                    $activity_state = str_replace('"', '', trim($data[$i],"\t     (object StateView "));
                    $activity_state = str_replace('@', '', trim($activity_state));
                    $activity_state = str_replace('$', '', trim($activity_state));
                    $activity_state = preg_replace('/UNNAMED/i', '', $activity_state);
                    $activity_state = preg_replace('/[0-9]+/','', $activity_state);
                    $activity_state = str_replace(' ', '', trim($activity_state));
              
                    $activity_name = str_replace('"', '', trim($data[$i],"\t     (object StateView "));
                    $activity_name = preg_replace('/StartState/', '',$activity_name);
                    $activity_name = preg_replace('/EndState/', '', $activity_name);
                    $activity_name = str_replace('@', '', trim($activity_name));
                    $id = substr($activity_name,-2);
                    $activity_name = preg_replace('/[0-9]+/','', $activity_name);
                    $counter++;

                    $hasil[$counter] = array(
                        "State" => $activity_state,
                        "Activity Name" => $activity_name,
                        "ID" => $id,
                        "Dependency" => ""
                    );
                  }
                }
  
            /** 
            * Looping Kedua
            * untuk mencari Dependency 
            * */
            for ($i = 0; $i < count($data); $i++){
  
                if( (str_contains($data[$i], '(object TransView ')) ){
  
                    $client = str_replace('"', '', trim($data[$i+4],"\t     client\t@ \n"));
                    $supplier = str_replace('"', '', trim($data[$i+5],"\t     supplier\t@ \n"));
  
                    for ($urutan = 1; $urutan <= count($hasil); $urutan++) { 
                        
                        if ($hasil[$urutan]['ID'] == $supplier) {
                            $hasil[$urutan]["Dependency"] = $client;
                        }else if($hasil[$urutan]["State"] == "StartState"){
                            $hasil[$urutan]["Dependency"] = 0;
                        }
                    }  
                }
            }  
        ?>
        <div class="card py-5 px-5 icon_box">
          <div class="row">
            <div class="col-10" align="center">
                <span><h2 style="color:black">Activity Dependency Table</h2></span>
                  <table class = "table table-bordered">
                          <tr>
                            <td>No</td>
                            <td>State</td>
                            <td>Activity Name</td>
                            <td>ID</td>
                            <td>Dependency</td>
                          </tr>
                          @foreach($hasil as $key => $value)
                          <tr>
                            <td>{{$no++}}</td> 
                            <td>{{$value['State']}}</td>
                            @if($value['Activity Name']=='$UNNAMED$ ')
                              @php
                                $unname=$value['State'];
                                $a = substr($unname,0,-5);
                              @endphp
                                <td>{{$a}}</td>
                            @else
                            <td>{{$value['Activity Name']}}</td>
                            @endif
                            <td>{{$value['ID']}}</td>
                            <td>{{$value['Dependency']}}</td>                
                          </tr>
                          @endforeach
                        </table>
                      </div>

                      <br><br>
                      <div class="col-10" align="center">
                        <span><h2 style="color:black ">Activity Dependency Graph</h2></span>
                        <div style="color: black">
                          <table  class = " table-bordered">
                            <tr>
                              <td>Dependency</td>
                              <td>ID</td>
                            </tr>
                          @foreach ($hasil as $key => $value)
                              <?
                                $dependency = trim($value['Dependency'], " \n");
                                $dependency = str_replace('\n', '', trim($dependency));
                                $id = trim($value['ID'], " \n");
                              ?>
                              <tr>
                                <td>{{ $value['Dependency'] }}</td>
                                <td>{{ $value['ID'] }}</td>
                              </tr>

                              @php
                              if($value['Activity Name']=='$UNNAMED$ '){
                                $unname=$value['State'];
                                $a = substr($unname,0,-5);
                                $nama[]=$a;
                              }
                              else{
                                $nama[]=$value['Activity Name'];
                              }
                                $awal[]=$value['Dependency'];
                                $tujuan[]=$value['ID'];
                                
                                if ($value['Dependency']==0) {
                                  $start=$value['ID'];
                                }
                              @endphp
                          @endforeach
                          </table>

                          {{-- Mencari nilai cabang --}}
                          @foreach ($hasil as $key1 => $value1)
                            @foreach ($hasil as $key2 => $value2)
                            @if($value1['Dependency']==$value2['Dependency'] && $key1!=$key2 )
                              @if(in_array($value1['Dependency'],$cabang))
                              @else
                                @php
                                    $cabang[]=$value1['Dependency'];
                                @endphp
                              @endif
                            @endif
                            @endforeach
                          @endforeach

                          {{-- Mencari jalan buntu --}}
                          @foreach ($hasil as $key1 => $value1)
                            @php
                              $cek_buntu=0;
                            @endphp
                            @foreach ($hasil as $key2 => $value2)
                            @if($value1['ID']==$value2['Dependency'])
                              @php
                                  $cek_buntu=1;
                              @endphp
                            @endif
                            @endforeach
                            @if($cek_buntu==0)
                              @php
                                  $buntu[]=$value1['ID'];
                              @endphp
                            @endif
                          @endforeach
                        </div>
                            <br>
                            <span><h2 style="color:black ">Jalur Uji</h2></span>
                            <div style="color: black">
                              <div id="hasil"></div>
                              <br>
                              <span><h2 style="color:black ">Keterangan Jalur Uji</h2></span>
                              <div style="color: black">
                                <div id="hasil-nama"></div>
                                  <table id="table" class="table table-bordered">
                                    <tbody>
                                      <tr>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                            @endif
                        </div>
                      </div>
                    </div>
                  </div>
              </section>
              <script>
                var awal ={!!json_encode($awal)!!};
                var tujuan ={!!json_encode($tujuan)!!};
                var nama ={!!json_encode($nama)!!};
                var cabang ={!!json_encode($cabang)!!};
                var buntu ={!!json_encode($buntu)!!};
                var start ={!!json_encode($start)!!};
                
                var array=[];
                var jalur_uji=[];

                for (let i = 0; i < cabang.length; i++) {
                  cabang[i]=Number(cabang[i]);
                 }     
                for (let i = 0; i < buntu.length; i++) {
                  buntu[i]=Number(buntu[i]);
                 }     

                class Graph
                {
                  
                  // Constructor
                  constructor(v)
                  {
                    this.V = v;
                    this.adj = new Array(v);
                    for(let i = 0; i < v; i++)
                      this.adj[i] = [];
                  }
                  
                  addEdge(v, w)
                  {
                    
                    this.adj[v].push(w);
                  }
                  
                  DFSUtil(v, visited)
                  {
                    
                    visited[v] = true;
                    
                    array[array.length]=v;

                    for(let i of this.adj[v].values())
                    {
                      let n = i
                      if (!visited[n])
                        this.DFSUtil(n, visited);
                    }
                  }
                  DFS(v)
                  {
                    let visited = new Array(this.V);
                    for(let i = 0; i < this.V; i++)
                      visited[i] = false;
                
                    this.DFSUtil(v, visited);
                  }
                }
                
                g = new Graph(100);
                for (let i = 0; i < awal.length; i++) {
                  g.addEdge(Number(awal[i]), Number(tujuan[i]));
                 }          
                
                  g.DFS(Number(start));
                  
                var jml_jalur_uji=cabang.length+1;
                for (var i = 0; i < (jml_jalur_uji); i++) {
                  console.log(cabang);
                  document.getElementById("table").innerHTML += "<td>";

                  for (var j = 0; j < (array.length); j++) {
                    document.getElementById("hasil").innerHTML +=array[j] + ", ";

                    for (let k = 0; k < nama.length; k++) {
                      if(array[j]==tujuan[k]){
                        document.getElementById("table").innerHTML += nama[k] + ", ";
                      }
                    }
                    document.getElementById("table").innerHTML += "</td>";

                    if(cabang.includes(array[j]))
                    {
                      var posisi=j+1;
                      jalur_uji[jalur_uji.length]=array[j];
                      console.log("cabang"+array[j]);
                    }else{
                      jalur_uji[jalur_uji.length]=array[j];
                      console.log("bukan cabang"+array[j]);
                    }

                    if(buntu.includes(array[j]))
                    {               
                      console.log("buntu"+array[j]);
                      array.splice(posisi,(j+1)-posisi);
                      j=array.length;

                      for (var k = 0; k < cabang.length; k++) {
                        if(cabang[k]==array[posisi-1]){
                        cabang.splice(k,1);
                        }
                      }
                    }else{
                      console.log("bukan buntu"+array[j]);
                    }
                  }
                  document.getElementById("hasil").innerHTML +="<br>";
                  document.getElementById("hasil-nama").innerHTML +="<br>";
                }
                </script>
                
</body>
</html>