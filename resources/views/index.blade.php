<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Skripsi - Testing</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Maundy - v4.6.0
  * Template URL: https://bootstrapmade.com/maundy-free-coming-soon-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex flex-column align-items-center">

      <h1>Rekomendasi Kasus Uji</h1>
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
  </header><!-- End #header -->

  <main id="main">

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container">

      <div class="section-title">
        @if(session()->has('success'))
        <h2>Activity Dependency Table</h2>
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
      <div class="row mt-2">

        <div class="card my-5 mx-5 py-5 px-5 icon_box">
          <table class = "table table-bordered">
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
            </div>
            </div>

    </section><!-- End About Us Section -->



  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">
      {{-- <div class="copyright">
        &copy; Copyright <strong><span>Maundy</span></strong>. All Rights Reserved
      </div> --}}
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/maundy-free-coming-soon-bootstrap-theme/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
  </footer><!-- End #footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>