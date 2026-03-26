@extends('admin.master')

@section('mainContent')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css"
        integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg=="
        crossorigin="anonymous" />

    <style>
        [type=search] {
            -webkit-appearance: textfield;
            outline-offset: -2px;
            border: 1px solid #a6bcce;
            padding: 8px;
            border-radius: 10px;
        }

.parcel-list ul {
    margin: 0;
    padding-left: 20px;
    list-style-type: decimal;
}

.parcel-list li {
    margin-bottom: 2px;
    padding-left: 5px;
}

.parcel-info-html {
    display: inline-block;
}

/* Ensure DataTables doesn't escape HTML */
#datatable td {
    word-wrap: break-word;
}
/* Fixed Table Container */
.table-wrapper {
    max-height: 70vh;
    overflow: auto;
    position: relative;
}

/* Fixed Header */
.table-wrapper table thead {
    position: sticky;
    top: 0;
    background: white;
    z-index: 100;
}

/* Ensure table cells have proper borders */
.table-wrapper table th,
.table-wrapper table td {
    border: 1px solid #dee2e6;
    padding: 8px;
}

/* Shadow effect for better visibility */
.table-wrapper table thead {
    box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
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

        /* Fixed Horizontal Scroll */
.dataTables_scroll {
    position: relative;
}

.dataTables_scrollHead {
    position: sticky !important;
    top: 0;
    z-index: 999;
}

.dataTables_scrollBody {
    max-height: 70vh !important; /* Adjust height as needed */
    overflow-y: auto !important;
    overflow-x: auto !important;
}

/* Ensure table headers stay fixed */
.dataTables_scrollHead .dataTables_scrollHeadInner {
    width: 100% !important;
}

.dataTables_scrollHead table {
    width: 100% !important;
}

/* Fix for sticky header */
.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

/* Ensure proper z-index for fixed elements */
.modal-custom {
    z-index: 10000;
}

.dataTables_scrollHead {
    z-index: 999 !important;
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

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background-color: #f2f2f2;
        }

        /* Optional: Add styling for the first column (e.g., ID column) */
        .data-table td:first-child {
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
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Бүх хүргэлтийн мэдээлэл</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">

                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">

                <!-- /.row -->
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-info" style="background-color:#f4511e
;"> <a href="index"
                                style="color:white;">Шинэ хүргэлт үүсгэх</a></button>
                        <button type="button" id="__btnPrint" class="btn btn-info" style="background-color:#032EF1
;"> <a
                                href="#" style="color:white;">Хэвлэх</a></button>
                        <button type="button" id="__btnExcelExport" class="btn btn-info"
                            style="background-color:#032EF1
;"> <a href="#" style="color:white;">Экселээр гаргаж
                                авах</a></button>
                        <button type="button" id="__btnImportExcel" class="btn btn-info"
                            style="background-color:#032EF1
;"> <a href="#" style="color:white;">Экселээс
                                оруулах</a></button>
                        <button type="button" id="__btnTootsooNiilvvleh" class="btn btn-info"> <a href="#"
                                style="color:white;">Тооцоо
                                нийлүүлэх</a></button>
                        <button type="button" class="btn btn-primary"> <a
                                href="http://178.128.63.241/dist/Excel_blank_delivery.xlsx" style="color:white;">Экселийн
                                Загвар татаж
                                авах</a></button>
                        <div class="row">
                            <div class="row">
                                <div class="form-group myform">
                                    <label for="status">Төрөл:</label>
                                    <select id="filterByCustomerType" class="form-control inputStatus9">
                                        <option value="">Бүгд</option>
                                        <option value="1">Энгийн</option>
                                        <option value="2">1-2 хайрцаг</option>
                                        <option value="3">3-6 хайрцаг</option>
                                        <option value="4">6-10 хайрцаг</option>
                                        <option value="6">Бөөний 10+</option>
                                    </select>
                                </div>
                                <div class="form-group myform">
                                    <label for="status">Төлөв:</label>
                                    <select id="filterByStatus" class="form-control inputStatus">
                                        <option value="">Бүгд</option>
                                        <option value="3">Хүргэгдсэн</option>
                                        <option value="4">Цуцалсан</option>
                                        <option value="5">Буцаасан</option>
                                    </select>
                                </div>
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
                                        <label for="status">Жолооч:</label>
                                        <select id="filterByDriver" class="form-control inputStatus9">
                                            <?php $shop = DB::table('users')
                                                ->where('role', 'driver')
                                                ->where('active',1)
                                                ->get(); ?>
                                            <option value="">Бүгд</option>
                                            @foreach ($shop as $shops)
                                                <option value="{{ $shops->name }}">{{ $shops->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group myform">
                                        <label for="status">Харилцагч:</label>
                                        <select id="filterByCustomer" class="form-control inputStatus9">
                                            <?php $shop = DB::table('users')
                                                ->where('role', 'customer')
                                                ->get(); ?>
                                            <option value="">Бүгд</option>
                                            @foreach ($shop as $shops)
                                                <option value="{{ $shops->name }}">{{ $shops->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group myform">
                                        <label>Эхлэх:</label>
                                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                value="" />
                                            <div class="input-group-append" data-toggle="datetimepicker">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group myform">
                                        <label>Дуусах:</label>
                                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                value="" />
                                            <div class="input-group-append" data-toggle="datetimepicker">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group myform" style="margin-top:32px;">

                                        <button type="button" id="filterByDateRange" class="btn btn-info"> <a
                                                href="#" style="color:white;">Шүүх</a></button>
                                    </div>
                                @endif
                            </div>

                            <div class="card">
                                <div class="card-header">

                                    <!-- /.card-header -->
                                    <div class="card-body table-wrapper table-responsive p-0">
                                        <table class="table table-hover text-nowrap small" id="datatable"
                                            style="width:100% !important;">
                                            <thead>
                                                <tr>
                                                    <th class="whitespace-nowrap"><input type="checkbox"
                                                            style="width:20px;height:20px;"
                                                            onClick="toggle(this);updateCount();" /></th>
                                                    <th class="text-center whitespace-nowrap table-info">Track ID</th>
                                                    <th class="text-center whitespace-nowrap table-info">Огноо</th>
                                                   

                                                    <th class="text-center whitespace-nowrap table-warning">Тоо ширхэг</th>
                                                    <th class="text-center whitespace-nowrap table-success">Төлөв</th>
                                                    <th class="text-center whitespace-nowrap table-warning">Хүлээн авагчийн
                                                        нэр
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-warning">Утас 1, Утас 2
                                                    </th>

                                                    <th class="text-center whitespace-nowrap table-warning">Хүлээн авагчийн
                                                        хаяг
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-warning">Нэмэлт тайлбар
                                                        (ирэхээсээ өмнө залгах г.м.)</th>
                                                    <th class="text-center whitespace-nowrap table-warning">Барааны тооцоо
                                                    </th>
                                                     <th class="text-center whitespace-nowrap table-success">Жолооч
                                                        тэмдэглэл
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-success">Жолооч</th>
                                                    <th class="text-center whitespace-nowrap table-success">Хүргэсэн огноо
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-success">Жолоочид өгсөн
                                                        үнэлгээ</th>
                                                    <th class="text-center whitespace-nowrap table-success">Үйлдэл</th>

                                                    <th class="text-center whitespace-nowrap table-danger">Төрөл</th>
                                                    <th class="text-center whitespace-nowrap table-danger">Харилцагч</th>
                                                    <th class="text-center whitespace-nowrap table-danger">z-код
                                                    </th>

                                                    <th class="whitespace-nowrap table-danger">Мерчант нэр
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-danger">Утас 1, Утас 2
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-danger">Илгээгчийн хаяг
                                                        [дэлгэрэнгүй]
                                                    </th>
                                                    <th class="text-center whitespace-nowrap table-danger">
                                                        Барааны мэдээлэл
                                                    </th>
                                                 
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
                                        @if (auth()->user()->role != 'customer')
                                            <button type="button" class="btn btn-default" id="btnStatusModal"
                                                style="background-color:#7B22FC
;color:white;">Төлөв солих</button>
                                            <button type="button" class="btn btn-default" id="btnBusModal"
                                                style="background-color:#7B22FC
;color:white;">Бүс солих</button>
                                            <button type="button" class="btn btn-default" id="btnDriverModal"
                                                style="background-color:#7B22FC
;color:white;">Жолооч солих</button>
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
                                <div id="statusModal" class="modal">
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
                                                <?php $bus = DB::table('users')
                                                    ->where('role', 'driver')
                                                    ->where('active', 1)
                                                    ->get(); ?>
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


                                <div id="verifyModal" class="modal">
                                    <div class="modal-content text-center"
                                        style="width:400px !important;height:250px !important;margin-top:200px;">
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
                                            <button type="button"
                                                class="btn btn-primary btn_verify">Баталгаажуулах</button>
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
        const loadDeliveryDataTable = (status, region, driver, customer, type, status_1, status_2, status_3,
            status_4, status_5, status_6, status_10, status_100, start_date, end_date) => {

            var table = $j('#datatable').DataTable({
                processing: true,
                serverSide: true,
                bDestroy: true,
                scrollX: true,
                scrollY: "70vh",
                scrollCollapse: true,
                fixedHeader: true,
                ajax: {
                    type: 'GET',
                    url: deliveryTableUrl,
                    data: {
                        status: status,
                        region: region,
                        driver: driver,
                        type: type,
                        customer: customer,
                        status_1: status_1,
                        status_2: status_2,
                        status_3: status_3,
                        status_4: status_4,
                        status_5: status_5,
                        status_6: status_6,
                        status_10: status_10,
                        status_100: status_100,
                        start_date: start_date,
                        end_date: end_date,
                    },
                    dataSrc: function(json) {
                        return json.data;
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
                        name: 'number',
                        data: 'number'
                    },
                    {
                        name: 'status',
                        data: 'status'
                    },
                    {
                        name: 'receivername',
                        data: 'receivername'
                    },
                    {
                        name: 'phone',
                        data: null,
                        render: function(data, type, row) {
                            if ((row.phone != null) && (row.phone2 != null)) {
                                var mergedPhone = row.phone + ', ' + row.phone2;
                            } else if ((row.phone === null) && (row.phone2 != null)) {
                                var mergedPhone = row.phone2;
                            } else if ((row.phone != null) && (row.phone2 === null)) {
                                var mergedPhone = row.phone;
                            } else {
                                var mergedPhone = "";
                            }
                            return mergedPhone;
                        }
                    },
                    {
                        name: 'address',
                        data: 'address'
                    },
                    {
                        name: 'comment',
                        data: 'comment'
                    },
                    {
                        name: 'price',
                        data: 'price'
                    },
                    {
                        name: 'note',
                        data: 'note'
                    },
                    {
                        name: 'driver',
                        data: 'driver'
                    },
{
    name: 'delivered_at',
    data: 'delivered_at'
},
                    {
                        name: 'rating',
                        data: 'rating'
                    },
                    {
                        name: 'actions',
                        data: 'actions'
                    },
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
                        name: 'merchantName',
                        data: 'merchantName'
                    },
                    {
                        name: 'merchantPhone1',
                        data: null,
                        render: function(data, type, row) {
                            if ((row.merchantPhone1 != null) && (row.merchantPhone2 != null)) {
                                var mergedPhones = row.merchantPhone1 + ', ' + row.merchantPhone2;
                            } else if ((row.merchantPhone1 === null) && (row.merchantPhone2 != null)) {
                                var mergedPhones = row.merchantPhone2;
                            } else if ((row.merchantPhone1 != null) && (row.merchantPhone2 === null)) {
                                var mergedPhones = row.merchantPhone1;
                            } else if ((row.merchantPhone1 === null) && (row.merchantPhone2 === null)) {
                                var mergedPhones = "Утас алга";
                            } else {
                                var mergedPhones = "";
                            }
                            return mergedPhones;
                        }
                    },
                    {
                        name: 'merchantAddress',
                        data: 'merchantAddress'
                    },
{
    name: 'mergedMerchantParcel',
    data: null,
    render: function(data, type, row) {
        if (type === 'display') {
            let output = '';
            
            if (row.goodtype) {
                output += '<div><strong>' + row.goodtype + '</strong></div>';
            }
            
            if (row.parcel_info) {
                // Remove HTML tags first, then split by comma
                let cleanText = row.parcel_info.replace(/<[^>]*>/g, '');
                let items = cleanText.split(',');
                
                output += '<ul style="margin:0;padding-left:20px;">';
                items.forEach(item => {
                    let text = item.trim();
                    if (text) {
                        output += '<li>' + text + '</li>';
                    }
                });
                output += '</ul>';
            }
            
            if (row.image) {
                output += '<img src="{{ asset('storage/') }}/' + row.image + '" width="30" style="vertical-align: middle;">';
            }
            
            return output || "Мэдээлэл байхгүй";
        }
        
        // Plain text version
        let textParts = [];
        if (row.goodtype) textParts.push(row.goodtype);
        if (row.parcel_info) {
            let plainText = row.parcel_info.replace(/<[^>]*>/g, '');
            textParts.push(plainText);
        }
        return textParts.join(' - ') || "Мэдээлэл байхгүй";
    }
}
                ],
                columnDefs: [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'width': '1%',
                        'className': 'dt-body-center',
                    },
                    {
                        targets: [1, 2],
                        className: 'text-center table-info'
                    },
                    {
                        targets: [3, 5, 6, 7, 8, 9],
                        className: 'text-center table-warning'
                    },
                    {
                        targets: [7],
                        orderable: false
                    },
                    {
                        targets: [10, 11, 12, 13, 14],
                        className: 'text-center table-success'
                    },
                    {
                        targets: [15, 16, 17, 18, 19, 20, 21],
                        className: 'text-center table-danger'
                    },
                     {
            targets: [21],
            render: function(data, type, row) {
                // This prevents HTML escaping
                if (type === 'display' && typeof data === 'string' && data.includes('<')) {
                    return data;
                }
                return data;
            }
        }
                ],
                paginationType: 'numbers',
                "language": {
                    "search": "Хайх:"
                },
                lengthMenu: [1000, 1500, 2500, 3500],
            });
        }

        let status_1 = 1;
        let status_2 = 2;
        let status_3 = 3;
        let status_4 = 4;
        let status_5 = 5;
        let status_6 = 6;
        let status_10 = 10;
        let status_100 = 100;

        let status = $('#filterByStatus').val();
        let region = $('#filterByBus').val();
        let driver = $('#filterByDriver').val();
        let customer = $('#filterByCustomer').val();
        var type = $('#filterByCustomerType').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        
        loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3, status_4,
            status_5, status_6, status_10, status_100, start_date, end_date
        );

        $('#filterByCustomerType').change(function() {
            var type = $('#filterByCustomerType').val();
            rows_selected.length = 0;
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });

        $('#filterByStatus').change(function() {
            var status = $(this).val();
            var region = $('#filterByBus').val();
            var driver = $('#filterByDriver').val();
            var customer = $('#filterByCustomer').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });
        
        $('#filterByBus').change(function() {
            var customer = $('#filterByCustomer').val();
            var status = $('#filterByStatus').val();
            var driver = $('#filterByDriver').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var region = $(this).val();
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });

        $('#filterByDriver').change(function() {
            var customer = $('#filterByCustomer').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var status = $('#filterByStatus').val();
            var driver = $(this).val();
            var region = $('#filterByBus').val();
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });

        $('#filterByCustomer').change(function() {
            var customer = $(this).val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var status = $('#filterByStatus').val();
            var region = $('#filterByBus').val();
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });

        $('#filterByDateRange').click(function() {
            var customer = $('#filterByCustomer').val();
            var status = $('#filterByStatus').val();
            var region = $('#filterByBus').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            rows_selected.length = 0;
            loadDeliveryDataTable(status, region, driver, customer, type, status_1, status_2, status_3,
                status_4, status_5, status_6, status_10, status_100, start_date, end_date);
        });

        var selected_status = 1;
        var selected_bus = 1;
        var selected_driver = 1;
        
        $(document).on('click', '#btnStatusModal', function() {
            $('#statusModal').attr('style', 'display:block');
        });

        $(document).on('click', '#btnBusModal', function() {
            $('#busModal').attr('style', 'display:block');
        });

        $(document).on('click', '#btnVerifyModal', function() {
            $('#verifyModal').attr('style', 'display:block');
        });

        window.updateCount = function() {
            var x = $(".checkbox:checked").length;
            document.getElementById("y").innerHTML = 'Нийт ' + x + ' мөр сонгосон байна';
        };

        $(document).on('click', '#btnDriverModal', function() {
            $('#driverModal').attr('style', 'display:block');
        });

        $(document).on('click', '#btnDeleteModal', function() {
            $('#deleteModal').attr('style', 'display:block');
        });

        $('.btn_change_status').click(function() {
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

        $(document).on('click', 'input[type="checkbox"]', function() {
            var rowId = $(this).attr('data-id');
            var index = $.inArray(rowId, rows_selected);

            if (this.checked && index === -1) {
                rows_selected.push(rowId);
            } else if (!this.checked && index !== -1) {
                rows_selected.splice(index, 1);
            }
            rows_selected = rows_selected.filter(function(value) {
                return value !== undefined;
            });
        });

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
        function printData(divID) {
            var divElements = document.getElementById(divID).innerHTML;
            var oldPage = document.body.innerHTML;
            document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";
            window.print();
            document.body.innerHTML = oldPage;
        }

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
                $("#print_wrapper").html(result);
                printData("print_wrapper");
                $("#print_wrapper").html('');
                rows_selected.length = 0;
            });
        });

        $(document).on('click', '#__btnTootsooNiilvvleh', function() {
            const tootsooNiilvvlsenEseh = '{{ route('tootsooNiilvvlsenEseh') }}';
            var ids = rows_selected.join(",");
            $.ajax({
                type: 'GET',
                url: tootsooNiilvvlsenEseh,
                data: {
                    print: 1,
                    ids: ids,
                },
                beforeSend: function() {
                    console.log("waiting");
                }
            }).done(function(result) {
                let downloadLink = 'Success...';
                $('#excel-wrapper').html(downloadLink);
                rows_selected = [];
                rows_selected.length = 0;
            });
        });
    });
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.all.min.js"
        integrity="sha512-LXVbtSLdKM9Rpog8WtfAbD3Wks1NSDE7tMwOW3XbQTPQnaTrpIot0rzzekOslA1DVbXSVzS7c/lWZHRGkn3Xpg=="
        crossorigin="anonymous"></script>

    <script>
        // When the user clicks on <span> (x), close the modal
        $(document).on('click', '.closing', function() {
            $('#customModal').attr('style', 'display:none');
            $('#statusModal').attr('style', 'display:none');
            $('#driverModal').attr('style', 'display:none');
            $('#deleteModal').attr('style', 'display:none');

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
