@extends('admin.master')

@section('mainContent')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css" integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg==" crossorigin="anonymous" />

<style>



</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Хүргэлт</h1>
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
        <div class="row">
          <div class="col-12">
          <button type="button" class="btn btn-primary btn-sm"> <a href="index" style="color:white;">Шинэ хүргэлт үүсгэх</a></button> 
          <button type="button" class="btn btn-primary btn-sm"> <a href="index" style="color:white;">Print</a></button> 
          <button type="button" class="btn btn-primary btn-sm"> <a href="index" style="color:white;">Export</a></button> 
         <div class="row">
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
                        <?php $bus=DB::table('regions')->get(); ?>
                        <option value="">Бүгд</option>
                        @foreach($bus as $region)
                        <option value="{{$region->name}}">{{$region->name}}</option>
                        @endforeach                      
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Жолооч:</label>
                    <select id="filterByDriver" class="form-control inputStatus9">
                        <option value="">Бүгд</option>
                        <option value="altansukhJ1">altansukhJ1</option>   
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Харилцагч:</label>
                    <select id="filterByCustomer" class="form-control inputStatus9">
                        <option value="">Бүгд</option>
                        <option value="&quot; энхрий онлайн шоп&quot;">&quot; энхрий онлайн шоп&quot;</option>
                    </select>
                </div>
            </div>
           
            <div class="card">
                
              <div class="card-header">

              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                
                <table class="table table-hover text-nowrap" id="datatable">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap"> <input type="checkbox"  style="width:20px;height:20px;" onClick="toggle(this);updateCount();" /></th>
                        <th class="text-center whitespace-nowrap">Үүссэн огноо</th>
                        <th class="whitespace-nowrap">Нэр</th>
                        <th class="text-center whitespace-nowrap">Утас</th>
                        <th class="text-center whitespace-nowrap">Дүн</th>

                        <th class="text-center whitespace-nowrap">Хаяг</th>
                        <th class="text-center whitespace-nowrap">Тайлбар</th>

                        <th class="text-center whitespace-nowrap">Төлөв</th>
                      

                        <th class="text-center whitespace-nowrap">Бүс</th>
                        <th class="text-center whitespace-nowrap">Жолооч</th>
                     
                        <th class="text-center whitespace-nowrap">Баталгаажсан</th>

                        <th class="text-center whitespace-nowrap">Үйлдэл</th>
                    </tr>
                </thead>
                <tbody></tbody>
                </table>
              </div>
              <!-- /.card-body -->

    <div style="position:fixed;bottom:20px;">
         <button class="btn btn-primary shadow-md mr-2"><span id="y">0 </span> </button>                                                              
         <button type="button" class="btn btn-default"  id="btnStatusModal">Төлөв солих</button> 
         <button type="button" class="btn btn-default" id="btnBusModal">Бүс солих</button>      
         <button type="button" class="btn btn-default" id="btnDriverModal">Жолооч солих</button>
         <button type="button" class="btn btn-default" id="btnVerifyModal">Баталгаажуулах</button>
         <button type="button" class="btn btn-default" id="btnDeleteModal">Устгах</button>
    </div> 

 
                
        
        
    

       
</div>
<div id="statusModal" class="modal" >
            <div class="modal-content text-center" style="width:400px !important;height:200px !important;margin-left:700px;margin-top:200px;">
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
            <button type="button" class="btn btn-default closing" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn_change_status">Солих</button>
            </div>
        </div>
</div>
<div id="busModal" class="modal" >
            <div class="modal-content text-center" style="width:400px !important;height:200px !important;margin-left:700px;margin-top:200px;">
                <div class="modal-header">
                    <h4 class="modal-title">Бүс солих</h4>
                </div>
                <div class="modal-body">
                    <select class="form-control inputStatus3">
                        <?php $bus=DB::table('regions')->get(); ?>
                        @foreach($bus as $region)
                        <option value="{{$region->name}}">{{$region->name}}</option>
                        @endforeach
                    </select>       
                </div>
            <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default closing" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn_change_bus">Солих</button>
            </div>
        </div>
