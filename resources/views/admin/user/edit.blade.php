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
                                <h3 class="card-title">Хэрэглэгч засах</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            {!! Form::open([
                                'url' => 'user/edit',
                                'method' => 'post',
                                'role' => 'form',
                                'files' => true,
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Нэр</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user['name'] }}"
                                        placeholder="Нэр" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Нууц үг</label>
                                    <input type="password" name="password" class="form-control" placeholder="Нууц үг">
                                </div>


                                <div class="form-group">
                                    <label for="address">Энгийн</label>
                                    <input type="text" class="form-control" name="engiin" value="{{ $user['engiin'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Цагтай</label>
                                    <input type="text" class="form-control" name="tsagtai"
                                        value="{{ $user['tsagtai'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Яаралтай</label>
                                    <input type="text" class="form-control" name="yaraltai"
                                        value="{{ $user['yaraltai'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Онц яаралтай</label>
                                    <input type="text" class="form-control" name="ontsYaraltai"
                                        value="{{ $user['onts_yaraltai'] }}">
                                </div>

                                @if ($user['role'] == 'driver')
                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Бүс <code></code></label>
                                        <select class="custom-select rounded-0" name="region" onchange="yesnoCheck(this);">
                                            <?php $regions = DB::table('regions')->get(); ?>
                                            <option value="">Бүс сонгох</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->name }}"
                                                    @if ($region->name == $user['region']) selected="selected" @endif>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Утас</label>
                                    <input type="text" name="phone" class="form-control" placeholder="Phone"
                                        id="phone">
                                    <a class="flex items-center text-theme-9 add-phone-cart" href="#"
                                        style="float:right; margin-top:8px;">
                                        Нэмэх </a>
                                </div>
                                <div class="row" id="cart_details">
                                    @if (isset($cart_data))
                                        @if (Cookie::get('phone_cart'))
                                            @php $total="0" @endphp
                                            <div class="col-md-7 ms-auto">
                                                <div class="cart-page-header">
                                                    <h4 class="cart-page-header-title">Утасны жагсаалт</h4>
                                                </div>
                                                <div class="d-flex flex-column gap-3">

                                                    @foreach ($cart_data as $data)
                                                        <label class="order-card col-12 cartpage" data-cart-item-id="123"
                                                            data-product-id="12">
                                                            <!-- <input class="order-card__input" type="checkbox" checked /> -->
                                                            <div class="order-card__body">

                                                                <input type="hidden" class="product_id"
                                                                    value="{{ $data['item_id'] }}">
                                                                <div class="product-row">

                                                                    <div class="product-row__content">
                                                                        <h2 class="product-row__content-title">
                                                                            <div style="width:200px;">
                                                                                {{ urldecode($data['item_name']) }}
                                                                            </div>
                                                                            <div style="display:inline;">тоо:
                                                                                {{ number_format($data['item_quantity']) }}
                                                                            </div>
                                                                            <div style="display:inline;margin-left:50px;">
                                                                                Үнэ:
                                                                                {{ number_format($data['item_price']) }}
                                                                            </div>
                                                                        </h2>
                                                                        <div class="product-row__tally--price--amount"
                                                                            style="display:inline;"> </div>
                                                                        <div class="product-row__content-author">
                                                                        </div>
                                                                    </div>
                                                                    <div class="product-row__tally" style="display:inline;">
                                                                        <div class="product-row__tally--price">


                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>


                                            <div class="col-md-3 me-auto">


                                            </div>
                                        @endif
                                    @else
                                        <div class="row">
                                            <div class="col-md-12 mycard py-5 text-center">
                                                <div class="mycards">
                                                    <h6>Та одоогоор утас бүртгээгүй байна</h6>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Хаяг</label>
                                    <input type="text" name="address" class="form-control" placeholder="Address"
                                        id="address">
                                    <a class="flex items-center text-theme-9 add-address-cart" href="#"
                                        style="float:right; margin-top:8px;">
                                        Нэмэх </a>
                                </div>
                                <div class="row" id="cart_details_add">

                                    @if (isset($cart_data1))
                                        @if (Cookie::get('address_cart'))
                                            @php $total="0" @endphp
                                            <div class="col-md-7 ms-auto">
                                                <div class="cart-page-header">
                                                    <h4 class="cart-page-header-title">Хаягийн жагсаалт</h4>
                                                </div>
                                                <div class="d-flex flex-column gap-3">

                                                    @foreach ($cart_data1 as $data)
                                                        <label class="order-card col-12 cartpage" data-cart-item-id="123"
                                                            data-product-id="12">
                                                            <!-- <input class="order-card__input" type="checkbox" checked /> -->
                                                            <div class="order-card__body">

                                                                <input type="hidden" class="product_id"
                                                                    value="{{ $data['item_id'] }}">
                                                                <div class="product-row">

                                                                    <div class="product-row__content">
                                                                        <h2 class="product-row__content-title">
                                                                            <div style="width:200px;">
                                                                                {{ urldecode($data['item_name']) }}
                                                                            </div>
                                                                            <div style="display:inline;">тоо:
                                                                                {{ number_format($data['item_quantity']) }}
                                                                            </div>
                                                                            <div style="display:inline;margin-left:50px;">
                                                                                Үнэ:
                                                                                {{ number_format($data['item_price']) }}
                                                                            </div>
                                                                        </h2>
                                                                        <div class="product-row__tally--price--amount"
                                                                            style="display:inline;"> </div>
                                                                        <div class="product-row__content-author">
                                                                            x
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


                                            </div>
                                        @endif
                                    @else
                                        <div class="row">
                                            <div class="col-md-12 mycard py-5 text-center">
                                                <div class="mycards">
                                                    <h6>Та одоогоор хаяг бүртгээгүй байна.</h6>

                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                </div>




                                <input value="{{ $user['id'] }}" name="userId" type="hidden">

                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary cc">Хадгалах</button>
                            </div>
                            {!! Form::close() !!}
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
        function cartloadDetailsPhone() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                url: '/load-phone-details',
                method: "GET",

                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                success: function(response) {
                    $("#cart_details").html(response);
                }
            });
        }

        $(document).ready(function() {
            $('.add-phone-cart').click(function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }
                });
                var phone = $("#phone").val();

                $.ajax({
                    url: "/add-phone-cart",
                    method: "POST",

                    data: {
                        'phone': phone,
                    },

                    success: function(response) {
                        console.log(response);
                        //window.location.reload();
                        $("#cart_details").html(response);
                        cartloadDetailsPhone();
                    },
                });
            });
        });

        $(document).ready(function() {
            $('.cc').click(function(e) {
                $.ajax({
                    url: '/clear-cart',
                    type: 'GET',
                    success: function(response) {
                        alertify.set('notifier', 'position', 'top-right');
                        alertify.success(response.status);
                    }
                });
            });
        });


        $(document).ready(function() {
            $('.add-address-cart').click(function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }
                });
                var address = $("#address").val();

                $.ajax({
                    url: "/add-address-cart",
                    method: "POST",

                    data: {
                        'address': address,
                    },

                    success: function(response) {
                        console.log(response);
                        //window.location.reload();
                        $("#cart_details_add").html(response);
                        cartloadDetailsAdd();
                    },
                });
            });
        });

        function cartloadDetailsAdd() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                url: '/load-address-details',
                method: "GET",

                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                success: function(response) {
                    $("#cart_details_add").html(response);
                }
            });
        }
    </script>
@endsection
