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
                <h3 class="card-title">Захиалга үүсгэх</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              {!! Form::open(['url' => 'order/create', 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Дэлгүүр</label>
                    <input type="text" name="shop" class="form-control"  placeholder="Дэлгүүр">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Утас</label>
                    <input type="text" name="phone" class="form-control"  placeholder="Утас">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Хаяг</label>
                    <input type="text" name="address" class="form-control"  placeholder="Хаяг">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Тайлбар</label>
                    <input type="text" name="comment" class="form-control"  placeholder="Тайлбар">
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

@endsection