</div>

                <div id="driverModal" class="modal" >
                            <div class="modal-content text-center" style="width:400px !important;height:200px !important;margin-left:700px;margin-top:200px;">
                                <div class="modal-header">
                                    <h4 class="modal-title">Жолооч солих</h4>
                                </div>
                                <div class="modal-body">
                                    <select class="form-control inputStatus4">
                                    <?php $bus=DB::table('users')->where('role','driver')->get(); ?>
                                        @foreach($bus as $region)
                                        <option value="{{$region->name}}">{{$region->name}}</option>
                                        @endforeach
                                    </select>       
                                </div>
                            <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default closing" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn_change_drive">Солих</button>
                            </div>
                        </div>
                </div>

<div id="deleteModal" class="modal" >
            <div class="modal-content text-center" style="width:400px !important;height:200px !important;margin-left:700px;margin-top:200px;">
                <div class="modal-header">
                    <h4 class="modal-title">Устгах</h4>
                </div>
                
            <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default closing" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn_delete">Устгах</button>
            </div>
        </div>
</div>


<div id="verifyModal" class="modal" >
            <div class="modal-content text-center" style="width:400px !important;height:250px !important;margin-left:700px;margin-top:200px;">
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
            <button type="button" class="btn btn-default closing" data-dismiss="modal">Close</button>
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
      for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
        
      }

        checkboxes.forEach((input) => {
                if (input.checked) {
                    rows_selected.push(input.value);
                }else{
                         rows_selected.length = 0;
                }
            });
        }
    $('input[name="foo"]').click(function() {
    document.getElementById("result").textContent = "Total Number of Items Selected = " + document.querySelectorAll('input[name="foo"]:checked').length;

});
    </script>


            <script type="text/javascript">
                var $j = jQuery.noConflict();
                $(document).ready(function($j){
                const deliveryTableUrl = '{{ route('datatable-delivery') }}';
                const loadDeliveryDataTable = (status,bus,driver,customer,status_1) => {
                  var table =  $j('#datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        bDestroy: true,
                        ajax: {
                            type: 'GET',
                            url: deliveryTableUrl,
                            data: {
                                status: status,
                                region: bus,
                                driver: driver,
                                customer: customer,
                                status_1 : status_1
                            },
                            dataSrc: function ( json ) {
                             return json.data;
                            }
                        },
                        columns: [
                            {
                                name: 'checkbox',
                                data: 'checkbox',
                            },
                            {
                                name: 'created_at',
                                data: 'created_at'
                            },
                            {
                                name: 'shop',
                                data: 'shop'
                            },
                            {
                                name: 'phone',
                                data: 'phone'
                            },
                            {
                                name: 'price',
                                data: 'price'
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
                                name: 'status',
                                data: 'status'
                            },
                            {
                                name: 'region',
                                data: 'region'
                            },
                            {
                                name: 'driver',
                                data: 'driver'
                            },
                            {
                                name: 'verified',
                                data: 'verified'
                            },
                            {
                                name: 'actions',
                                data: 'actions'
                            }
                            
                        ],
                        columnDefs: [
                            {
                                'targets': 0,
                                'searchable':false,
                                'orderable':false,
                                'width':'1%',
                                'className': 'dt-body-center',
                                
                            },
                            {
                                targets: [7],
                                orderable: false
                            },
                            {
                                targets: [1],
                                className: 'text-center'
                            }
                        ],
                        paginationType: 'numbers',
                        "language": {
                            "search": "Хайх:"
                        },
                        lengthMenu: [1000, 2000, 3000, 4000], 
                    });
                    // setInterval(function(){  table.ajax.reload();  },30000);

                    //selectedIds.forEach(function(selectedId) {
                   // alert(selectedId);
                    //}
    
                }
                let status_10 = 10;
                let status_1 = 1;
                let status = $('#filterByStatus').val();
                let bus = $('#filterByBus').val();
                let driver = $('#filterByDriver').val();
                let customer = $('#filterByCustomer').val();

                loadDeliveryDataTable(status,bus,driver,customer,status_1);
                $('#filterByStatus').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $(this).val();
                    var bus = $('#filterByBus').val();
                    var driver = $('#filterByDriver').val();
                    loadDeliveryDataTable(status,bus,driver,customer,status_1);
                });                
                $('#filterByBus').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var driver = $('#filterByDriver').val();
                    var bus = $(this).val();
                    loadDeliveryDataTable(status,bus,driver,customer,status_1);
                });

                $('#filterByDriver').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var driver = $(this).val();
                    var bus = $('#filterByBus').val();
                    loadDeliveryDataTable(status,bus,driver,customer,status_1);
                });

                $('#filterByCustomer').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var customer = $(this).val();
                    var bus = $('#filterByBus').val();
                    loadDeliveryDataTable(status,bus,driver,customer,status_1);
                });
               
                var selected_status = 1;
                var selected_bus = 1;
                var selected_driver = 1;
                $(document).on('click', '#btnStatusModal', function() {
                    $('#statusModal').attr('style','display:block');
                });

                $(document).on('click', '#btnBusModal', function() {
                    $('#busModal').attr('style','display:block');
                    console.log("hi");
               });

               $(document).on('click', '#btnVerifyModal', function() {
                    $('#verifyModal').attr('style','display:block');
                    console.log("hi");
               });

                window.updateCount = function() {
                    var x = $(".checkbox:checked").length;
                    document.getElementById("y").innerHTML ='Нйит '+ x+' мөр сонгосон байна';
                };

                $(document).on('click', '#btnDriverModal', function() {
                    $('#driverModal').attr('style','display:block');
                    console.log("btnDeleteModal");

                });
    
                $(document).on('click', '#btnDeleteModal', function() {
                    $('#deleteModal').attr('style','display:block');
                });

                $('.btn_change_status').click(function () {
                    console.log("btn_change_status click");
                    console.log(rows_selected);
                    const changeStatusUrl = '{{ route('change_status_on_delivery') }}';
                    var ids = rows_selected.join(",");
                    selected_status = $('.inputStatus1').val();
    
                    $.ajax({
                        type: 'GET',
                        url: changeStatusUrl,
                        data: {
                            ids : ids,
                            status : selected_status
                        },
                        beforeSend: function() {
                            console.log("Loading");
                        }
                    }).done(function(result) {
                        $('#customModal').attr('style','display:none');
                        window.location.reload();
                    });
                });

                $('.btn_verify').click(function () {
                    console.log("btn_change_verify click");
                    console.log(rows_selected);
                    const changeVerifyUrl = '{{ route('change_verify_on_delivery') }}';
                    var ids = rows_selected.join(",");
                    verified = $('.inputStatus15').val();
    
                    $.ajax({
                        type: 'GET',
                        url: changeVerifyUrl,
                        data: {
                            ids : ids,
                            verified : verified
                        },
                        beforeSend: function() {
                            console.log("Loading");
                        }
                    }).done(function(result) {
                        $('#customModal').attr('style','display:none');
                        window.location.reload();
                    });
                });

                $('.btn_delete').click(function () {
                        console.log("btn_delete click");
                        console.log(rows_selected);
                        const changeVerifyUrl = '{{ route('change_delete_on_delivery') }}';
                        var ids = rows_selected.join(",");
                           
                        $.ajax({
                            type: 'GET',
                            url: changeVerifyUrl,
                            data: {
                                ids : ids
                                },
                            beforeSend: function() {
                                console.log("Loading");
                                                    }
                            }).done(function(result) {
                            $('#customModal').attr('style','display:none');
                            window.location.reload();
                    });
                });
                
                $('.btn_change_bus').click(function () {
                    console.log("btn_change_bus click");
                    console.log(rows_selected);
                    const changeBusUrl = '{{ route('change_bus_on_delivery') }}';
                    var ids = rows_selected.join(",");
                    selected_bus = $('.inputStatus3').val();
    
                    $.ajax({
                        type: 'GET',
                        url: changeBusUrl,
                        data: {
                            ids : ids,
                            region : selected_bus
                        },
                        beforeSend: function() {
                            console.log("Loading");
                        }
                    }).done(function(result) {
                        $('#customModal').attr('style','display:none');
                        window.location.reload();
                    });
                });

                

                $('.btn_change_drive').click(function () {
                    console.log("btn_change_drive click");
                    console.log(rows_selected);
                    const changeDriverUrl = '{{ route('change_driver_on_delivery') }}';
                    var ids = rows_selected.join(",");
                    selected_driver = $('.inputStatus4').val();
    
                    $.ajax({
                        type: 'GET',
                        url: changeDriverUrl,
                        data: {
                            ids : ids,
                            driverselected : selected_driver
                        },
                        beforeSend: function() {
                            console.log("Loading");
                        }
                    }).done(function(result) {
                        $('#customModal').attr('style','display:none');
                        window.location.reload();
                    });
                });


                $(document).on('click', 'input[type="checkbox"]', function() {
                    // Get row ID
                    var rowId = $(this).attr('data-id');
                    // Determine whether row ID is in the list of selected row IDs
                    var index = $.inArray(rowId, rows_selected);

                    // If checkbox is checked and row ID is not in list of selected row IDs
                    if(this.checked && index === -1){
                    rows_selected.push(rowId);

                    // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                    } else if (!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                    }
                    //console.log(rows_selected);
                    //$("#selectedIds").val(rows_selected);
                });
    
                // Handle to Export as a excel file
                $(document).on('click', '#__btnExcelExport', function() {
    
                    $('#customModal').attr('style','display:block');
    
                    const excelExportUrl = '{{ route('excel-export-delivery') }}';
                    var ids = rows_selected.join(",");
                    var status = $('#filterByStatus').val();
                    $.ajax({
                           type: 'GET',
                            url: excelExportUrl,
                            data: {
                                excel: 1,
                                ids : ids,
                                status : status
                            },
                        beforeSend: function() {
                            $('#excel-wrapper').html('Excel File is processing. Please wait a bit...');
                            $('.modal-excel').removeClass('modal-hide');
                        }
                    }).done(function(result) {
                       // console.log(result);
                        let downloadLink = 'Success. <a href="'+result+'" download >Download Now</a>';
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
                    const printDataDeliveryURL = '{{ route('print-data-delivery') }}';
                    var ids = rows_selected.join(",");
                    $.ajax({
                        type: 'GET',
                            url: printDataDeliveryURL,
                            data: {
                                print: 1,
                                ids : ids,
                            },
                        beforeSend: function() {
            
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

            });
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.all.min.js" integrity="sha512-LXVbtSLdKM9Rpog8WtfAbD3Wks1NSDE7tMwOW3XbQTPQnaTrpIot0rzzekOslA1DVbXSVzS7c/lWZHRGkn3Xpg==" crossorigin="anonymous"></script>

        <script>

            
        // When the user clicks on <span> (x), close the modal
            $(document).on('click', '.closing', function() {
            $('#customModal').attr('style','display:none');
            $('#statusModal').attr('style','display:none');
            $('#driverModal').attr('style','display:none');
            $('#deleteModal').attr('style','display:none');

        $('#busModal').attr('style','display:none');
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
    #print_wrapper .table td{
    padding: 0.75rem 1.25rem;
    border: 1px solid;
    }

    @media print{
        .table thead tr td,.table tbody tr td{
            border-width: 1px;
            border-style: solid;
            border-color: black;
            font-size: 10px;
            background-color: red;
            padding:0px;
            -webkit-print-color-adjust:exact;
        }
    }
</style>
@include('sweetalert::alert')

  @endsection