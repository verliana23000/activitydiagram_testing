<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Skripsi - Testing</title>
        <!-- Favicon-->
        {{-- <link rel="icon" type="image/x-icon" href="assets/favicon.ico" /> --}}
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Generate Test Case</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                </div>
            </div>
        </nav>
        <!-- Page content-->
        <div class="container">
            <div class="text-left mt-5">
                <form action="{{route('upload.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">File:</label>
                        <input class="form-control" type="file" name="file">
                        <label>Catatan: format file harus *.ptl</label>
                    </div>
                    <button type="submit" class="btn btn-success">Proses</button>
                </form>        
            </div>
        </div>
        @if(session()->has('success'))
            {{ session()->get('success')}}
            
        <?php
            $nama_file=session()->get('success');
            $data = file('file/'.$nama_file);
            $hasil = array();
            $counter = 0;
            $no=1;
                
            
            for ($i = 0; $i < count($data); $i++){
            
            if( (str_contains($data[$i], '(object ActivityStateView ')) ){ // Activity State
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
            * Looping Pertama
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
        <div class="card my-5 mx-5 py-5 px-5">
            <table class = "table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <td>No</td>
                        <td>State</td>
                        <td>Name</td>
                        <td>ID</td>
                        <td>Dependency</td>
                    </tr>
                    @foreach($hasil as $key => $value)
                    <tr>
                        <td>{{$no++}}</td> 
                        <td>{{$value['State']}}</td>
                        <td>{{$value['Activity Name']}}</td>
                        <td>{{$value['ID']}}</td>
                        <td>{{$value['Dependency']}}</td>                  
                    </tr>
                    @endforeach
                </table>
            </tbody>
        </div>
        @endif

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>
