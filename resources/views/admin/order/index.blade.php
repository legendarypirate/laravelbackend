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
                    <select class="custom-select rounded-0" aria-label=".form-select-lg example" name="phone" id="textField" >
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Хаяг</label>
                    <select class="custom-select rounded-0" aria-label=".form-select-lg example" name="address" id="textField1">
                    </select>
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
<script>
             $(document).ready(function () {
                $('#deliver').on('change', function () {
                    let id = $(this).val();
                    $('#textField').empty();
                    $('#textField').append(`<option value="0" disabled selected>Processing...</option>`);
                    $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function (response) {
                    var response = JSON.parse(response);
                    console.log(response);   
                    $('#textField').empty();
        
                    response.forEach(element => {
                    $('#textField').append(`<option value="${element['phone']}">${element['phone']}</option>`);
                    });
                            }
                        });
                    });
                });

                $(document).ready(function () {
                $('#deliver').on('change', function () {
                let id = $(this).val();
                $('#textField1').empty();
                $('#textField1').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                type: 'GET',
                url: 'address/' + id,
                success: function (response) {
                var response = JSON.parse(response);
                console.log(response);   
                $('#textField1').empty();
       
                response.forEach(element => {
                    $('#textField1').append(`<option value="${element['address']}">${element['address']}</option>`);
                    });
                }
            });
        });
    });

</script>
@endsection