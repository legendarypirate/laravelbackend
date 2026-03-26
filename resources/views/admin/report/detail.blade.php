@extends('admin.master')

@section('mainContent')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css"
        integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg=="
        crossorigin="anonymous" />

    <style>
        .date-filter {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-buttons {
            display: flex;
            align-items: end;
            gap: 10px;
        }
        .filter-buttons .btn {
            height: 38px;
        }
        .checkbox-lg {
            width: 20px !important;
            height: 20px !important;
            cursor: pointer;
        }
        .dt-body-center {
            text-align: center;
            vertical-align: middle;
        }
        .driver-info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .driver-info-item {
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .driver-info-label {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
            display: inline-block;
        }
        .driver-info-value {
            color: #6c757d;
        }
        .info-section {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .nav-tabs .nav-link {
            color: #007bff;
        }
        .tab-content {
            padding-top: 20px;
        }
        .phone-list .phone-item {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        .phone-list .phone-item:hover {
            background-color: #f8f9fa !important;
            border-color: #007bff;
            transform: translateX(5px);
        }
        .phone-list .phone-item a:hover {
            color: #007bff !important;
        }
        .history-badge-in {
            background-color: #28a745;
            color: white;
        }
        .history-badge-out {
            background-color: #dc3545;
            color: white;
        }
        .history-item {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .history-item.in {
            border-left-color: #28a745;
        }
        .history-item.out {
            border-left-color: #dc3545;
        }
        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Жолоочийн дэлгэрэнгүй</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Жолоочийн мэдээлэл</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
            <?php $driverInfo = DB::table('users')->where('name', $driver)->first(); ?>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Driver Information Card -->
              @if(isset($driverInfo) && $driverInfo)
<div class="row">
    <div class="col-12">
        <div class="card driver-info-card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary rounded-circle p-3 me-3">
                        <i class="fas fa-user text-white fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-0 text-primary">Жолоочийн мэдээлэл</h4>
                </div>
                
                <!-- Basic Information -->
                <div class="info-section border-start border-primary border-4">  
                    <h5 class="text-primary mb-3 d-flex align-items-center">
                        <i class="fas fa-id-card me-2"></i>Үндсэн мэдээлэл
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="driver-info-item p-3 bg-light rounded">
                                <div class="driver-info-label text-muted small mb-1">
                                    <i class="fas fa-signature me-1"></i>Нэр
                                </div>
                                <div class="driver-info-value fw-bold text-dark">{{ $driverInfo->name ?? 'Мэдээлэл байхгүй' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="driver-info-item p-3 bg-light rounded">
                                <div class="driver-info-label text-muted small mb-1">
                                    <i class="fas fa-envelope me-1"></i>Имэйл
                                </div>
                                <div class="driver-info-value fw-bold text-dark">{{ $driverInfo->email ?? 'Мэдээлэл байхгүй' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="driver-info-item p-3 bg-light rounded">
                                <div class="driver-info-label text-muted small mb-1">
                                    <i class="fas fa-calendar-plus me-1"></i>Бүртгүүлсэн
                                </div>
                                <div class="driver-info-value fw-bold text-dark">
                                    @if($driverInfo->created_at)
                                        {{ \Carbon\Carbon::parse($driverInfo->created_at)->format('Y-m-d H:i') }}
                                    @else
                                        Мэдээлэл байхгүй
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="info-section border-start border-success border-4 mt-4">
                    <h5 class="text-success mb-3 d-flex align-items-center">
                        <i class="fas fa-address-book me-2"></i>Холбоо барих мэдээлэл
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="driver-info-item p-3 bg-light rounded h-100">
                                <div class="driver-info-label text-muted small mb-2">
                                    <i class="fas fa-mobile-alt me-1"></i>Утасны дугаар
                                </div>
                                <div class="driver-info-value">
                                    <?php 
                                    $driverPhones = DB::table('phones')->where('userid', $driverInfo->id)->get();
                                    ?>
                                    @if($driverPhones->count() > 0)
                                        <div class="phone-list">
                                            @foreach($driverPhones as $index => $phone)
                                                <div class="phone-item d-flex align-items-center mb-2 p-2 rounded bg-white">
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <a href="tel:{{ $phone->phone }}" class="text-decoration-none fw-bold text-dark flex-grow-1">
                                                        {{ $phone->phone }}
                                                    </a>
                                                    <span class="badge bg-secondary ms-2">#{{ $index + 1 }}</span>
                                                </div>
                                            @endforeach
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle me-1"></i>Нийт {{ $driverPhones->count() }} утас
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted d-flex align-items-center">
                                            <i class="fas fa-phone-slash me-2"></i>Мэдээлэл байхгүй
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="driver-info-item p-3 bg-light rounded h-100">
                                <div class="driver-info-label text-muted small mb-2">
                                    <i class="fas fa-map-marked-alt me-1"></i>Хаяг
                                </div>
                                <div class="driver-info-value">
                                    <?php 
                                    $driverAddress = DB::table('addresses')->where('userid', $driverInfo->id)->first();
                                    ?>
                                    @if($driverAddress && $driverAddress->address)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            <span class="fw-bold text-dark">{{ $driverAddress->address }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-2"></i>Мэдээлэл байхгүй
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

                <!-- Tabs Navigation -->
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="driverTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="deliveries-tab" data-bs-toggle="tab" data-bs-target="#deliveries" type="button" role="tab" aria-controls="deliveries" aria-selected="true">
                                    <i class="fas fa-truck me-1"></i>Хүргэлт
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="false">
                                    <i class="fas fa-box me-1"></i>Бараа
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="driverTabsContent">
                            <!-- Deliveries Tab -->
                            <div class="tab-pane fade show active" id="deliveries" role="tabpanel" aria-labelledby="deliveries-tab">
                                <!-- Date Filter Section -->
                                <div class="row date-filter">
                                    <!-- Status Filter -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status_filter">Төлөв:</label>
                                            <select id="status_filter" class="form-control">
                                                <option value="">Бүгд</option>
                                                <option value="3">Хүргэгдсэн</option>
                                                <option value="4">Цуцалсан</option>
                                                <option value="5">Буцаасан</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Date Filters -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="start_date">Эхлэх огноо:</label>
                                            <input type="date" id="start_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="end_date">Дуусах огноо:</label>
                                            <input type="date" id="end_date" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="col-md-5">
                                        <div class="filter-buttons">
                                            <div class="form-group flex-fill">
                                                <button type="button" id="apply_date_filter" class="btn btn-primary btn-block">
                                                    Шүүх
                                                </button>
                                            </div>
                                            <div class="form-group flex-fill">
                                                <button type="button" id="clear_date_filter" class="btn btn-secondary btn-block">
                                                    Цэвэрлэх
                                                </button>
                                            </div>
                                            <div class="form-group flex-fill">
                                                <button type="button" id="export_selected_excel" class="btn btn-success btn-block">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery List -->
                                <div class="row" style="margin-bottom:10px!important;">
                                    <div class="col-12">
                                        <button type="button" id="__btnPrint" class="btn btn-info" style="background-color:#032EF1;">
                                            <a href="#" style="color:white;">Хэвлэх</a>
                                        </button>
                                        <button type="button" id="__btnExcelExport" class="btn btn-info" style="background-color:#032EF1;">
                                            <a href="#" style="color:white;">Экселээр гаргаж авах</a>
                                        </button>

                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Жолоочийн хүргэлтийн жагсаалт</h5>
                                                <div class="card-body table-wrapper table-responsive p-0">
                                                    <table class="table table-hover text-nowrap small" id="datatable" style="width:100% !important;">
                                                        <thead>
                                                            <tr>
                                                                <th class="whitespace-nowrap dt-body-center">
                                                                    <input type="checkbox" class="checkbox-lg" id="selectAll">
                                                                </th>
                                                                <th class="text-center whitespace-nowrap table-info">Track ID</th>
                                                                <th class="text-center whitespace-nowrap table-info">Огноо</th>
                                                                <th class="text-center whitespace-nowrap table-warning">Төрөл</th>
                                                                <th class="text-center whitespace-nowrap table-warning">Харилцагч</th>
                                                                <th class="text-center whitespace-nowrap table-warning">z-код</th>
                                                                <th class="whitespace-nowrap table-warning">Статус</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>

                                                <div class="action-buttons">
                                                    <button class="btn btn-primary shadow-md mr-2"><span id="y">0 </span> мөр сонгосон</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items Tab -->
                            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
                                <!-- Date Filter Section for Items -->
                                <div class="row date-filter">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="item_start_date">Эхлэх огноо:</label>
                                            <input type="date" id="item_start_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="item_end_date">Дуусах огноо:</label>
                                            <input type="date" id="item_end_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="filter-buttons">
                                            <div class="form-group flex-fill">
                                                <button type="button" id="apply_item_filter" class="btn btn-primary btn-block">
                                                    Шүүх
                                                </button>
                                            </div>
                                            <div class="form-group flex-fill">
                                                <button type="button" id="clear_item_filter" class="btn btn-secondary btn-block">
                                                    Цэвэрлэх
                                                </button>
                                            </div>
                                            <div class="form-group flex-fill">
                                                <button type="button" id="export_items_excel" class="btn btn-success btn-block">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </button>
                                            </div>
                                            <div class="form-group flex-fill">
                                                <button type="button" id="print_items" class="btn btn-info btn-block">
                                                    <i class="fas fa-print"></i> Хэвлэх
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Жолоочийн барааны жагсаалт</h5>
                                            </div>
                                            <div class="card-body table-wrapper table-responsive p-0">
                                                <table class="table table-hover text-nowrap small" id="itemsTable" style="width:100% !important;">
                                                    <thead>
                                                        <tr>
                                                            <th class="dt-body-center">
                                                                <input type="checkbox" class="checkbox-lg" id="selectAllItems">
                                                            </th>
                                                            <th class="text-center whitespace-nowrap table-info">#</th>
                                                            <th class="text-center whitespace-nowrap table-info">Барааны нэр</th>
                                                            <th class="text-center whitespace-nowrap table-warning">Тоо ширхэг</th>
                                                            <th class="text-center whitespace-nowrap table-warning">Нэмсэн огноо</th>
                                                            <th class="text-center whitespace-nowrap table-warning">Шинэчилсэн огноо</th>
                                                            <th class="text-center whitespace-nowrap table-info">Үйлдэл</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Items will be loaded here -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Selected items count -->
                                            <div class="action-buttons">
                                                <button class="btn btn-primary shadow-md mr-2"><span id="selectedItemsCount">0 </span> бараа сонгосон</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Барааны түүх</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="historyLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Ачаалж байна...</span>
                        </div>
                        <p class="mt-2">Түүхийг ачаалж байна...</p>
                    </div>
                    <div id="historyContent" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Барааны нэр:</strong> <span id="historyItemName"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Нийт тоо ширхэг:</strong> <span id="historyTotalQuantity"></span>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label small mb-0">Огноо (эхлэх)</label>
                                <input type="date" id="historyDateFrom" class="form-control form-control-sm" style="max-width: 160px;">
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-0">Огноо (дуусах)</label>
                                <input type="date" id="historyDateTo" class="form-control form-control-sm" style="max-width: 160px;">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="historyDateClear">Цэвэрлэх</button>
                            </div>
                        </div>
                        <hr>
                        <h6>Гүйлгээний түүх</h6>
                        <div id="historyList" class="mt-3">
                            <!-- History items will be loaded here -->
                        </div>
                    </div>
                    <div id="historyEmpty" class="text-center py-4" style="display: none;">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Гүйлгээний түүх олдсонгүй</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="historyPrintBtn" style="display: none;"><i class="fas fa-print me-1"></i>Хэвлэх</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
        let driver = '{{ $driver }}';
        const driverTableUrl = '{{ route('get-driver-delivery-info') }}';
        const exportExcelUrl = '{{ route('export.driver.excel') }}';
        const driverItemsUrl = '{{ route('get-driver-items') }}';
        const itemHistoryBaseUrl = '{{ url("/item/history") }}';
        const exportItemsExcelUrl = '{{ route('export.items.excel') }}';
        
        var rows_selected = [];
        var items_selected = [];
        var dataTable;
        var itemsData = [];

        // Initialize DataTable
        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: driverTableUrl,
                type: 'GET',
                data: function(d) {
                    d.driver = driver;
                    d.status = $('#status_filter').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                {
                    name: 'checkbox',
                    data: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    name: 'track',
                    data: 'track',
                },
                {
                    name: 'created_at',
                    data: 'created_at',
                },
                {
                    name: 'type',
                    data: 'type',
                },
                {
                    name: 'shop',
                    data: 'shop',
                },
                {
                    name: 'order_code',
                    data: 'order_code',
                },
                {
                    name: 'status',
                    data: 'status',
                },
            ],
            columnDefs: [
               {
                    targets: 0,
                    className: 'dt-body-center',
                    width: '50px',
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="checkbox-lg" name="foo" data-id="' + row.id + '" value="' + row.id + '">';
                    }
                },
                {
                    targets: [1, 2],
                    className: 'text-center table-info'
                },
                {
                    targets: [3, 4, 5, 6],
                    className: 'text-center table-warning'
                },
            ],
            paginationType: 'numbers',
            "language": {
                "search": "Хайх:"
            },
            lengthMenu: [50, 100, 150, 300],
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:not(:first-child)').css('cursor', 'pointer');
            }
        });

        // Load items when items tab is clicked
        $('#items-tab').on('click', function() {
            loadDriverItems();
        });

        function loadDriverItems() {
            $.ajax({
                url: driverItemsUrl,
                type: 'GET',
                data: {
                    driver: driver,
                    start_date: $('#item_start_date').val(),
                    end_date: $('#item_end_date').val()
                },
                success: function(response) {
                    let itemsTable = $('#itemsTable tbody');
                    itemsTable.empty();
                    items_selected = [];
                    updateItemsCount();
                    
                    if (response.data && response.data.length > 0) {
                        itemsData = response.data;
                        
                        $.each(response.data, function(index, item) {
                            itemsTable.append(
                                '<tr>' +
                                    '<td class="dt-body-center">' +
                                        '<input type="checkbox" class="checkbox-lg item-checkbox" data-item-id="' + item.id + '" value="' + item.id + '">' +
                                    '</td>' +
                                    '<td class="text-center">' + (index + 1) + '</td>' +
                                    '<td class="text-center">' + (item.item_name || 'Мэдээлэл байхгүй') + '</td>' +
                                    '<td class="text-center">' + item.quantity + '</td>' +
                                    '<td class="text-center">' + (item.created_at ? new Date(item.created_at).toLocaleDateString('mn-MN') : '-') + '</td>' +
                                    '<td class="text-center">' + (item.updated_at ? new Date(item.updated_at).toLocaleDateString('mn-MN') : '-') + '</td>' +
                                    '<td class="text-center">' +
                                        '<button class="btn btn-sm btn-info view-history" data-item-id="' + item.item_id + '" data-item-name="' + (item.item_name || '') + '" data-quantity="' + item.quantity + '">' +
                                            '<i class="fas fa-history me-1"></i>Түүх харах' +
                                        '</button>' +
                                    '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        itemsTable.append(
                            '<tr>' +
                                '<td colspan="7" class="text-center">Барааны мэдээлэл олдсонгүй</td>' +
                            '</tr>'
                        );
                        itemsData = [];
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading driver items:', error);
                    let itemsTable = $('#itemsTable tbody');
                    itemsTable.empty();
                    itemsTable.append(
                        '<tr>' +
                            '<td colspan="7" class="text-center text-danger">Барааны мэдээлэл ачаалахад алдаа гарлаа</td>' +
                        '</tr>'
                    );
                    itemsData = [];
                }
            });
        }

        // View history button click
        $(document).on('click', '.view-history', function() {
            const itemId = $(this).data('item-id');
            const itemName = $(this).data('item-name');
            const quantity = $(this).data('quantity');
            
            showItemHistory(itemId, itemName, quantity);
        });

        var currentModalHistoryData = [];
        var currentModalItemName = '';
        var currentModalQuantity = '';

        function getFilteredHistoryByDate() {
            var from = $('#historyDateFrom').val();
            var to = $('#historyDateTo').val();
            var list = currentModalHistoryData;
            if (from) {
                var fromDate = new Date(from);
                fromDate.setHours(0, 0, 0, 0);
                list = list.filter(function(h) { return new Date(h.created_at) >= fromDate; });
            }
            if (to) {
                var toDate = new Date(to);
                toDate.setHours(23, 59, 59, 999);
                list = list.filter(function(h) { return new Date(h.created_at) <= toDate; });
            }
            return list;
        }

        function renderHistoryList(data) {
            var historyList = $('#historyList');
            historyList.empty();
            $.each(data, function(index, history) {
                var historyClass = history.type === 'in' ? 'in' : 'out';
                var badgeClass = history.type === 'in' ? 'history-badge-in' : 'history-badge-out';
                var typeText = history.type === 'in' ? 'ОРЛОГО' : 'ЗАРАЛТ';
                var operationDate = history.created_at ? new Date(history.created_at).toLocaleString('mn-MN') : '-';
                historyList.append(
                    '<div class="history-item ' + historyClass + '">' +
                        '<div class="d-flex justify-content-between align-items-start">' +
                            '<div class="flex-grow-1">' +
                                '<div class="d-flex align-items-center mb-2">' +
                                    '<span class="badge ' + badgeClass + ' me-2">' + typeText + '</span>' +
                                    '<strong class="me-2">' + history.quantity + ' ширхэг</strong>' +
                                '</div>' +
                                '<div class="mb-1">' +
                                    '<small class="text-muted"><strong>Тайлбар:</strong> ' + (history.comment || 'Тайлбаргүй') + '</small>' +
                                '</div>' +
                                '<div class="mb-1">' +
                                    '<small class="text-muted"><strong>Барааны нэр:</strong> ' + (history.goodname || 'Мэдээлэл байхгүй') + '</small>' +
                                '</div>' +
                                '<div class="mb-1">' +
                                    '<small class="text-muted"><strong>Хэрэглэгч:</strong> ' + (history.user_id || 'Мэдээлэл байхгүй') + '</small>' +
                                '</div>' +
                                '<div class="mb-1">' +
                                    '<small class="text-muted"><strong>Үйлдэл хийсэн:</strong> ' + (history.operation_by || 'Мэдээлэл байхгүй') + '</small>' +
                                '</div>' +
                            '</div>' +
                            '<div class="text-end">' +
                                '<small class="text-muted">' + operationDate + '</small>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            });
        }

        function applyHistoryDateFilter() {
            var filtered = getFilteredHistoryByDate();
            renderHistoryList(filtered);
        }

        $('#historyDateFrom, #historyDateTo').on('change', function() { applyHistoryDateFilter(); });
        $('#historyDateClear').on('click', function() {
            $('#historyDateFrom').val('');
            $('#historyDateTo').val('');
            applyHistoryDateFilter();
        });

        function esc(s) { return (s || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
        $('#historyPrintBtn').on('click', function() {
            var filtered = getFilteredHistoryByDate();
            var printRows = filtered.map(function(h) {
                var d = h.created_at ? new Date(h.created_at).toLocaleString('mn-MN') : '-';
                var type = h.type === 'in' ? 'Орлого' : 'Зарлага';
                var qty = (h.type === 'in' ? '+' : '-') + Math.abs(h.quantity);
                var addr = esc(h.receiver_address);
                var phone = [h.receiver_phone, h.receiver_phone2].filter(Boolean).join(' / ');
                var deliveryComment = esc(h.delivery_comment);
                return '<tr><td>' + d + '</td><td>' + type + '</td><td>' + qty + '</td><td>' + esc(h.comment) + '</td><td>' + addr + '</td><td>' + esc(phone) + '</td><td>' + deliveryComment + '</td></tr>';
            }).join('');
            var w = window.open('', '_blank');
            w.document.write(
                '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Барааны түүх</title>' +
                '<style>body{font-family:system-ui,sans-serif;font-size:12px;padding:16px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #333;padding:6px 8px;text-align:left;}th{background:#eee;}</style></head><body>' +
                '<p><strong>Бараа:</strong> ' + esc(currentModalItemName) + '</p>' +
                '<p><strong>Нийт тоо ширхэг:</strong> ' + (currentModalQuantity || '') + '</p>' +
                '<table><thead><tr><th>Огноо</th><th>Төрөл</th><th>Тоо</th><th>Тайлбар</th><th>Хүлээн авагчийн хаяг</th><th>Утас</th><th>Нэмэлт тайлбар</th></tr></thead><tbody>' + printRows + '</tbody></table>' +
                '</body></html>'
            );
            w.document.close();
            w.focus();
            setTimeout(function() { w.print(); w.close(); }, 250);
        });

        function showItemHistory(itemId, itemName, quantity) {
            const modal = new bootstrap.Modal(document.getElementById('historyModal'));
            modal.show();
            
            $('#historyLoading').show();
            $('#historyContent').hide();
            $('#historyEmpty').hide();
            $('#historyPrintBtn').hide();
            $('#historyDateFrom').val('');
            $('#historyDateTo').val('');
            currentModalHistoryData = [];
            currentModalItemName = itemName || '';
            currentModalQuantity = quantity || '';
            
            $('#historyItemName').text(itemName);
            $('#historyTotalQuantity').text(quantity);
            
            $.ajax({
                url: itemHistoryBaseUrl + '/' + encodeURIComponent(itemId),
                type: 'GET',
                success: function(response) {
                    $('#historyLoading').hide();
                    const data = (response && response.data) ? response.data : [];
                    currentModalHistoryData = data;
                    if (data.length > 0) {
                        applyHistoryDateFilter();
                        $('#historyPrintBtn').show();
                        $('#historyContent').show();
                    } else {
                        $('#historyEmpty').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading item history:', error);
                    $('#historyLoading').hide();
                    $('#historyEmpty').show();
                }
            });
        }

        // Handle checkbox changes for deliveries
        $('#datatable tbody').on('click', 'input[type="checkbox"]', function(e) {
            e.stopPropagation();
            var rowId = $(this).data('id');
            var index = $.inArray(rowId, rows_selected);

            if (this.checked && index === -1) {
                rows_selected.push(rowId);
            } else if (!this.checked && index !== -1) {
                rows_selected.splice(index, 1);
            }
            
            updateCount();
        });

        // Select all checkbox for deliveries
        $('#selectAll').on('click', function() {
            var isChecked = this.checked;
            
            $('#datatable tbody input[type="checkbox"]').each(function() {
                var rowId = $(this).data('id');
                var index = $.inArray(rowId, rows_selected);
                
                if (isChecked) {
                    this.checked = true;
                    if (index === -1) {
                        rows_selected.push(rowId);
                    }
                } else {
                    this.checked = false;
                    if (index !== -1) {
                        rows_selected.splice(index, 1);
                    }
                }
            });
            
            updateCount();
        });

        // Handle row click for deliveries
        $('#datatable tbody').on('click', 'tr', function(e) {
            if (!$(e.target).is('input[type="checkbox"]')) {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
                checkbox.trigger('click');
            }
        });

        function updateCount() {
            var x = rows_selected.length;
            $('#y').text('Нийт ' + x + ' мөр сонгосон байна');
            
            var totalCheckboxes = $('#datatable tbody input[type="checkbox"]').length;
            var checkedCheckboxes = $('#datatable tbody input[type="checkbox"]:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        }

        // Барааны checkbox сонголт
        $(document).on('click', '.item-checkbox', function() {
            const itemId = $(this).data('item-id');
            const index = items_selected.indexOf(itemId);

            if (this.checked && index === -1) {
                items_selected.push(itemId);
            } else if (!this.checked && index !== -1) {
                items_selected.splice(index, 1);
            }
            
            updateItemsCount();
        });

        // Бүх барааг сонгох
        $('#selectAllItems').on('click', function() {
            const isChecked = this.checked;
            items_selected = [];
            
            $('.item-checkbox').each(function() {
                this.checked = isChecked;
                if (isChecked) {
                    items_selected.push($(this).data('item-id'));
                }
            });
            
            updateItemsCount();
        });

        // Сонгогдсон барааны тоог шинэчлэх
        function updateItemsCount() {
            $('#selectedItemsCount').text(items_selected.length);
            $('#selectAllItems').prop('checked', 
                items_selected.length > 0 && 
                items_selected.length === $('.item-checkbox').length
            );
        }

        // Барааны Excel экспорт
        $('#export_items_excel').click(function() {
            if (items_selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Анхаар',
                    text: 'Та экспортлох бараагаа сонгоогүй байна!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Excel файл боловсруулж байна...',
                text: 'Түр хүлээнэ үү',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('driver', driver);
            
            if ($('#item_start_date').val()) {
                formData.append('start_date', $('#item_start_date').val());
            }
            if ($('#item_end_date').val()) {
                formData.append('end_date', $('#item_end_date').val());
            }
            
            items_selected.forEach(id => {
                formData.append('item_ids[]', id);
            });

            fetch(exportItemsExcelUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `driver_items_${driver}_${new Date().toISOString().split('T')[0]}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                Swal.close();
            })
            .catch(error => {
                console.error('Export error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Алдаа',
                    text: 'Excel файл үүсгэхэд алдаа гарлаа: ' + error.message,
                    confirmButtonText: 'OK'
                });
            });
        });

        // Бараа хэвлэх
        $('#print_items').click(function() {
            let itemsToPrint = [];
            
            if (items_selected.length > 0) {
                itemsToPrint = itemsData.filter(item => items_selected.includes(item.id.toString()));
            } else {
                itemsToPrint = itemsData;
            }

            if (itemsToPrint.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Анхаар',
                    text: 'Хэвлэх барааны мэдээлэл олдсонгүй!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const printWindow = window.open('', '_blank');
            const printDate = new Date().toLocaleDateString('mn-MN');
            
            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Жолоочийн барааны жагсаалт</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .header h1 { margin: 0; color: #333; }
                        .info { margin-bottom: 20px; }
                        .info table { width: 100%; border-collapse: collapse; }
                        .info td { padding: 5px; border: 1px solid #ddd; }
                        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; font-weight: bold; }
                        .text-center { text-align: center; }
                        .footer { margin-top: 30px; text-align: right; font-size: 12px; color: #666; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Жолоочийн барааны жагсаалт</h1>
                        <p>Хэвлэсэн огноо: ${printDate}</p>
                    </div>
                    
                    <div class="info">
                        <table>
                            <tr>
                                <td><strong>Жолоочийн нэр:</strong></td>
                                <td>${driver}</td>
                                <td><strong>Нийт бараа:</strong></td>
                                <td>${itemsToPrint.length} ширхэг</td>
                            </tr>
                            <tr>
                                <td><strong>Эхлэх огноо:</strong></td>
                                <td>${$('#item_start_date').val() || 'Бүгд'}</td>
                                <td><strong>Дуусах огноо:</strong></td>
                                <td>${$('#item_end_date').val() || 'Бүгд'}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Барааны нэр</th>
                                <th class="text-center">Тоо ширхэг</th>
                                <th>Нэмсэн огноо</th>
                                <th>Шинэчилсэн огноо</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            itemsToPrint.forEach((item, index) => {
                printContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name || 'Мэдээлэл байхгүй'}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td>${item.created_at ? new Date(item.created_at).toLocaleDateString('mn-MN') : '-'}</td>
                        <td>${item.updated_at ? new Date(item.updated_at).toLocaleDateString('mn-MN') : '-'}</td>
                    </tr>
                `;
            });

            printContent += `
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        <p>Хэвлэгдсэн: ${new Date().toLocaleString('mn-MN')}</p>
                    </div>
                    
                    <div class="no-print" style="margin-top: 20px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Хэвлэх
                        </button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                            Хаах
                        </button>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.write(printContent);
            printWindow.document.close();
        });

        // Export selected deliveries to Excel
        $('#export_selected_excel').click(function() {
            console.log('Export clicked - Selected IDs:', rows_selected);
            
            if (rows_selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Анхаар',
                    text: 'Та хэвлэх мөрөө сонгоогүй байна!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Excel файл боловсруулж байна...',
                text: 'Түр хүлээнэ үү',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('driver', driver);
            formData.append('status', $('#status_filter').val());
            
            if ($('#start_date').val()) {
                formData.append('start_date', $('#start_date').val());
            }
            if ($('#end_date').val()) {
                formData.append('end_date', $('#end_date').val());
            }
            
            rows_selected.forEach(id => {
                formData.append('ids[]', id);
            });

            fetch(exportExcelUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `driver_deliveries_${driver}_${new Date().toISOString().split('T')[0]}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                Swal.close();
            })
            .catch(error => {
                console.error('Export error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Алдаа',
                    text: 'Excel файл үүсгэхэд алдаа гарлаа: ' + error.message,
                    confirmButtonText: 'OK'
                });
            });
        });

        // Apply all filters for deliveries
        $('#apply_date_filter').click(function() {
            dataTable.ajax.reload();
            rows_selected = [];
            updateCount();
        });

        // Clear all filters for deliveries
        $('#clear_date_filter').click(function() {
            $('#status_filter').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            dataTable.ajax.reload();
            rows_selected = [];
            updateCount();
        });

        // Apply filter when Enter key is pressed in inputs
        $('#status_filter, #start_date, #end_date').keypress(function(e) {
            if (e.which == 13) {
                dataTable.ajax.reload();
                rows_selected = [];
                updateCount();
            }
        });

        // Apply filter when status selection changes
        $('#status_filter').change(function() {
            dataTable.ajax.reload();
            rows_selected = [];
            updateCount();
        });

        // Барааны шүүлтүүр
        $('#apply_item_filter').click(function() {
            loadDriverItems();
        });

        $('#clear_item_filter').click(function() {
            $('#item_start_date').val('');
            $('#item_end_date').val('');
            loadDriverItems();
        });
    });
</script>

    <!-- Include your existing scripts and styles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.all.min.js"
        integrity="sha512-LXVbtSLdKM9Rpog8WtfAbD3Wks1NSDE7tMwOW3XbQTPQnaTrpIot0rzzekOslA1DVbXSVzS7c/lWZHRGkn3Xpg=="
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('sweetalert::alert')
@endsection