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
                <h3 class="card-title">Хүргэлт дуудлагын мэдээлэл (Захиалга)</h3> <!-- Хүргэлт захиалга талаас -->
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              @if(auth()->user()->role=='customer')
              {!! Form::open(['url' => 'order/create', 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}
                <div class="card-body">
                <div class="form-group">
                        <label for="exampleSelectRounded0">Салбар эсвэл харилцагч мерчантын нэр <code></code></label>
                        
                        <select class="custom-select rounded-0" name="shop" id="deliver">
                        <?php $user=DB::table('users')->where('role','=','customer')->get(); ?>
<option value="">Салбар-мерчант сонгох</option>
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
                    <input type="text" name="comment" class="form-control"  placeholder="Илгээмжийн нэр төрөл, тоо ширхэг, барааны код, сериал дугаар, овор хэмжээ болон очиж авахдаа анхаарах зүйлсийг бичнэ үү   ">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Захиалгын код</label>
                    <input type="text" name="comment" class="form-control"  placeholder="Та өөрийн ( дэлгүүрийн) захилагын кодоо оруулж өгнө үү /Хүргэгдэх хаяг ижил бол ижил код үүсгэж өгнө/">
                  </div>
                  <div class="form-group">
                        <label for="exampleSelectRounded0">Татан авалтын цаг<code></code></label>
                        <select class="custom-select rounded-0" name="shop" id="deliver">
                      
                        <option value="Өглөө">Өглөө (08-14 цаг)</option>
                        <option value="Орой">Үдээс хойш (14-20 цаг)</option>
                        <option value="Орой">Орой (20-00 цаг)</option>

                        </select>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Хадгалах</button>
                </div>
                {!! Form::close()!!}
                @else


                {!! Form::open(['url' => 'order/create', 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Дэлгүүр</label>
                    <input type="hidden" name="shop" class="form-control" value="{{auth()->user()->id}}"  placeholder="Тайлбар">
                  </div>
                  <?php $phone=DB::table('phones')->where('userid',Auth::user()->id)->get(); ?>
                  <?php $address=DB::table('addresses')->where('userid',Auth::user()->id)->get(); ?>

                  <div class="form-group">
                    <label for="exampleInputEmail1">Утас</label>
                    <select class="custom-select rounded-0" aria-label=".form-select-lg example" name="phone" id="textField" >
                          @foreach($phone as $phones)
                                <option value="{{$phones->phone}}">{{$phones->phone}}</option>
                          @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Хаяг</label>
                    <select class="custom-select rounded-0" aria-label=".form-select-lg example" name="address" id="textField1">

                    @foreach($address as $addresses)
                                <option value="{{$addresses->address}}">{{$addresses->address}}</option>
                          @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Тайлбар</label>
                    <input type="text" name="type" class="form-control"  placeholder="Жишээ нь 10 жижиг хайрцаг, 2 уут бараа нийт 12, ойролцоогоор 1-с 2 кг жинтэй">
                  </div>
                  <div class="form-group">
                        <label for="exampleSelectRounded0">Төрөл <code></code></label>
                        <select class="custom-select rounded-0" name="type" onchange="yesnoCheck(this);">
<option value="Өглөө">Өглөө (08-12 цаг)</option>
<option value="Орой">Орой (12-19 цаг)</option>

                        </select>
                  </div>

                  <div class="form-group" id="ifYes" style="display: none;">
                    <div  style="color:green;"> Яаралтай хүргэлт нь 1-2 цагийн дотор хүргэгдэх бөгөөд нэмэлт төлбөртэй болохыг анхаарна уу.  </div>
                  </div>

      
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Хадгалах</button>
                </div>
                {!! Form::close()!!}

                @endif

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
                
                function yesnoCheck(that) {
                    if (that.value == "Яаралтай") {
                        document.getElementById("ifYes").style.display = "block";
                    } else {
                        document.getElementById("ifYes").style.display = "none";
                    }
                }

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
