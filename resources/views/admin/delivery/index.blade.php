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
                <h3 class="card-title">Хүргэлт үүсгэх</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              {!! Form::open(['url' => 'delivery/create', 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}
                <div class="card-body">
                  <div class="form-group">
                  <label for="exampleSelectRounded0">Захиалга <code></code></label>
                        <select class="custom-select rounded-0" name="shop" id="deliver">
                        <?php $user=DB::table('users')->where('role','=','customer')->get(); ?>
                        @foreach($user as $users)
                        <option value="{{$users->id}}">{{$users->name}}</option>
                        @endforeach
                        </select>
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
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Барааны мэдээлэл</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox">
                  <label class="form-check-label">Барааны агуулах</label>
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputEmail1">Барааны өртөг</label>
                    <input type="text" name="price" class="form-control"  placeholder="Барааны өртөг">
                  </div>
               
                  <div class="form-group">
                    <label for="exampleInputEmail1">Баглаа боодлын тоо</label>
                    <input type="text" name="number" class="form-control"  placeholder="Баглаа боодлын тоо">
                  </div>

                  <div class="form-group">
                        <label for="exampleSelectRounded0">Овор <code></code></label>
                        <select class="custom-select rounded-0" id="exampleSelectRounded0" name="size">
                        <option value="1-5 kg">1-5 kg</option>
                        <option value="5-10 kg">5-10 kg</option>
                        <option value="10 kg">10 kg<</option>
                        </select>
                  </div>
               
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox">
                  <label class="form-check-label">Хагарна</label>
                  </div>    <div class="form-check">
                  <input class="form-check-input" type="checkbox">
                  <label class="form-check-label">Хайлна</label>
                  </div>    <div class="form-check">
                  <input class="form-check-input" type="checkbox">
                  <label class="form-check-label">Хөлдөж</label>
                  </div>    <div class="form-check">
                  <input class="form-check-input" type="checkbox">
                  <label class="form-check-label">Сэгсэрч болохгүй</label>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Хадгалах</button>
                </div>
            </div>
            <!-- general form elements disabled -->
            
            <!-- /.card -->
            <!-- general form elements disabled -->
            
            <!-- /.card -->
          </div>
          
          <!--/.col (right) -->
        </div>

        
        <!-- /.row -->
      </div><!-- /.container-fluid -->


      {!! Form::close()!!}
    </section>
    <!-- /.content -->
  </div>

@endsection