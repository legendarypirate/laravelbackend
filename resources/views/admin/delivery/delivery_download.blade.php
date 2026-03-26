@extends('admin.master')

@section('mainContent')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css"
        integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg=="
        crossorigin="anonymous" />

    <style>
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Жолоочийн татан авалт хийсэн мэдээлэл</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Simple Tables</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- /.row -->
                <div class="row" style="margin-bottom:10px!important;">
                    <div class="col-12">
                        <button type="button" id="__btnPrint" class="btn btn-info" style="background-color:#032EF1
;"> <a
                                href="#" style="color:white;">Хэвлэх</a></button>
                        <button type="button" id="__btnExcelExport" class="btn btn-info"
                            style="background-color:#032EF1
;"> <a href="#" style="color:white;">Экселээр гаргаж
                                авах</a></button>
                        {{-- <div class="row">
                            <div class="form-group">
                                <label for="status">Төлөв:</label>
                                <select id="filterByStatus" class="form-control inputStatus">
                                    <option value="">Бүгд</option>
                                    <option value="1">Бүртгэгдсэн</option>
                                    <option value="2">Жолоочид хуваарилсан</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Бүс:</label>
                                <select id="filterByBus" class="form-control inputStatus9">
                                    <?php $bus = DB::table('regions')->get(); ?>
                                    <option value="">Бүгд</option>
                                    @foreach ($bus as $region)
                                        <option value="{{ $region->name }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Жолооч:</label>
                                <select id="filterByDriver" class="form-control inputStatus9">
                                    <option value="">Бүгд</option>
                                    <?php $bus = DB::table('users')
                                        ->where('role', 'driver')
                                        ->get(); ?>
                                    @foreach ($bus as $region)
                                        <option value="{{ $region->name }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Харилцагч:</label>
                                <select id="filterByCustomer" class="form-control inputStatus9">
                                    <option value="">Бүгд</option>
                                    <?php $bus = DB::table('users')
                                        ->where('role', 'customer')
                                        ->get(); ?>
                                    @foreach ($bus as $region)
                                        <option value="{{ $region->name }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}


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
                                                <th class="text-center whitespace-nowrap table-info">Жолоочийн нэр</th>
                                                <th class="text-center whitespace-nowrap table-info">Shop нэр</th>
                                                <th class="text-center whitespace-nowrap table-info">Хүргэлтийн дугаар</th>
                                                <th class="text-center whitespace-nowrap table-info">Тооцоо</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <!-- /.card-body -->

                                <div style="position:fixed;bottom:20px;">
                                    <button class="btn btn-primary shadow-md mr-2"><span id="y">0 </span> </button>
                                </div>

                            </div>
                            <div id="statusModal" class="modal">
                                <div class="modal-content text-center"
                                    style="width:400px !important;height:200px !important;margin-left:700px;margin-top:200px;">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Тайлан нийлэх</h4>
                                    </div>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default closing"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary btn_report">Нийлэх</button>
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
            console.log("Asds:" + rows_selected);
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
            console.log("Asds:" + rows_selected);
        }
        $('input[name="foo"]').click(function() {
            document.getElementById("result").textContent = "Total Number of Items Selected = " + document
                .querySelectorAll('input[name="foo"]:checked').length;

        });
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            const deliveryDownloadUrl = '{{ route('delivery_download_data') }}';
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: deliveryDownloadUrl,
                    type: 'GET',

                    dataSrc: function(json) {
                        return json.data;
                        console.log(json);
                    },
                    error: function(xhr, error, thrown) {
                        console.log('AJAX Error:', xhr, error, thrown);
                    }
                },
                columns: [{
                        name: 'checkbox',
                        data: 'checkbox',
                    },
                    {
                        name: 'driver_id',
                        data: 'driver_id',
                    },
                    {
                        name: 'shop',
                        data: 'shop',
                    },
                    {
                        name: 'deliveries_id',
                        data: 'deliveries_id',
                    },
                    {
                        name: 'download_price',
                        data: 'download_price',
                    },
                ],
                columnDefs: [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'width': '1%',
                        'className': 'dt-body-center',

                    },
                    {
                        targets: [1, 2, 3, 4],
                        className: 'text-center table-info'
                    },

                ],
                paginationType: 'numbers',
                "language": {
                    "search": "Хайх:"
                },
                lengthMenu: [50, 100, 150, 300],
            });
        });




        var selected_status = 1;
        var selected_bus = 1;
        var selected_driver = 1;
        $(document).on('click', '#btnReport', function() {
            $('#statusModal').attr('style', 'display:block');
        });

        $(document).on('click', '#btnBusModal', function() {
            $('#busModal').attr('style', 'display:block');
            console.log("hi");
        });

        function updateCount() {
            var x = $(".checkbox:checked").length;
            document.getElementById("y").innerHTML = 'Нийт ' + x + ' мөр сонгосон байна';
        };

        $(document).on('click', '#btnDriverModal', function() {
            $('#driverModal').attr('style', 'display:block');
            console.log("btnDeleteModal");

        });

        $(document).on('click', '#btnDeleteModal', function() {
            $('#deleteModal').attr('style', 'display:block');
        });

        $('.btn_report').click(function() {
            console.log("btn_report click");
            console.log(rows_selected);
            const changeStatusUrl = '{{ route('report_compile') }}';
            var ids = rows_selected.join(",");
            selected_status = $('.inputStatus1').val();

            $.ajax({
                type: 'GET',
                url: changeStatusUrl,
                data: {
                    ids: ids,
                    status: selected_status,
                    verified: 0
                },
                beforeSend: function() {
                    console.log("Loading");
                }
            }).done(function(result) {
                $('#customModal').attr('style', 'display:none');
                window.location.reload();

            });
            this.disabled = true;
            this.value = 'Sending…';
            return false;

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
            console.log(rows_selected);
            $("#selectedIds").val(rows_selected);
        });

        // Handle to Export as a excel file
        $(document).on('click', '#__btnExcelExport', function() {

            $('#customModal').attr('style', 'display:block');

            const excelExportUrl = '{{ route('excel-export-driver') }}';
            var ids = rows_selected.join(",");
            var status = $('#filterByStatus').val();
            $.ajax({
                type: 'GET',
                url: excelExportUrl,
                data: {
                    excel: 1,
                    ids: ids,
                    status: status
                },
                beforeSend: function() {
                    $('#excel-wrapper').html(
                        'Excel File is processing. Please wait a bit...');
                    $('.modal-excel').removeClass('modal-hide');
                }
            }).done(function(result) {
                // console.log(result);
                let downloadLink = 'Success. <a href="' + result +
                    '" download >Download Now</a>';
                $('#excel-wrapper').html(downloadLink);
                //reinitialize array
                rows_selected = [];
            });
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
        $(document).on('click', '#__btnPrint', function() {
            const printDataDriverURL = '{{ route('print-data-driver-item') }}';
            var ids = rows_selected.join(",");
            console.log("ids");
            $.ajax({
                type: 'GET',
                url: printDataDriverURL,
                data: {
                    print: 1,
                    ids: ids,
                },
                beforeSend: function() {
                    console.log("waiting");
                }
            }).done(function(result) {
                // console.log(result);
                $("#print_wrapper").html(result);
                printData("print_wrapper");
                $("#print_wrapper").html('');
                rows_selected.length = 0;
                //return false;

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
