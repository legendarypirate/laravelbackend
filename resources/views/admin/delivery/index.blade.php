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
                        <select class="custom-select rounded-0" name="shop" id="good">
                        <?php $user=DB::table('users')->where('role','=','customer')->get(); ?>
                        @foreach($user as $users)
                        <option value="{{$users->name}}">{{$users->name}}</option>
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
                <input type="checkbox" style="width:15px;height:15px;" id="checkbox2" name="check2" value="1" />
                  <label class="form-check-label">Барааны агуулах</label>
                  </div>
                            <div class="autoUpdate1" id="autoUpdate1" style="display:none;" >

                            <div class="mt-3 ">
                                        <label for="regular-form-3" class="form-label proddata">Барааны нэр </label><br>
                                        <select id="products" class="form-control formselect sm:mt-2 sm:mr-2 js-example-basic-single"  aria-label=".form-select-lg example" name="size" style="width:600px;">

                                            <?php
                                                    if(Auth::user()->role=='Customer'){
                                                        $good=DB::table('goods')->where('custname',Auth::user()->name)->orderBy('goodname','ASC')->get();
                                                    } else {
                                                        $good=DB::table('goods')->get();

                                                    }
                                            ?>
                                            <option  class="" value="#">Бараа сонгоно уу</option>
                                            
                                            @foreach($good as $goods)
                                            <option  class="product_id" value="{{$goods->id}}">{{$goods->goodname}}</option>
                                         
                                            @endforeach
                                     
                                        </select>
                                   
                             </div>

                                    <div class="mt-3">
                                        <label for="input-state-3" class="form-label">Тоо ширхэг</label>
                                        <input id="input-state-3" type="number" class="qty-input form-control border-danger" placeholder="Баглаа боодлын тоо" name="number">
                                      
                                    </div>
                                    <div class="pro-action">
                              <button type="button" class="add-to-cart btn btn-primary mt-5"><span> Сагсанд нэмэх</span> </button>
                              <div class="row" id="cart_details">
            
            @if(isset($cart_data))
                @if(Cookie::get('shopping_cart'))
                    @php $total="0" @endphp
                <div class="col-md-7 ms-auto">
                    <div class="cart-page-header"><h6 class="cart-page-header-title">Order list</h6></div>
                    <div class="d-flex flex-column gap-3">
                    
                        @foreach ($cart_data as $data)
                            <label class="order-card col-12 cartpage" data-cart-item-id="123" data-product-id="12">
                                <!-- <input class="order-card__input" type="checkbox" checked /> -->
                                <div class="order-card__body">
                            
                                    <input type="hidden" class="product_id" value="{{ $data['item_id'] }}" >
                                    <div class="product-row">
                                
                                        <div class="product-row__content">
                                            <h6 class="product-row__content-title"><div style="width:200px;">{{urldecode($data['item_name'])}}</div>  <div style="display:inline;">тоо: {{ number_format($data['item_quantity']) }}</div>   <div style="display:inline;margin-left:50px;">  Үнэ:  {{ number_format($data['item_price']) }} </div> </h6> 
                                            <div class="product-row__tally--price--amount" style="display:inline;"> </div>
                                            <div class="product-row__content-author">                                
                                            </div>
                                        </div>
                                        <div class="product-row__tally" style="display:inline;">
                                            <div class="product-row__tally--price">
                                             
                                          
                                            </div>
                                            <!-- <div class="product-row__tally--action">
                                                <button class="d-flex gap-2 align-items-center pe-0 delete-item">
                                                    <i class="far fa-trash-alt"></i>
                                                <a  class="delete_cart_data"> X</a> 
                                                </button>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            
            
                <div class="col-md-3 me-auto">
        
                <div class="cart-page__purchase">
                    <div class="cart-page__purchase-lists">
                        @foreach ($cart_data as $data)
                      
                     
                            @php $total = $total + ( $data["item_price"] * $data["item_quantity"] ) @endphp
                        @endforeach
                    
                    </div>
                    <div class="cart-page__purchase-total">
                        <div class="cart-page__purchase-total-item">
                            <div class="span">Total sum:</div>
                            <div class="total-price">₮ {{ number_format($total, 2) }}</div>
                        </div>
                    </div>
                    @auth
                    
                
                    @endauth
                    @guest
                    
                    
                        <div class="d-grid mt-3"><a href="#"><button class="btn btn-success btn-lg rounded-0"  type="button" data-bs-toggle="modal" data-bs-target="#modalLogin" aria-controls="loginToSystem" aria-logedin="false" aria-label="Signin">Purchase</button></a></div>
                    @endguest
                </div>
                </div>
            @endif
                @else
                    <div class="row">
                    <div class="col-md-12 mycard py-5 text-center">
                        <div class="mycards">
                            <h4>Таны сагс одоогоор хоосон байна.</h4>
                        
                        </div>
                    </div>
                </div>
            @endif
        </div>
                            </div>
                                   
                         <!-- Your Product ID -->
                            <input type="hidden" class="qty-input" value="1"> <!-- Your Number of Quantity -->

                            </div>
                        
                       
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
                    <label for="exampleInputEmail1">Барааны төрөл</label>
                    <input type="text" name="goodtype" class="form-control"  placeholder="Хувцас, гоо сайхан, тоглоом гэх мэт">
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
<script>
              $(document).ready(function(){
$('#checkbox2').change(function(){
if(this.checked){
$('#autoUpdate1').fadeIn('slow');
document.getElementById('autoUpdate2').style.display = "none";
}
else {
$('#autoUpdate1').fadeOut('slow');
document.getElementById('autoUpdate2').style.display = "block";
}

});
});

$(document).ready(function () {
                $('#good').on('change', function () {
                let id = $(this).val();
                $('#products').empty();
                $('#products').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                type: 'GET',
                url: 'good/' + id,
                success: function (response) {
                var response = JSON.parse(response);
                console.log(response);   
                $('#products').empty();
        
                response.forEach(element => {
                    $('#products').append(`<option value="${element['id']}">${element['goodname']}</option>`);
                    });
                }
            });
        });
    });

    $(document).ready(function () {
        $('.add-to-cart').click(function (e) {
            e.preventDefault();

            $.ajaxSetup({
              contentType: "application/x-www-form-urlencoded;charset=utf-8",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    
                }
            });
            var product_id = $("#products option:selected" ).val();
            var product_name = $("#products option:selected" ).text();
            var quantity = $('.qty-input').val();

            $.ajax({
                url: "/add-to-cart",
                method: "POST",
           
                data: {
                    'quantity': quantity,
                    'product_id': product_id,
                    'product_name': product_name,
                },
       
                success: function (response) {
                  console.log(response);
                  //window.location.reload();
                  $("#cart_details").html(response);                   
                  cartloadDetails();
                },
            });
        });
    });
    function cartloadDetails()
    {
        $.ajaxSetup({     
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });
        $.ajax({
            url: '/load-cart-details',
            method: "GET",
            
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            success: function (response) {
              $("#cart_details").html(response); 
            }
        });
    }
</script>
@endsection