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
                                <h3 class="card-title">Үнэ засах</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            {!! Form::open([
                                'url' => 'setting/edit',
                                'method' => 'post',
                                'role' => 'form',
                                'files' => true,
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="card-body">
                                <?php
                                if ($user['type'] == '1') {
                                    $data = 'Энгийн';
                                } elseif ($user['type'] == '2') {
                                    $data = 'Цагтай';
                                } elseif ($user['type'] == '3') {
                                    $data = 'Яаралтай';
                                } elseif ($user['type'] == '4') {
                                    $data = 'Онц яаралтай';
                                } else {
                                    $data = 'Татан авалт';
                                } ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Төрөл</label>
                                    <input type="text" name="type" class="form-control" value="{{ $data }}"
                                        placeholder="Төрөл" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Үнэ</label>
                                    <input type="text" value="{{ $user['price'] }}" name="price" class="form-control"
                                        placeholder="Үнэ">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Жолооч</label>
                                    <input type="text" name="driver" class="form-control" value="{{ $user['driver'] }}"
                                        placeholder="Жолооч">
                                </div>

                                <input value="{{ $user['id'] }}" name="settingId" type="hidden">

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
