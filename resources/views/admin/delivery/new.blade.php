@extends('admin.master')

@section('mainContent')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css"
        integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg=="
        crossorigin="anonymous" />
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.8.1/bootstrap-table.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.8.1/bootstrap-table.min.js"></script> --}}

    <style>
        [type=search] {
            -webkit-appearance: textfield;
            outline-offset: -2px;
            border: 1px solid #a6bcce;
            padding: 8px;
            border-radius: 10px;
        }



        /* The Modal (background) */
        .modal-custom {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 99999999;
            /* Sit on top */
            padding-top: 10%;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }



        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        h6::after {
            content: ' ' counter(checked);
        }


        input[type=checkbox]:checked {
            counter-increment: checked;
        }

        #print_wrapper .table th {
            padding: 0.75rem 1.25rem;
            border: 1px solid;
            font-weight: 700;
        }

        #print_wrapper .table td {
            padding: 0.75rem 1.25rem;
            border: 1px solid;
        }

        @media print {

            .table thead tr td,
            .table tbody tr td {
                border-width: 1px;
                border-style: solid;
                border-color: black;
                font-size: 10px;
                background-color: red;
                padding: 0px;
                -webkit-print-color-adjust: exact;
            }
        }

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            position: relative;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .gallery {
            margin-left: 3vw;
            margin-right: 3vw;
        }

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
                    <div class="col-sm-12">
                        <div class="card">
                            <h5 class="card-header">Шинэ хүргэлтийн жагсаалт</h5>
                        </div>
                    </div>
                </div>
                @if (session('message'))
                    <!-- The modal -->
                    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" style="background-color: #4CAF50; color: #fff;">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="alertModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body ">
                                    {{ session('message') }}
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Trigger the modal automatically when the page loads -->
                    <script>
                        $(document).ready(function() {
                            $('#alertModal').modal('show');
                        });
                    </script>
                @endif
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">

                <!-- /.row -->
                <div class="row">
                    <div class="col-12">

                        <button type="button" class="btn btn-warning float-sm-right"> <a href="index"
                                style="color:white;text-decoration:none;"><i class=" fa fa-plus"
                                    style="margin-right:10px"></i>Шинэ
                                хүргэлт
                                үүсгэх</a></button>
                        <button type="button" id="__btnPrint" class="btn btn-primary" style="">
                            <a href="#" style="color:white;"><i class=" fa fa-print"
                                    style="margin-right:10px"></i>Хэвлэх</a></button>
                        <button type="button" id="__btnPrintZarlaga" class="btn btn-success" style="">
                            <a href="#" style="color:white;"><i class=" fa fa-print"
                                    style="margin-right:10px"></i>Зарлагын баримт</a></button>
<button type="button" id="__btnPrintInvoice" class="btn btn-success" style="">
                            <a href="#" style="color:white;"><i class=" fa fa-print"
                                    style="margin-right:10px"></i>Нэхэмжлэх үүсгэх</a></button>
                        <button type="button" id="__btnExcelExport" class="btn btn-primary"> <a href="#"
                                style="color:white;"><i class="fa fa-excel" style="margin-right:10px"></i>Экселээр
                                гаргаж
                                авах</a></button>
                        <button type="button" id="__btnImportExcel" class="btn btn-primary"> <a href="#"
                                style="color:white;"><i class="fa fa-excel" style="margin-right:10px"></i>Экселээс
                                оруулах</a></button>
                        <button type="button" class="btn btn-primary" style="
