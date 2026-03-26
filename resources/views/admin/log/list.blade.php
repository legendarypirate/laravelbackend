@extends('admin.master')

@section('mainContent')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Лог</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Лог</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
                            <section class="content">
      <div class="container-fluid">
      <br>
        <!-- /.row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="get" action="{{url('/log/list')}}" class="form-inline">
                            <div class="form-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Хайх..." value="{{ request('search') }}" style="width:300px;">
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Хайх</button>
                            @if(request('search'))
                                <a href="{{url('/log/list')}}" class="btn btn-secondary">Цэвэрлэх</a>
                            @endif
                        </form>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Утга</th>
                                    <th>Утас</th>
                                    <th>Цаг</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($log as $logs)
                                <tr>
                                    <td>{{$logs->id}}</td>
                                    <td>{{$logs->value}}</td>
                                    <td>{{$logs->phone}}</td>
                                    <td>{{$logs->created_at}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Мэдээлэл олдсонгүй</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $log->links() }}
                    </div>
                </div>
            </div>
        </div>
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
                const deliveryTableUrl = '{{ route('datatable-order') }}';
                
                const loadDeliveryDataTable = (status,bus,driver,customer,except_status,except_stat) => {
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
                                except_status : except_status,
                                except_stat : except_stat

                            },
                            dataSrc: function ( json ) {
    
                            //console.log(json);
    
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
                                name: 'address',
                                data: 'address'
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
                                name: 'comment',
                                data: 'comment'
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
                let except_status = 3;
                let except_stat = 4;

                let status = $('#filterByStatus').val();
                let bus = $('#filterByBus').val();
                let driver = $('#filterByDriver').val();
                let customer = $('#filterByCustomer').val();

                loadDeliveryDataTable(status,bus,driver,customer,except_status,except_stat);
    

                
                $('#filterByStatus').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $(this).val();
                    var bus = $('#filterByBus').val();
                    var driver = $('#filterByDriver').val();
                    loadDeliveryDataTable(status,bus,driver,customer,except_status,except_stat);
                });



                
                $('#filterByBus').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var driver = $('#filterByDriver').val();
                    var bus = $(this).val();
                    loadDeliveryDataTable(status,bus,driver,customer,except_status,except_stat);
                });

                $('#filterByDriver').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var driver = $(this).val();
                    var bus = $('#filterByBus').val();
                    loadDeliveryDataTable(status,bus,driver,customer,except_status,except_stat);
                });

                $('#filterByCustomer').change(function () {
                   // $('.dataTables_wrapper').html('');
                    var status = $('#filterByStatus').val();
                    var customer = $(this).val();
                    var bus = $('#filterByBus').val();
                    loadDeliveryDataTable(status,bus,driver,customer,except_status,except_stat);
                });
               
                var selected_status = 1;
                var selected_bus = 1;
                var selected_driver = 1;
                $(document).on('click', '#btnStatusModal', function() {
                    $('#statusModal').attr('style','display:block');
                });

                $(document).on('click', '#btnBusModal', function() {
                    $('#busModal').attr('style','display:block');
                });

                window.updateCount = function() {
                    var x = $(".checkbox:checked").length;
                    document.getElementById("y").innerHTML ='Нйит '+ x+' мөр сонгосон байна';
                };


                $(document).on('click', '#btnDriveModal', function() {
                    $('#driveModal').attr('style','display:block');
                });
    
                $(document).on('click', '#btnDeleteModal', function() {
                    console.log("btnDeleteModal");
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


           
               
    
                // Handle to Export as a excel file
                

                
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
    
        <script>
        // When the user clicks on <span> (x), close the modal
            $(document).on('click', '.close', function() {
            $('#customModal').attr('style','display:none');
            $('#statusModal').attr('style','display:none');
            $('#driveModal').attr('style','display:none');
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

    /* Make pagination SVG icons smaller */
    .card-footer svg,
    nav[role="navigation"] svg {
        width: 12px !important;
        height: 12px !important;
    }
</style>

  @endsection