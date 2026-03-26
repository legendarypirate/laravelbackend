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
                                <h3 class="card-title">Бараа орлогодох</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            {!! Form::open([
                                'url' => 'good/add',
                                'method' => 'post',
                                'role' => 'form',
                                'files' => true,
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="card-body">

                                @if (auth()->user()->role != 'customer')
                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Дэлгүүр / Агуулах <code></code></label>
                                        <select class="custom-select rounded-0" name="shop" id="deliver">
                                            <?php $user = DB::table('users')
                                                ->where('role', '=', 'customer')
                                                ->get(); ?>
                                            @foreach ($user as $users)
                                                <option value="">Дэлгүүр сонгох</option>

                                                <option value="{{ $users->name }}">{{ $users->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Дэлгүүрийн нэр</label>
                                        <input type="text" name="shop" class="form-control"
                                            value="{{ auth()->user()->name }}" placeholder="good" disabled>
                                    </div>
                                @endif


                                <div class="form-group">
                                    <label for="exampleInputEmail1">Барааны нэр</label>
                                    <select class="custom-select rounded-0" aria-label=".form-select-lg example"
                                        name="goodid" id="field">
                                        <?php $goods = DB::table('goods')
                                            ->where('user_id', auth()->user()->id)
                                            ->get(); ?>
                                        <option value="">Бүгд</option>
                                        @foreach ($goods as $g)
                                            <option value="{{ $g->id }}">{{ $g->goodname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Төрөл</label>
                                    <select class="custom-select rounded-0" aria-label=".form-select-lg example"
                                        name="type">
                                        <option value="1">Орлого</option>
                                        <option value="2">Зарлага</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Тоо</label>
                                    <input type="number" name="count" class="form-control" placeholder="Тоо ширхэг">
                                </div>

                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Хадгалах</button>
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
        $(document).ready(function() {
            $('#deliver').on('change', function() {
                let name = $(this).val();
                $('#field').empty();
                $('#field').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'good/' + name,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#field').empty();

                        response.forEach(element => {
                            $('#field').append(
                                `<option value="${element['id']}">${element['goodname']}</option>`
                            );
                        });
                    }
                });
            });
        });
    </script>

    @include('sweetalert::alert')

@endsection
