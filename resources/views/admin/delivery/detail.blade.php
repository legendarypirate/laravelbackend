@extends('admin.master')

@section('mainContent')
    <style>
        .zoom {
            -webkit-transition: all 0.35s ease-in-out;
            -moz-transition: all 0.35s ease-in-out;
            transition: all 0.35s ease-in-out;
            cursor: -webkit-zoom-in;
            cursor: -moz-zoom-in;
            cursor: zoom-in;
        }

        .zoom:hover,
        .zoom:active,
        .zoom:focus {
            /**adjust scale to desired size,
                    add browser prefixes**/
            -ms-transform: scale(5);
            -moz-transform: scale(5);
            -webkit-transform: scale(5);
            -o-transform: scale(5);
            transform: scale(5);
            position: relative;
            z-index: 100;
        }
    </style>
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
                                <h3 class="card-title"> Илгээгчийн мэдээлэл оруулах хэсэг (Хаанаас ямар илгээмж авах тухай
                                    мэдээллээ энд бөглөнө үү)</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleSelectRounded0">Салбарын нэр
                                        <code></code></label>
                                    {!! Form::open([
                                        'url' => 'delivery/edit',
                                        'method' => 'post',
                                        'role' => 'form',
                                        'files' => true,
                                        'enctype' => 'multipart/form-data',
                                    ]) !!}
                                    <select class="custom-select rounded-0" name="merchant_id" id="merchantsRequest">
                                        <?php
                                        
                                        $merchant = DB::table('merchant')->get(); ?>
                                        <option value="">Салбар-мерчант сонгох</option>
                                        @foreach ($merchant as $merchants)
                                            <option value="{{ $merchants->id }}"
                                                @if ($merchants->id == $merchant_info['id']) selected="selected" @endif>
                                                {{ $merchants->merchantName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Утас</label>
                                    <select class="custom-select rounded-0" aria-label=".form-select-lg example"
                                        name="phone" id="merchantPhone1">
                                        <option>{{ $merchant_info['merchantPhone1'] }}</option>
                                    </select>


                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Утас</label>
                                    <select class="custom-select rounded-0" aria-label=".form-select-lg example"
                                        name="phone" id="merchantPhone2">
                                        <option>{{ $merchant_info['merchantPhone2'] }}</option>
                                    </select>

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Хаяг</label>

                                    {{-- <input type="text" id="merchantAddress" name="merchantAddress" class="form-control"
                                         > --}}
                                    <select class="custom-select rounded-0" aria-label=".form-select-lg example"
                                        name="address" id="merchantAddress" readonly>
                                        <option>{{ $merchant_info['merchantAddress'] }}</option>

                                    </select>

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Илгээмжийн мэдээлэл</label>
                                    <input type="text" name="parcel_info" class="form-control"
                                        value="{{ $list['parcel_info'] }}"
                                        placeholder="Илгээмжийн нэр төрөл, тоо ширхэг, барааны код, сериал дугаар, овор хэмжээ болон очиж авахдаа анхаарах зүйлсийг бичнэ үү   ">

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Захиалгын код</label>
                                    <input type="text" name="order_code" class="form-control"
                                        value="{{ $list['order_code'] }}"
                                        placeholder="Та өөрийн ( дэлгүүрийн) захилагын кодоо оруулж өгнө үү (Нэг хүлээн авачид олон бараа хүргэх бол)">

                                </div>
                                <div class="form-group">
                                    <label for="exampleSelectRounded0">Татан авалтын цаг сонгох<code></code></label>
                                    <select class="custom-select rounded-0" name="download_time" id="deliver">
                                        <option value="Өглөө" @if ($list['download_time'] == 'Өглөө') selected="selected" @endif>
                                            Өглөө (08-14 цаг)</option>
                                        <option value="Орой">Үдээс хойш (14-20 цаг)</option>
                                        <option value="Орой">Орой (20-00 цаг)</option>

                                    </select>
                                </div>
                            </div>

                            <div class="card-header">
                                <h3 class="card-title"> Хүлээн авагчийн мэдээлэл бөглөх хэсэг</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleSelectRounded0">Дэлгүүр <code></code></label>
                                    <div class="form-control">{{ $list['shop'] }}</div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Утас</label>
                                    <input class="form-control" name="phone" value="{{ $list['phone'] }}">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Хүлээн авагчийн нэр</label>
                                    <input class="form-control" name="receivername" value="{{ $list['receivername'] }}">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Хаяг</label>
                                    <input class="form-control" name="address" value="{{ $list['address'] }}">

                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Тайлбар</label>
                                    <input class="form-control" name="comment" value="{{ $list['comment'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleSelectRounded0">Төрөл <code></code></label>
                                    <select class="custom-select rounded-0" name="type" onchange="yesnoCheck(this);">
                                        <option value="1" @if (1 == $list['type']) selected="selected" @endif>
                                            Энгийн</option>
                                        <option value="2" @if (2 == $list['type']) selected="selected" @endif>
                                            1-2 хайрцаг</option>
                                        <option value="3" @if (3 == $list['type']) selected="selected" @endif>
                                            3-6 хайрцаг</option>
                                        <option value="4" @if (4 == $list['type']) selected="selected" @endif>
                                            6-10 хайрцаг</option>
                                        <option value="6" @if (4 == $list['type']) selected="selected" @endif>
                                            10-аас дээш бөөний</option>
                                    </select>
                                </div>

                                @if (auth()->user()->role != 'customer')
                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Бүс <code></code></label>
                                        <select class="custom-select rounded-0" name="region"
                                            onchange="yesnoCheck(this);">
                                            <?php $regions = DB::table('regions')->get(); ?>
                                            <option value="">Бүс сонгох</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->name }}"
                                                    @if ($region->name == $list['region']) selected="selected" @endif>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group" id="ifYes" style="display: none;">
                                    <div style="color:green;"> Яаралтай хүргэлт нь 1-2 цагийн дотор хүргэгдэх бөгөөд нэмэлт
                                        төлбөртэй болохыг анхаарна уу. </div>
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

                                    </div>
                                    <div class="autoUpdate1" id="autoUpdate1" style="display:none;">

                                        <div class="mt-3 ">
                                            <label for="regular-form-3" class="form-label proddata">Барааны нэр
                                            </label><br>
                                            <select id="products"
                                                class="form-control formselect sm:mt-2 sm:mr-2 js-example-basic-single"
                                                aria-label=".form-select-lg example" name="size" style="width:600px;">

                                                <?php
                                                if (Auth::user()->role == 'Customer') {
                                                    $good = DB::table('goods')
                                                        ->where('custname', Auth::user()->name)
                                                        ->orderBy('goodname', 'ASC')
                                                        ->get();
                                                } else {
                                                    $good = DB::table('goods')->get();
                                                }
                                                ?>
                                                <option class="" value="#">Бараа сонгоно уу</option>

                                                @foreach ($good as $goods)
                                                    <option class="product_id" value="{{ $goods->id }}">
                                                        {{ $goods->goodname }}</option>
                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="mt-3">
                                            <label for="input-state-3" class="form-label">Тоо ширхэг</label>
                                            <input id="input-state-3" type="number"
                                                class="qty-input form-control border-danger"
                                                placeholder="Баглаа боодлын тоо" name="number">

                                        </div>
                                        <div class="pro-action">
                                            <button type="button" class="add-to-cart btn btn-primary mt-5"><span> Сагсанд
                                                    нэмэх</span> </button>
                                            <div class="row" id="cart_details">

                                                @if (isset($cart_data))
                                                    @if (Cookie::get('shopping_cart'))
                                                        @php $total="0" @endphp
                                                        <div class="col-md-7 ms-auto">
                                                            <div class="cart-page-header">
                                                                <h6 class="cart-page-header-title">Order list</h6>
                                                            </div>
                                                            <div class="d-flex flex-column gap-3">

                                                                @foreach ($cart_data as $data)
                                                                    <label class="order-card col-12 cartpage"
                                                                        data-cart-item-id="123" data-product-id="12">
                                                                        <!-- <input class="order-card__input" type="checkbox" checked /> -->
                                                                        <div class="order-card__body">

                                                                            <input type="hidden" class="product_id"
                                                                                value="{{ $data['item_id'] }}">
                                                                            <div class="product-row">

                                                                                <div class="product-row__content">
                                                                                    <h6 class="product-row__content-title">
                                                                                        <div style="width:200px;">
                                                                                            {{ urldecode($data['item_name']) }}
                                                                                        </div>
                                                                                        <div style="display:inline;">тоо:
                                                                                            {{ number_format($data['item_quantity']) }}
                                                                                        </div>
                                                                                        <div
                                                                                            style="display:inline;margin-left:50px;">
                                                                                            Үнэ:
                                                                                            {{ number_format($data['item_price']) }}
                                                                                        </div>
                                                                                    </h6>
                                                                                    <div class="product-row__tally--price--amount"
                                                                                        style="display:inline;"> </div>
                                                                                    <div
                                                                                        class="product-row__content-author">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-row__tally"
                                                                                    style="display:inline;">
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
                                                                        <div class="total-price">₮
                                                                            {{ number_format($total, 2) }}</div>
                                                                    </div>
                                                                </div>
                                                                @auth


                                                                @endauth
                                                                @guest
                                                                    ы

                                                                    <div class="d-grid mt-3"><a href="#"><button
                                                                                class="btn btn-success btn-lg rounded-0"
                                                                                type="button" data-bs-toggle="modal"
                                                                                data-bs-target="#modalLogin"
                                                                                aria-controls="loginToSystem"
                                                                                aria-logedin="false"
                                                                                aria-label="Signin">Purchase</button></a></div>
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
                                        <input type="hidden" class="qty-input" value="1">
                                        <!-- Your Number of Quantity -->

                                    </div>


                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Тооцоо</label>
                                    <input class="form-control" name="price" value="{{ $list['price'] }}">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Баглаа боодлын тоо</label>
                                    <input class="form-control" name="number" value="{{ $list['number'] }}">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Барааны төрөл</label>
                                    <div class="form-control">{{ $list['type'] }}</div>
                                </div>


                                <div class="form-group">
                                    <label for="exampleSelectRounded0">Овор <code></code></label>
                                    <select class="custom-select rounded-0" id="exampleSelectRounded0" name="size">
                                        <option value="1-5 kg"
                                            @if ($list['size'] == '1-5 kg') selected="selected" @endif>1-5 kg</option>
                                        <option value="5-10 kg"
                                            @if ($list['size'] == '5-10 kg') selected="selected" @endif>5-10 kg</option>
                                    </select>

                                    {{-- <div class="form-control">{{ $list['size'] }}</div> --}}

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Илгээмжийн зураг</label>
                                    <div class="text-center image-container">
                                        <style>
                                            .image-upload>input {
                                                display: none;
                                            }
                                        </style>



                                        @php
                                            $imagesArray = explode('|', $list['image']);
                                        @endphp




                                        <div class="container">
                                            <div class="row">
                                                @foreach ($imagesArray as $imagePath)
                                                    <div class="col-md-3">
                                                        <img class="zoom" id="imageDisplay"
                                                            style="max-width: 100%; height: 100px;"
                                                            src="{{ asset('storage/' . $imagePath) }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="row">
                                                @foreach ($imagesArray as $imagePath)
                                                    <div class="col-md-3">
                                                        <img class="zoom" id="imageDisplay"
                                                            style="max-width: 100%; height: 100px;"
                                                            src="{{ asset('storage/app/public/signImage' . $imagePath) }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="image-upload">
                                            <label for="file-input">
                                                <img src="https://png.pngtree.com/png-vector/20190425/ourmid/pngtree-camera-icon-vector-illustration-in-line-style-for-any-purpose-png-image_989668.jpg"
                                                    width="30" />
                                                Зураг солих</label>
                                            <input id="file-input" type="file" name="image" />
                                        </div>
                                    </div>
                                </div>
                                @if (auth()->user()->role != 'customer')
                                    @if (!empty($list['driver_request']))
                                        <div class="form-group">
                                            <label for="exampleSelectRounded0">Жолоочид хувиарлах<code></code></label>

                                            @php
                                                $elements = explode(',', $list['driver_request']);
                                                $elements = array_map('trim', $elements);

                                            @endphp


                                            @foreach ($elements as $e)
                                                <div class="form-group">
                                                    <input type="radio" name="driver_request" id="driver_request"
                                                        value="{{ $e }}">
                                                    <label class="exampleSelectRounded0"
                                                        for="driver_request">{{ $e }}</label>
                                                </div>
                                            @endforeach

                                        </div>
                                    @endif
                                @endif
                            </div>
                            <!-- /.card-body -->

                        </div>
                        <input class="form-control" name="zurag" type="hidden" value="{{ $list['image'] }}">
                        <input class="form-control" name="delId" type="hidden" value="{{ $list['id'] }}">

                        <!-- general form elements disabled -->
                        @if ($list['verified'] == 0)
                            <div class="card-footer" style="display: flex; align-items: center; justify-content: right;">
                                <button type="submit" class="btn btn-primary">Хадгалах</button>
                            </div>
                        @endif
                        <!-- /.card -->
                        <!-- general form elements disabled -->

                        <!-- /.card -->
                    </div>

                    <!--/.col (right) -->
                </div>


                <!-- /.row -->
            </div><!-- /.container-fluid -->


            {!! Form::close() !!}
        </section>
        <!-- /.content -->
    </div>
    <script>
        const imageInput = document.getElementById('file-input');
        const imageDisplay = document.getElementById('imageDisplay');

        imageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageDisplay.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                imageDisplay.src = '';
            }
        });

        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantPhone1').empty();
                $('#merchantPhone1').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantPhone1').empty();

                        response.forEach(element => {
                            $('#merchantPhone1').append(
                                `<option value="${element['merchantPhone1']}">${element['merchantPhone1']}</option>`
                            );
                        });
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantPhone2').empty();
                $('#merchantPhone2').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantPhone2').empty();

                        response.forEach(element => {
                            $('#merchantPhone2').append(
                                `<option value="${element['merchantPhone2']}">${element['merchantPhone2']}</option>`
                            );
                        });
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantAddress').empty();
                $('#merchantAddress').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantAddress').empty();

                        response.forEach(element => {
                            $('#merchantAddress').append(
                                `<option value="${element['merchantAddress']}">${element['merchantAddress']}</option>`
                            );
                        });
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#checkbox2').change(function() {
                if (this.checked) {
                    $('#autoUpdate1').fadeIn('slow');
                    document.getElementById('autoUpdate2').style.display = "none";
                } else {
                    $('#autoUpdate1').fadeOut('slow');
                    document.getElementById('autoUpdate2').style.display = "block";
                }

            });
        });

        $(document).ready(function() {
            $('#good').on('change', function() {
                let id = $(this).val();
                $('#products').empty();
                $('#products').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'good/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#products').empty();

                        response.forEach(element => {
                            $('#products').append(
                                `<option value="${element['id']}">${element['goodname']}</option>`
                            );
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
        $(document).ready(function() {
            $('.add-to-cart').click(function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }
                });
                var product_id = $("#products option:selected").val();
                var product_name = $("#products option:selected").text();
                var quantity = $('.qty-input').val();

                $.ajax({
                    url: "/add-to-cart",
                    method: "POST",

                    data: {
                        'quantity': quantity,
                        'product_id': product_id,
                        'product_name': product_name,
                    },

                    success: function(response) {
                        console.log(response);
                        //window.location.reload();
                        $("#cart_details").html(response);
                        cartloadDetails();
                    },
                });
            });
        });

        function cartloadDetails() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                url: '/load-cart-details',
                method: "GET",

                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                success: function(response) {
                    $("#cart_details").html(response);
                }
            });
        }
    </script>
@endsection
