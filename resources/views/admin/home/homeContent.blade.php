@extends('admin.master')

@section('mainContent')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Хянах самбар</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box" style="border:3px solid #5bc0de;">
              <div class="inner">
                <h3>{{$driver}}</h3>

                <p>Жолооч</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer" style="color:black;">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box" style="border:3px solid #5cb85c;">
              <div class="inner">
                <h3>{{$delivery}}<sup style="font-size: 20px"></sup></h3>

                <p>Хүргэлт</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer" style="color:black;">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box" style="border:3px solid #f0ad4e;">
              <div class="inner">
                <h3>{{$customer}}</h3>

                <p>Харилцагч</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer" style="color:black;">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box" style="border:3px solid #d9534f;">
              <div class="inner">
                <h3>{{$order}}</h3>

                <p>Захиалга</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer" style="color:black;">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            
            <!-- /.card -->

            <!-- DIRECT CHAT -->
          
            <!--/.direct-chat -->

            <!-- TO DO List -->
     
            <!-- /.card -->
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
        
          <!-- right col -->
        </div>
          <div class="row"> 
          <div class="chart-container col-md-6" >
          <div class="pie-chart-container" style="height:400px">
            <canvas id="pie-chart"></canvas>
          </div> 

        </div>
        <div class="col-md-6">

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-chart-pie mr-1"></i>
      Шинэ хэрэглэгчид
    </h3>
    <div class="card-tools">
      <ul class="nav nav-pills ml-auto">
       
      </ul>
    </div>
  </div><!-- /.card-header -->
  <div class="card-body">
    <div class="card-body table-responsive p-0" style="">
<table class="table table-head-fixed text-nowrap">
<thead>
<tr>
<th>ID</th>
<th>Нэр</th>
<th>Role</th>
<th>Огноо</th>
<th>Үйлдэл</th>


</tr>
</thead>
<tbody>

@foreach($ware as $wares)
<tr>
<td>{{$wares->id}}</td>
<td>{{$wares->name}}</td>
<td>{{$wares->role}}</td>
<td>{{$wares->created_at}}</td>
<td><a href="{{url('/user/delete/'.$wares->id)}}">Устгах</a></td>

</tr>
@endforeach

</tbody>
</table>
</div>
  </div><!-- /.card-body -->
</div>
</div>
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <script>
  $(function(){
      //get the pie chart canvas
      var cData = JSON.parse(`<?php echo $chart_data ; ?>`);
      var ctx = $("#pie-chart");
 
      //pie chart data
      var data = {
        labels: cData.label,
        datasets: [
          {
            label: "Users Count",
            data: cData.data,
            backgroundColor: [
              "#DEB887",
              "#A9A9A9",
              "#DC143C",
              "#F4A460",
              "#2E8B57",
              "#1D7A46",
              "#CDA776",
            ],
            borderColor: [
              "#CDA776",
              "#989898",
              "#CB252B",
              "#E39371",
              "#1D7A46",
              "#F4A460",
              "#CDA776",
            ],
            borderWidth: [1, 1, 1, 1, 1,1,1]
          }
        ]
      };
 
      //options
      var options = {
        responsive: true,
        scales: {
          x: {
            grid: {
              display: false,
              min: 0, // Optionally set the minimum value for the axis
            }
          },
          yAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
        },
        title: {
          display: true,
          position: "top",
          text: "Хүргэлтийн тоо сүүлийн 7 хоног",
          fontSize: 18,
          fontColor: "#111"
        },
        legend: {
          display: false,
          position: "bottom",
          labels: {
            fontColor: "#333",
            fontSize: 16
          }
        }
      };
 
      //create Pie Chart class object
      var chart1 = new Chart(ctx, {
        type: "bar",
        data: data,
        options: options
      });
 
  });
</script>
@endsection