;"> <a
                                href="https://e-chuchu.mn/dist/Excel_blank_delivery.xlsx" style="color:white;"><i
                                    class="fa fa-excel" style="margin-right:10px"></i>Экселийн
                                Загвар татаж
                                авах</a></button>

                        <div class="row" style="margin-bottom:15px">

                            @if (auth()->user()->role != 'customer')
                                <div class="form-group myform">
                                    <label for="status">Бүс:</label>
                                    <select id="filterByBus" class="form-control inputStatus9">
                                        <?php $bus = DB::table('regions')->get(); ?>
                                        <option value="">Бүгд</option>
                                        @foreach ($bus as $region)
                                            <option value="{{ $region->name }}">{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group myform">
                                    <label for="status">Харилцагч:</label>
                                    <select id="filterByCustomer" class="form-control inputStatus9">
                                        <?php $shop = DB::table('users')->where('role', 'customer')->where('active',1)->get(); ?>
                                        <option value="">Бүгд</option>
                                        @foreach ($shop as $shops)
                                            <option value="{{ $shops->name }}">{{ $shops->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if (auth()->user()->role == 'customer')
                                <div class="form-group myform">
                                    <label for="status">Мерчант:</label>
                                    <select id="filterByCustomerMerchant" class="form-control inputStatus9">
                                        <?php $merchants = DB::table('merchant')
                                            ->where('user_id', auth()->user()->id)
                                            ->get(); ?>
                                        <option value="">Бүгд</option>
                                        @foreach ($merchants as $m)
                                            <option value="{{ $m->id }}">{{ $m->merchantName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group myform">
                                    <label for="status">Төрөл:</label>
                                    <select id="filterByCustomerType" class="form-control inputStatus9">
                                        <option value="">Бүгд</option>
                                        <option value="1">Энгийн</option>
                                        <option value="2">Цагтай</option>
                                        <option value="3">Яаралтай</option>
                                        <option value="4">Онц яаралтай</option>
                                        <option value="6">Бөөний хүргэлт</option>
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="card ">
                            <div class="card-header">
                                <!-- /.card-header -->
                                <div class="card-body table-responsive table-container  p-0 ">
                                    <table class="table table-hover text-nowrap display nowrap small" id="datatable"
                                        data-toggle="table" data-show-refresh="true" data-show-toggle="true"
                                        data-show-columns="true">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap"> <input type="checkbox"
                                                        style="width:20px;height:20px;"
                                                        onClick="toggle(this);updateCount();" /></th>
                                                <th class="text-center whitespace-nowrap table-info">Track ID</th>
                                                <th class="text-center whitespace-nowrap table-info">Огноо</th>
                                               
                                                <th class="text-center whitespace-nowrap table-warning">Хүлээн авагч хаяг
                                                <th class="text-center whitespace-nowrap table-warning">
                                                    Барааны мэдээлэл
                                                </th>

                                                <th class="text-center whitespace-nowrap table-warning">Тоо ширхэг</th>
                                                <th class="text-center whitespace-nowrap table-danger">Хүлээн авагчийн нэр
                                                </th>
                                                <th class="text-center whitespace-nowrap table-danger">Утас 1, Утас 2</th>

                                                </th>
                                                <th class="text-center whitespace-nowrap table-danger">Нэмэлт тайлбар
                                                    (ирэхээсээ өмнө залгах г.м.)</th>
                                                     @if (auth()->user()->role != 'customer')
                                                    <th class="text-center whitespace-nowrap table-info">Бүс</th>
                                                @endif
                                                <th class="text-center whitespace-nowrap table-warning">Төрөл</th>
                                                <th class="text-center whitespace-nowrap table-warning">Харилцагч</th>
                                                <th class="text-center whitespace-nowrap table-warning">z-код
                                                </th>
                                                <th class="text-center whitespace-nowrap table-info">Нэхэмжлэлийн дугаар</th>
                                                <th class="text-center whitespace-nowrap table-info">Нэхэмжлэлийн огноо</th>
                                                <th class="text-center whitespace-nowrap table-info">Харилцагчийн РД</th>
                                                <th class="text-center whitespace-nowrap table-info">Харилцагчийн имэйл</th>
                                                <th class="whitespace-nowrap table-warning">Мерчант нэр
                                                </th>
                                                <th class="text-center whitespace-nowrap table-warning">Утас 1, Утас 2</th>
                                                <th class="text-center whitespace-nowrap table-warning">Илгээгчийн хаяг
                                                    [дэлгэрэнгүй]
                                                </th>
                                                <th class="text-center whitespace-nowrap table-success">Барааны тооцоо</th>
                                                <th class="text-center whitespace-nowrap">Баталгаажилт</th>
                                                <th class="text-center whitespace-nowrap">Жолооч</th>
                                                <th class="text-center whitespace-nowrap table-success">Төлөв</th>
                                                <th class="text-center whitespace-nowrap table-success">Үйлдэл</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->

                                <div style="position:fixed;bottom:20px;">
                                    <button class="btn btn-primary shadow-md mr-2"
                                        style="background-color:#7B22FC
;color:white;"><span id="y">0 </span>
                                    </button>
                                    <form style="float:left" class="animate mr-2"
                                        action="{{ route('delivery.bulkQRPrint') }}" method="post">
                                        @csrf
                                        <input type="hidden" id="selectedIds" name="ids" value="">
                                        <input type="submit" name="submit" class="btn btn-default"
                                            style="background-color:#7B22FC
;color:white;" value="QR шошго хэвлэх">
                                    </form>

                                    <button type="button" class="btn btn-default" id="btnStatusModal"
                                        style="background-color:#7B22FC
;color:white;">Төлөв солих</button>

                                    @if (auth()->user()->role != 'customer')
                                        <button type="button" class="btn btn-default" id="btnBusModal"
                                            style="background-color:#7B22FC
            ;color:white;">Бүс солих</button>
                                        <button type="button" class="btn btn-default" id="btnDriverModal"
                                            style="background-color:#7B22FC
            ;color:white;">Жолооч
                                            солих</button>
                                        <button type="button" class="btn btn-default" id="btnVerifyModal"
                                            style="background-color:#7B22FC
            ;color:white;">Баталгаажуулах</button>
                                        <button type="button" class="btn btn-default" id="btnDeleteModal"
                                            style="background-color:#7B22FC
            ;color:white;">Устгах</button>
                                    @endif
                                </div>

                            </div>

                            <div id="customModal" class="modal-custom">

                                <!-- Modal content -->
                                <div class="modal-content">
                                    <span class="closing">&times;</span>
                                    <div id="excel-wrapper">......</div>
                                </div>
                            </div>
                            @if (auth()->user()->role != 'customer')
                                <div id="statusModal" class="modal modal-dialog-centered text-center"
                                    style="display:none;">
                                    <div class="modal-content text-center"
                                        style="width:400px !important;height:250px !important;margin-top:200px;">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Төлөв солих</h4>
                                        </div>
                                        <div class="modal-body">
                                            <select class="form-control inputStatus1">
                                                <option value="1">Бүртгэгдсэн</option>
                                                <option value="3">Хүргэгдсэн</option>
                                                <option value="4">Цуцалсан</option>
                                                <option value="5">Буцаасан</option>
                                                <option value="6">Хүлээгдэж буй</option>
                                                <option value="10">Хүлээн авсан</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default closing"
                                                data-dismiss="modal">Close</button>
                                            <button type="button"
                                                class="btn btn-primary btn_change_status">Солих</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="statusModal" class="modal modal-dialog-centered text-center"
                                    style="display:none;">
                                    <div class="modal-content text-center"
                                        style="width:400px !important;height:250px !important;margin-top:200px;">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Төлөв солих</h4>
                                        </div>
                                        <div class="modal-body">
                                            <select class="form-control inputStatus1">

                                                <option value="4">Цуцалсан</option>
                                                <option value="5">Буцаасан</option>

                                            </select>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default closing"
                                                data-dismiss="modal">Close</button>
                                            <button type="button"
                                                class="btn btn-primary btn_change_status">Солих</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div id="busModal" class="modal">
                                <div class="modal-content text-center"
                                    style="width:400px !important;height:250px !important;margin-top:200px;">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Бүс солих</h4>
                                    </div>
                                    <div class="modal-body">
                                        <select class="form-control inputStatus3">
                                            <?php $bus = DB::table('regions')->get(); ?>
                                            @foreach ($bus as $region)
                                                <option value="{{ $region->name }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closing"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary btn_change_bus">Солих</button>
                                    </div>
                                </div>
                            </div>

                            <div id="driverModal" class="modal">
                                <div class="modal-content text-center"
                                    style="width:400px !important;height:250px !important;margin-top:200px;">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Жолооч солих</h4>
                                    </div>
                                    <div class="modal-body">
                                        <select class="form-control inputStatus4">
                                            <?php $bus = DB::table('users')->where('role', 'driver')->where('active',1)->get(); ?>
                                            @foreach ($bus as $region)
                                                <option value="{{ $region->name }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closing"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary btn_change_drive">Солих</button>
                                    </div>
                                </div>
                            </div>

                            <div id="deleteModal" class="modal">
                                <div class="modal-content text-center"
                                    style="width:400px !important;height:250px !important;margin-top:200px;">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Устгах</h4>
                                    </div>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closing"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary btn_delete">Устгах</button>
                                    </div>
                                </div>
                            </div>

                            <div id="importModal" class="modal-custom">

                                <!-- Modal content -->
                                <div class="modal-content">
                                    <span class="closing">&times;</span>
                                    <div id="message-wrapper">
                                        <form action="{{ route('excel_import_file') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" id="import_file" name="file" class="form-control">
                                            <br>
                                            <button id="btn_import" class="btn btn-success">Import</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div id="verifyModal" class="modal">
                                <div class="modal-content text-center"
                                    style="width:400px !important;height:250px !important;margin-left:700px;margin-top:200px;">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Баталгаажуулах</h4>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Баталгаажсан:</label>
                                        <select class="form-control inputStatus15">
                                            <option value="1">Тийм</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closing"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary btn_verify">Баталгаажуулах</button>
                                    </div>
                                </div>
                            </div>



                        </div>


                        <div id="print_wrapper" hidden> </div>


                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->

            <!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>



    <script type="text/javascript">
        var rows_selected = [];

        function toggle(source) {
            checkboxes = document.getElementsByName('foo');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;

            }
            checkboxes.forEach((input) => {
                if (input.checked) {
                    rows_selected.push(input.value);
                } else {
                    rows_selected.length = 0;
                }
            });
        }
        $('input[name="foo"]').click(function() {
            document.getElementById("result").textContent = "Total Number of Items Selected = " + document
                .querySelectorAll('input[name="foo"]:checked').length;
        });
    </script>



    <script type="text/javascript">
        var $j = jQuery.noConflict();
        $(document).ready(function($j) {
            const deliveryTableUrl = '{{ route('datatable-delivery') }}';



            // const isCustomer = $user && $user - > role === 'customer';
            const loadDeliveryDataTable = (status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6, not_10,
                not_100, start_date, end_date,
                merchant, type) => {
                var table = $j('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    bDestroy: true,

                    ajax: {
                        type: 'GET',
                        url: deliveryTableUrl,
                        data: {
                            status: status,
                            region: bus,
                            driver: driver,
                            customer: customer,
                            not_2: not_2,
                            not_3: not_3,
                            not_4: not_4,
                            not_5: not_5,
                            not_6: not_6,
                            not_10: not_10,
                            not_100: not_100,
                            start_date: start_date,
                            end_date: end_date,
                            merchant: merchant,
                            type: type,

                        },
                        dataSrc: function(json) {
                            return json.data;
                            console.log(json.data);
                        }
                    },


                    columns: [{
                            name: 'checkbox',
                            data: 'checkbox',
                        },
                        {
                            name: 'track',
                            data: 'track'
                        },

                        {
                            name: 'created_at',
                            data: 'created_at'
                        },

                   
                        {
                            name: 'address',
                            data: 'address'
                        },
                        {
                            name: 'mergedMerchantParcel',
                            data: 'mergedMerchantParcel'
                        },

                        {
                            name: 'number',
                            data: 'number'
                        },
                        {
                            name: 'receivername',
                            data: 'receivername'
                        },

                        {
                            name: 'mergedPhones',
                            data: null,
                            render: function(data, type, row) {
                                if ((row.phone != null) && (row.phone2 != null)) {
                                    var mergedPhone = row.phone + ', ' + row.phone2;
                                } else if ((row.phone === null) && (row.phone2 != null)) {
                                    var mergedPhone = row.phone2;
                                } else if ((row.phone != null) && (row.phone2 === null)) {
                                    var mergedPhone = row.phone
                                }

                                return mergedPhone;
                            },
                            name: 'phone',
                            searchable: true,
                        },


                        {
                            name: 'comment',
                            data: 'comment'
                        },
                             @if (auth()->user()->role != 'customer')
                            {
                                name: 'region',
                                data: 'region'
                            },
                        @endif

                        {
                            name: 'type',
                            data: 'type'
                        },
                        {
                            name: 'shop',
                            data: 'shop'
                        },
                        {
                            name: 'order_code',
                            data: 'order_code'
                        },
                        {
                            name: 'invoice_number',
                            data: 'invoice_number'
                        },
                        {
                            name: 'invoice_date',
                            data: 'invoice_date'
                        },
                        {
                            name: 'customer_register',
                            data: 'customer_register'
                        },
                        {
                            name: 'customer_email',
                            data: 'customer_email'
                        },
                        {
                            name: 'merchantName',
                            data: 'merchantName'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                if ((row.merchantPhone1 != null) && (row.merchantPhone2 !=
                                        null)) {
                                    var mergedPhones = row.merchantPhone1 + ', ' + row
                                        .merchantPhone2;
                                } else if ((row.merchantPhone1 === null) && (row
                                        .merchantPhone2 != null)) {
                                    var mergedPhones = row.merchantPhone2;
                                } else if ((row.merchantPhone1 != null) && (row
                                        .merchantPhone2 === null)) {
                                    var mergedPhones = row.merchantPhone1
                                } else if ((row.merchantPhone1 === null) && (row
                                        .merchantPhone2 === null)) {
                                    var mergedPhones = "Утас алга";
                                }

                                return mergedPhones;
                            },
                            name: 'merchantPhone1',
                            searchable: true,
                        },
                        {
                            name: 'merchantAddress',
                            data: 'merchantAddress'
                        },
                        {
                            name: 'price',
                            data: 'price'
                        },
                        {
                            name: 'verified',
                            data: 'verified'
                        },
                        {
                            name: 'driver',
                            data: 'driver'
                        },
                        {
                            name: 'status',
                            data: 'status'
                        },
                        {
                            name: 'actions',
                            data: 'actions'
                        },




                    ],
                    columnDefs: [{
                            'targets': 0,
                            'searchable': true,
                            'orderable': false,
                            'width': '1%',
                            'className': 'dt-body-center',

                        },

                        {
                            targets: [1, 2],
                            className: 'text-center table-info'
                        },
                        {
                            targets: [3, 4, 5, 6, 7, 8],
                            className: 'text-center table-danger'
                        },
                        {
                            targets: [7],
                        },
                        {
                            targets: [9,10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
                            className: 'text-center table-warning',

                        },
                        {
                            targets: [12, 13, 14, 15],
                            className: 'text-center table-info',
                        },
                        {
                            targets: [16, 17, 18],
                            className: 'text-center table-success'
                        }
                    ],
                    paginationType: 'numbers',
                    "language": {
                        "search": "Хайх:"
                    },
                    lengthMenu: [1000, 1500, 2500, 3500],

                });
                // setInterval(function(){  table.ajax.reload();  },30000);
                //selectedIds.forEach(function(selectedId) {
                // alert(selectedId);
                //}


            }
            //юю



            let status = $('#filterByStatus').val();
            let bus = $('#filterByBus').val();
            let driver = $('#filterByDriver').val();
            let customer = $('#filterByCustomer').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var merchant = $('#filterByCustomerMerchant').val();
            var type = $('#filterByCustomerType').val();

            let not_2 = 2;
            let not_3 = 3;
            let not_4 = 4;
            let not_5 = 5;
            let not_6 = 6;
            let not_10 = 10;
            let not_100 = 100;
            loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6, not_10, not_100,
                start_date, end_date,
                merchant, type);
            $('#filterByCustomerMerchant').change(function() {
                var merchant = $('#filterByCustomerMerchant').val();
                var type = $('#filterByCustomerType').val();
                console.log("merchant id:" + $('#filterByCustomerMerchant').val());
                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date,
                    merchant, type);
            });


            loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6, not_10, not_100,
                start_date, end_date, merchant, type);
            $('#filterByCustomerType').change(function() {
                var type = $('#filterByCustomerType').val();
                var merchant = $('#filterByCustomerMerchant').val();
                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date,
                    merchant, type);
            });

            loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6, not_10, not_100,
                start_date, end_date);
            $(
                '#filterByStatus').change(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var status_1 = $('#filterByStatus').val();
                var driver = $('#filterByDriver').val();
                var bus = $('#filterByBus').val();
                var customer = $('#filterByCustomer').val();

                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date);
            });
            $('#filterByBus').change(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var status_1 = $('#filterByStatus').val();
                var driver = $('#filterByDriver').val();
                var bus = $('#filterByBus').val();
                var customer = $('#filterByCustomer').val();

                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date);
            });

            $('#filterByDriver').change(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var status_1 = $('#filterByStatus').val();
                var driver = $('#filterByDriver').val();
                var bus = $('#filterByBus').val();
                var customer = $('#filterByCustomer').val();

                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date);
            });

            $('#filterByCustomer').change(function() {

                var customer = $('#filterByCustomer').val();

                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date);
            });

            $('#filterByDateRange').click(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var status_1 = $('#filterByStatus').val();
                var driver = $('#filterByDriver').val();
                var bus = $('#filterByBus').val();
                var customer = $('#filterByCustomer').val();

                rows_selected.length = 0;
                loadDeliveryDataTable(status, bus, driver, customer, not_2, not_3, not_4, not_5, not_6,
                    not_10, not_100, start_date, end_date);
            });

            var selected_status = 1;
            var selected_bus = 1;
            var selected_driver = 1;
            $(document).on('click', '#btnStatusModal', function() {
                $('#statusModal').attr('style', 'display:block');
            });

            $(document).on('click', '#btnBusModal', function() {
                $('#busModal').attr('style', 'display:block');
                console.log("hi");
            });

            $(document).on('click', '#btnVerifyModal', function() {
                $('#verifyModal').attr('style', 'display:block');
                console.log("hi");
            });

            window.updateCount = function() {
                var x = $(".checkbox:checked").length;
                document.getElementById("y").innerHTML = 'Нйит ' + x + ' мөр сонгосон байна';
            };

            $(document).on('click', '#btnDriverModal', function() {
                $('#driverModal').attr('style', 'display:block');
                console.log("btnDeleteModal");

            });

            $(document).on('click', '#btnDeleteModal', function() {
                $('#deleteModal').attr('style', 'display:block');
            });

            $('.btn_change_status').click(function() {
                console.log("btn_change_status click");
                console.log(rows_selected);
                const changeStatusUrl = '{{ route('change_status_on_delivery') }}';
                var ids = rows_selected.join(",");
                selected_status = $('.inputStatus1').val();

                $.ajax({
                    type: 'GET',
                    url: changeStatusUrl,
                    data: {
                        ids: ids,
                        status: selected_status
                    },
                    beforeSend: function() {
                        console.log("Loading");
                    }
                }).done(function(result) {
                    $('#customModal').attr('style', 'display:none');
                    window.location.reload();
                });
            });

            $('.btn_verify').click(function() {
                console.log("btn_change_verify click");
                console.log(rows_selected);
                const changeVerifyUrl = '{{ route('change_verify_on_delivery') }}';
                var ids = rows_selected.join(",");
                verified = $('.inputStatus15').val();

                $.ajax({
                    type: 'GET',
                    url: changeVerifyUrl,
                    data: {
                        ids: ids,
                        verified: verified
                    },
                    beforeSend: function() {
                        console.log("Loading");
                    }
                }).done(function(result) {
                    $('#customModal').attr('style', 'display:none');
                    window.location.reload();
                });
            });

            $('.btn_delete').click(function() {
                console.log("btn_delete click");
                console.log(rows_selected);
                const changeVerifyUrl = '{{ route('change_delete_on_delivery') }}';
                var ids = rows_selected.join(",");

                $.ajax({
                    type: 'GET',
                    url: changeVerifyUrl,
                    data: {
                        ids: ids
                    },
                    beforeSend: function() {
                        console.log("Loading");
                    }
                }).done(function(result) {
                    $('#customModal').attr('style', 'display:none');
                    window.location.reload();
                });
            });

            $('.btn_change_bus').click(function() {
                console.log("btn_change_bus click");
                console.log(rows_selected);
                const changeBusUrl = '{{ route('change_bus_on_delivery') }}';
                var ids = rows_selected.join(",");
                selected_bus = $('.inputStatus3').val();

                $.ajax({
                    type: 'GET',
                    url: changeBusUrl,
                    data: {
                        ids: ids,
                        region: selected_bus
                    },
                    beforeSend: function() {
                        console.log("Loading");
                    }
                }).done(function(result) {
                    $('#customModal').attr('style', 'display:none');
                    window.location.reload();
                });
            });



            $('.btn_change_drive').click(function() {
                console.log("btn_change_drive click");
                console.log(rows_selected);
                const changeDriverUrl = '{{ route('change_driver_on_delivery') }}';
                var ids = rows_selected.join(",");
                selected_driver = $('.inputStatus4').val();

                $.ajax({
                    type: 'GET',
                    url: changeDriverUrl,
                    data: {
                        ids: ids,
                        driverselected: selected_driver
                    },
                    beforeSend: function() {
                        console.log("Loading");
                    }
                }).done(function(result) {
                    $('#customModal').attr('style', 'display:none');
                    window.location.reload();
                });
            });

            $(document).ready(function() {
                // Click event handler for the "X" icon
                $('.closing').click(function() {
                    // Clear the checkboxes (assuming they are inside the #excel-wrapper)
                    $('input[type="checkbox"]').prop('checked', false);

                    // Close the modal
                    $('#customModal')
                        .hide(); // This hides the modal; you may need to adapt this to your specific modal implementation
                    location.reload();
                });
            });
            const edit_comment_on_datatable = '{{ route('editCommentDataTable') }}';
            $j('#datatable').on('click', '.button_edit_comment', function() {
                var id = $(this).data('id');
                var comment = $('#note_' + id).val();

                $.ajax({
                    type: 'GET',
                    url: edit_comment_on_datatable,
                    data: {
                        comment: comment,
                        id: id,
                    },
                    success: function(result) {
                        alert("Нэмэлт тайлбар амжилттай шинэчлэгдлээ..");

                    },
                    error: function(result) {
                        //alert(3);
                    },
                });

            });

            $(document).on('click', 'input[type="checkbox"]', function() {
                // Get row ID
                var rowId = $(this).attr('data-id');
                // Determine whether row ID is in the list of selected row IDs
                var index = $.inArray(rowId, rows_selected);

                // If checkbox is checked and row ID is not in list of selected row IDs
                if (this.checked && index === -1) {
                    rows_selected.push(rowId);

                    // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                } else if (!this.checked && index !== -1) {
                    rows_selected.splice(index, 1);
                }

                rows_selected = rows_selected.filter(function(value) {
                    return value !== undefined;
                });
                console.log(rows_selected);
                $("#selectedIds").val(rows_selected);
                //$("#selectedIds").val(rows_selected.join(', '));
            });

            // Handle to Export as a excel file
           // Handle to Export as a excel file - Simple redirect approach
