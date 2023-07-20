@extends('admin.master')

@section('mainContent')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
           
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Бараа үүсгэх</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              {!! Form::open(['url' => 'good/create', 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Дэлгүүр</label>
                    <select class="custom-select rounded-0" name="shop">
                        <?php $user=DB::table('users')->where('role','=','customer')->get(); ?>
                        @foreach($user as $users)
                        <option value="{{$users->name}}">{{$users->name}}</option>
                        @endforeach
                        </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Агуулах</label>
                    <select class="custom-select rounded-0" name="ware">
                        <?php $ware=DB::table('wares')->get(); ?>
                        @foreach($ware as $wares)
                        <option value="{{$wares->name}}">{{$wares->name}}</option>
                        @endforeach
                        </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Барааны нэр</label>
                    <input type="text" name="goodname" class="form-control"  placeholder="good">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Үнэ</label>
                    <input type="text" name="price" class="form-control"  placeholder="price">
                  </div>
                
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Хадгалах</button>
                </div>
                {!! Form::close()!!}
            </div>
            <!-- /.card -->

            <!-- general form elements -->
        
            <!-- /.card -->

            <!-- Input addon -->
        
            <!-- /.card -->

          </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">
            <!-- Form Element sizes -->
         
            <!-- /.card -->

        
            <!-- /.card -->

            <!-- general form elements disabled -->
          
            <!-- /.card -->
            <!-- general form elements disabled -->
            
            <!-- /.card -->
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  @include('sweetalert::alert')

@endsection