$(document).on('click', '#__btnExcelExport', function() {
    var ids = rows_selected.join(",");
    
    console.log('Exporting these IDs:', ids);
    console.log('Number of IDs:', rows_selected.length);
    
    if (!ids) {
        alert('Please select at least one record to export');
        return;
    }
    
    // Simply redirect to the export URL - browser will handle the download
    window.location.href = '{{ route('excel-export-delivery') }}?excel=1&ids=' + encodeURIComponent(ids);
    
    // Clear selection after export
    rows_selected = [];
    $('input[type="checkbox"]').prop('checked', false);
    updateCount();
});

            $('#importModal').attr('style', 'display:none');
            // Handle to Export as a excel file
            $(document).on('click', '#__btnImportExcel', function() {

                $('#importModal').attr('style', 'display:block');
            });

            function printData(divID) {
                //Get the HTML of div
                var divElements = document.getElementById(divID).innerHTML;
                //Get the HTML of whole page
                var oldPage = document.body.innerHTML;




                //Reset the page's HTML with div's HTML only
                document.body.innerHTML =
                    "<html><head><title></title></head><body>" +
                    divElements + "</body>";

                //Print Page
                window.print();
                //Restore orignal HTML
                document.body.innerHTML = oldPage;

            }
            // Handle to Print Data
            $(document).on('click', '#__btnPrintZarlaga', function() {
                const printDataDeliveryURL = '{{ route('print-data-delivery_zarlaga') }}';
                var ids = rows_selected.join(",");

                $.ajax({
                    type: 'GET',
                    url: printDataDeliveryURL,
                    data: {
                        print: 1,
                        ids: ids,
                    },
                    beforeSend: function() {

                    }
                }).done(function(result) {
                    // console.log(result);
                    $("#print_wrapper").html(result);
                    
                    // Wait for all images to load before printing
                    var images = $("#print_wrapper").find('img');
                    var imagesToLoad = images.length;
                    var imagesLoaded = 0;
                    
                    if (imagesToLoad === 0) {
                        // No images, print immediately
                        printData("print_wrapper");
                        $("#print_wrapper").html('');
                        rows_selected.length = 0;
                    } else {
                        // Wait for all images to load
                        images.each(function() {
                            var img = $(this);
                            if (img[0].complete) {
                                imagesLoaded++;
                                checkAllImagesLoaded();
                            } else {
                                img.on('load', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                }).on('error', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                });
                            }
                        });
                    }
                    
                    function checkAllImagesLoaded() {
                        if (imagesLoaded >= imagesToLoad) {
                            // Small delay to ensure rendering is complete
                            setTimeout(function() {
                                printData("print_wrapper");
                                $("#print_wrapper").html('');
                                rows_selected.length = 0;
                            }, 100);
                        }
                    }
                    //return false;

                });
            });

             $(document).on('click', '#__btnPrintInvoice', function() {
                const printDataDeliveryURL = '{{ route('print-data-delivery_invoice') }}';
                var ids = rows_selected.join(",");

                $.ajax({
                    type: 'GET',
                    url: printDataDeliveryURL,
                    data: {
                        print: 1,
                        ids: ids,
                    },
                    beforeSend: function() {

                    }
                }).done(function(result) {
                    // console.log(result);
                    $("#print_wrapper").html(result);
                    
                    // Wait for all images to load before printing
                    var images = $("#print_wrapper").find('img');
                    var imagesToLoad = images.length;
                    var imagesLoaded = 0;
                    
                    if (imagesToLoad === 0) {
                        // No images, print immediately
                        printData("print_wrapper");
                        $("#print_wrapper").html('');
                        rows_selected.length = 0;
                    } else {
                        // Wait for all images to load
                        images.each(function() {
                            var img = $(this);
                            if (img[0].complete) {
                                imagesLoaded++;
                                checkAllImagesLoaded();
                            } else {
                                img.on('load', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                }).on('error', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                });
                            }
                        });
                    }
                    
                    function checkAllImagesLoaded() {
                        if (imagesLoaded >= imagesToLoad) {
                            // Small delay to ensure rendering is complete
                            setTimeout(function() {
                                printData("print_wrapper");
                                $("#print_wrapper").html('');
                                rows_selected.length = 0;
                            }, 100);
                        }
                    }
                    //return false;

                });
            });
            // Handle to Print Data
            $(document).on('click', '#__btnPrint', function() {
                const printDataDeliveryURL = '{{ route('print-data-delivery_item') }}';
                var ids = rows_selected.join(",");

                $.ajax({
                    type: 'GET',
                    url: printDataDeliveryURL,
                    data: {
                        print: 1,
                        ids: ids,
                    },
                    beforeSend: function() {

                    }
                }).done(function(result) {
                    // console.log(result);
                    $("#print_wrapper").html(result);
                    
                    // Wait for all images to load before printing
                    var images = $("#print_wrapper").find('img');
                    var imagesToLoad = images.length;
                    var imagesLoaded = 0;
                    
                    if (imagesToLoad === 0) {
                        // No images, print immediately
                        printData("print_wrapper");
                        $("#print_wrapper").html('');
                        rows_selected.length = 0;
                    } else {
                        // Wait for all images to load
                        images.each(function() {
                            var img = $(this);
                            if (img[0].complete) {
                                imagesLoaded++;
                                checkAllImagesLoaded();
                            } else {
                                img.on('load', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                }).on('error', function() {
                                    imagesLoaded++;
                                    checkAllImagesLoaded();
                                });
                            }
                        });
                    }
                    
                    function checkAllImagesLoaded() {
                        if (imagesLoaded >= imagesToLoad) {
                            // Small delay to ensure rendering is complete
                            setTimeout(function() {
                                printData("print_wrapper");
                                $("#print_wrapper").html('');
                                rows_selected.length = 0;
                            }, 100);
                        }
                    }
                    //return false;

                });
            });

        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.all.min.js"
        integrity="sha512-LXVbtSLdKM9Rpog8WtfAbD3Wks1NSDE7tMwOW3XbQTPQnaTrpIot0rzzekOslA1DVbXSVzS7c/lWZHRGkn3Xpg=="
        crossorigin="anonymous"></script>
    <script>
        $('.table').DataTable({
            responsive: true
        });
    </script>
    <script>
        // When the user clicks on <span> (x), close the modal
        $(document).on('click', '.closing', function() {
            $('#customModal').attr('style', 'display:none');
            $('#statusModal').attr('style', 'display:none');
            $('#driverModal').attr('style', 'display:none');
            $('#deleteModal').attr('style', 'display:none');
            $('#importModal').attr('style', 'display:none');
            $('#verifyModal').attr('style', 'display:none');
            $('#busModal').attr('style', 'display:none');


        });


        $(document).keydown(function(event) {
            if (event.keyCode == 27) {
                $('#customModal').hide();
            }
        });
        $(document).keydown(function(event) {
            if (event.keyCode == 27) {
                $('#statusModal').hide();
            }
        });
        $(document).keydown(function(event) {
            if (event.keyCode == 27) {
                $('#deleteModal').hide();
            }
        });
        $(document).keydown(function(event) {
            if (event.keyCode == 13) {
                $('#dri').submit();
            }
        });
        $(document).keydown(function(event) {
            if (event.keyCode == 27) {
                $('#driveModal').hide();
            }
        });
        $(document).keydown(function(event) {
            if (event.keyCode == 27) {
                $('#busModal').hide();
            }
        });
    </script>
    <style>
        .dataTables_wrapper .dt-buttons {
            position: absolute;
            margin: 10px
        }



        div.dtsp-panesContainer:after {
            content: '';
            display: table;
            clear: both;
        }

        .dtsp-title {
            display: none;
        }

        #print_wrapper .table th {
            padding: 0.75rem 1.25rem;
            border: 1px solid;
            font-weight: 700;
        }

        #print_wrapper .table td {
            padding: 0.75rem 1.25rem;
            border: 1px solid;
        }

        @media print {

            .table thead tr td,
            .table tbody tr td {
                border-width: 1px;
                border-style: solid;
                border-color: black;
                font-size: 10px;
                background-color: red;
                padding: 0px;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    @include('sweetalert::alert')
@endsection
