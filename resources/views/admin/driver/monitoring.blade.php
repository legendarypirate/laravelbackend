@extends('admin.master')

@section('mainContent')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css"
        integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg=="
        crossorigin="anonymous" />

    <style>
        .driver-select-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stats-card.total { border-left-color: #007bff; }
        .stats-card.active { border-left-color: #28a745; }
        .stats-card.completed { border-left-color: #17a2b8; }
        .stats-card.cancelled { border-left-color: #dc3545; }
        .stats-card.items-carrying { border-left-color: #ffc107; }
        .stats-card.items-delivered { border-left-color: #6f42c1; }
        .stats-card.delivery-price { border-left-color: #20c997; }
        .stats-card.total { cursor: pointer; }

        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.35);
            z-index: 1040;
            display: none;
        }
        .right-drawer {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 420px;
            background: #fff;
            z-index: 1050;
            transform: translateX(100%);
            transition: transform 0.25s ease;
            overflow-y: auto;
            border-left: 1px solid #dee2e6;
            display: none;
            padding: 20px;
        }
        .right-drawer.open {
            transform: translateX(0);
        }
        .shop-breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .shop-breakdown-header {
            cursor: pointer;
        }
        .shop-breakdown-details {
            background: #f8f9fa;
            border-left: 3px solid #dee2e6;
            padding: 10px 12px;
            display: none;
        }
        .shop-breakdown-details.open {
            display: block;
        }
        .filter-section {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .delivery-item-row {
            cursor: pointer;
        }
        .delivery-item-row:hover {
            background-color: #f8f9fa;
        }
        .items-modal .item-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .badge {
            font-size: 0.875rem;
            padding: 0.5em 0.75em;
        }
        .selected-driver-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
        }
        .driver-list-item {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .driver-list-item:hover {
            background-color: #f8f9fa;
        }
        .driver-list-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Жолооч мониторинг (New)</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Жолооч мониторинг</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Driver Selection Section -->
                <div class="driver-select-section">
                    <h4 class="mb-3"><i class="fas fa-user-circle"></i> Жолооч сонгох</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="driverSelect">Идэвхтэй жолооч сонгох:</label>
                            <select id="driverSelect" class="form-control">
                                <option value="">-- Жолооч сонгох --</option>
                            </select>
                        </div>
                    </div>
                    <div class="selected-driver-info mt-3" id="selectedDriverInfo" style="display:none;">
                        <strong>Сонгосон жолооч:</strong> <span id="selectedDriverName"></span>
                        <br><strong>Утас:</strong> <span id="selectedDriverPhone"></span>
                    </div>
                </div>

                <!-- Summary Statistics Cards -->
                <div class="row" id="statsSection" style="display:none;">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info stats-card total" id="statTotalDeliveriesCard">
                            <div class="inner">
                                <h3 id="statTotalDeliveries">0</h3>
                                <p>Нийт хүргэлт</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success stats-card active">
                            <div class="inner">
                                <h3 id="statActiveDeliveries">0</h3>
                                <p>Идэвхтэй хүргэлт</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info stats-card completed" id="statCompletedDeliveriesCard">
                            <div class="inner">
                                <h3 id="statCompletedDeliveries">0</h3>
                                <p>Дууссан хүргэлт</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger stats-card cancelled">
                            <div class="inner">
                                <h3 id="statCancelledDeliveries">0</h3>
                                <p>Цуцалсан хүргэлт</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning stats-card items-carrying">
                            <div class="inner">
                                <h3 id="statItemsCarrying">0</h3>
                                <p>Тээвэрлэж буй бараа</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-purple stats-card items-delivered">
                            <div class="inner">
                                <h3 id="statItemsDelivered">0</h3>
                                <p>Хүргэгдсэн бараа</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success stats-card delivery-price">
                            <div class="inner">
                                <h3 id="statTotalDeliveryPrice">0</h3>
                                <p>Нийт хүргэлтийн үнэ</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="filter-section" id="filtersSection" style="display:none;">
                    <h5 class="mb-3"><i class="fas fa-filter"></i> Шүүлт</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="filterStartDate">Эхлэх огноо:</label>
                            <input type="date" id="filterStartDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="filterEndDate">Дуусах огноо:</label>
                            <input type="date" id="filterEndDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="filterStatus">Төлөв:</label>
                            <select id="filterStatus" class="form-control">
                                <option value="">Бүгд</option>
                                <option value="1">Бүртгэгдсэн</option>
                                <option value="2">Хуваарилсан</option>
                                <option value="3">Хүргэгдсэн</option>
                                <option value="4">Цуцалсан</option>
                                <option value="5">Буцаасан</option>
                                <option value="6">Хүлээгдэж буй</option>
                                <option value="10">Хүлээн авсан</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterMerchant">Худалдаачин:</label>
                            <select id="filterMerchant" class="form-control">
                                <option value="">Бүгд</option>
                                @php
                                    $merchants = DB::table('merchant')->select('id', 'merchantName')->get();
                                @endphp
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}">{{ $merchant->merchantName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" id="applyFilters" class="btn btn-primary">
                                <i class="fas fa-search"></i> Шүүх
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Цэвэрлэх
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Driver Items Section -->
                <div class="card" id="driverItemsCard" style="display:none;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-shopping-bag"></i> Жолоочийн тээвэрлэж буй бараа</h3>
                    </div>
                    <div class="card-body">
                        <div id="driverItemsList">
                            <p class="text-center">Ачааллаж байна...</p>
                        </div>
                    </div>
                </div>

                <!-- Deliveries Table -->
                <div class="card" id="deliveriesCard" style="display:none;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Хүргэлтийн жагсаалт</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover" id="deliveriesTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Хүргэлтийн ID</th>
                                    <th>Төлөв</th>
                                    <th>Худалдаачин</th>
                                    <th>Харилцагчийн хаяг</th>
                                    <th>Хуваарилсан огноо</th>
                                    <th>Дууссан огноо</th>
                                    <th>Бараа</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Shop breakdown drawer -->
                <div class="drawer-overlay" id="shopBreakdownOverlay"></div>
                <aside id="shopBreakdownDrawer" class="right-drawer" aria-hidden="true">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-store"></i> Дэлгүүрээрх дууссан хүргэлт</h5>
                        <button type="button" class="btn btn-sm btn-secondary" id="closeShopBreakdownDrawer">Хаах</button>
                    </div>
                    <div class="text-muted mb-3" id="shopBreakdownMeta"></div>
                    <div id="shopBreakdownList" class="list-group">
                        <p class="text-center text-muted mb-0">Ачааллаж байна...</p>
                    </div>
                </aside>
            </div><!-- /.container-fluid -->
        </section>
    </div>

    <!-- Items Modal -->
    <div class="modal fade" id="itemsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Хүргэлтийн бараа</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body items-modal">
                    <div class="item-list" id="itemsList">
                        <p class="text-center">Ачааллаж байна...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedDriverId = null;
        let selectedDriverName = null;
        let selectedDriverPhone = null;
        let deliveriesTable = null;

        $(document).ready(function() {
            // Load all active drivers on page load
            loadAllActiveDrivers();

            // Driver selection change
            $('#driverSelect').on('change', function() {
                const driverId = $(this).val();
                if (driverId) {
                    const option = $(this).find('option:selected');
                    const driverName = option.data('driver-name');
                    const driverPhone = option.data('driver-phone');
                    selectDriver(driverId, driverName, driverPhone);
                } else {
                    clearDriverSelection();
                }
            });

            // Apply filters
            $('#applyFilters').on('click', function() {
                loadDriverData();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#filterStartDate').val('');
                $('#filterEndDate').val('');
                $('#filterStatus').val('');
                $('#filterMerchant').val('');
                loadDriverData();
            });

            // Click on "Дууссан хүргэлт" to open shop breakdown drawer
            $('#statCompletedDeliveriesCard').on('click', function() {
                if (!selectedDriverId && !selectedDriverName) return;
                openShopBreakdownDrawer();
            });

            // Close drawer
            $('#closeShopBreakdownDrawer, #shopBreakdownOverlay').on('click', function() {
                closeShopBreakdownDrawer();
            });
        });

        function loadAllActiveDrivers() {
            $.ajax({
                url: '{{ route("admin.driver-monitoring.drivers") }}',
                method: 'GET',
                data: { search: '' }, // Empty search to get all active drivers
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        const driverSelect = $('#driverSelect');
                        driverSelect.empty();
                        driverSelect.append('<option value="">-- Жолооч сонгох --</option>');
                        
                        response.data.forEach(function(driver) {
                            const option = $('<option></option>')
                                .attr('value', driver.id)
                                .data('driver-name', driver.name)
                                .data('driver-phone', driver.phone || '')
                                .text(driver.name + (driver.phone ? ' (' + driver.phone + ')' : ''));
                            driverSelect.append(option);
                        });
                    } else {
                        $('#driverSelect').html('<option value="">Жолооч олдсонгүй</option>');
                    }
                },
                error: function() {
                    $('#driverSelect').html('<option value="">Алдаа гарлаа</option>');
                }
            });
        }

        function selectDriver(driverId, driverName, driverPhone) {
            selectedDriverId = driverId;
            selectedDriverName = driverName;
            selectedDriverPhone = driverPhone;

            $('#selectedDriverName').text(driverName);
            $('#selectedDriverPhone').text(driverPhone || 'Мэдээлэл байхгүй');
            $('#selectedDriverInfo').show();

            // Show sections
            $('#statsSection').show();
            $('#filtersSection').show();
            $('#driverItemsCard').show();
            $('#deliveriesCard').show();

            // Load data
            loadDriverData();
        }

        function clearDriverSelection() {
            selectedDriverId = null;
            selectedDriverName = null;
            selectedDriverPhone = null;
            $('#driverSelect').val('');
            $('#selectedDriverInfo').hide();
            $('#statsSection').hide();
            $('#filtersSection').hide();
            $('#driverItemsCard').hide();
            $('#deliveriesCard').hide();
            
            if (deliveriesTable) {
                deliveriesTable.destroy();
                deliveriesTable = null;
            }

            closeShopBreakdownDrawer();
        }

        function openShopBreakdownDrawer() {
            $('#shopBreakdownMeta').text('');
            $('#shopBreakdownList').html('<p class="text-center text-muted mb-0">Ачааллаж байна...</p>');

            $('#shopBreakdownOverlay').show();
            $('#shopBreakdownDrawer').show().addClass('open');

            loadShopBreakdownList();
        }

        function closeShopBreakdownDrawer() {
            $('#shopBreakdownDrawer').removeClass('open').hide();
            $('#shopBreakdownOverlay').hide();
        }

        function loadShopBreakdownList() {
            if (!selectedDriverId && !selectedDriverName) {
                return;
            }

            $.ajax({
                url: '{{ route("admin.driver-monitoring.shops-breakdown") }}',
                method: 'GET',
                data: {
                    driver_id: selectedDriverId,
                    driver_name: selectedDriverName,
                    start_date: $('#filterStartDate').val(),
                    end_date: $('#filterEndDate').val(),
                    status: $('#filterStatus').val(),
                    merchant_id: $('#filterMerchant').val()
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const shops = response.data.shops || [];

                        if (shops.length === 0) {
                            $('#shopBreakdownList').html('<p class="text-center text-muted mb-0">Мэдээлэл байхгүй</p>');
                            return;
                        }

                        $('#shopBreakdownList').empty();

                        shops.forEach(function(shop, idx) {
                            const deliveriesCount = shop.deliveries_count || 0;
                            const deliveryPriceSum = shop.delivery_price_sum || 0;
                            const shopName = shop.shop_name || '-';
                            const safeId = 'shopBreakdown_' + idx;

                            const wrapper = $('<div class="list-group-item"></div>');
                            const header = $(
                                '<div class="shop-breakdown-row shop-breakdown-header" data-shop="' + $('<div>').text(shopName).html() + '" data-target="' + safeId + '"></div>'
                            );

                            header.append('<span class="text-truncate pr-2"><strong>' + $('<div>').text(shopName).html() + '</strong></span>');
                            header.append('<span class="text-right"><strong>' + deliveriesCount + '</strong></span>');
                            header.append('<span class="text-right text-muted small">Нийт үнэ: ' + Number(deliveryPriceSum).toLocaleString() + '</span>');

                            const details = $('<div class="shop-breakdown-details" id="' + safeId + '"><div class="text-muted small">Дэлгэрэнгүйг харахын тулд дарна уу</div></div>');

                            wrapper.append(header);
                            wrapper.append(details);
                            $('#shopBreakdownList').append(wrapper);
                        });
                    } else {
                        $('#shopBreakdownList').html('<p class="text-center text-danger mb-0">Мэдээлэл ачаалахад алдаа гарлаа</p>');
                    }
                },
                error: function() {
                    $('#shopBreakdownList').html('<p class="text-center text-danger mb-0">Алдаа гарлаа</p>');
                }
            });
        }

        // Expand / shrink shop breakdown row (lazy loads price tiers)
        $(document).on('click', '.shop-breakdown-header', function() {
            const shop = $(this).data('shop');
            const target = $(this).data('target');
            const $details = $('#' + target);

            if ($details.hasClass('open')) {
                $details.removeClass('open');
                return;
            }

            $details.addClass('open');

            // If already loaded once, do not reload
            if ($details.data('loaded') === 1) {
                return;
            }

            $details.html('<div class="text-center text-muted small">Ачааллаж байна...</div>');

            $.ajax({
                url: '{{ route("admin.driver-monitoring.shops-breakdown.prices") }}',
                method: 'GET',
                data: {
                    driver_id: selectedDriverId,
                    driver_name: selectedDriverName,
                    shop: shop,
                    start_date: $('#filterStartDate').val(),
                    end_date: $('#filterEndDate').val(),
                    merchant_id: $('#filterMerchant').val()
                },
                success: function(response) {
                    if (!response.success || !response.data) {
                        $details.html('<div class="text-danger small">Алдаа гарлаа</div>');
                        return;
                    }

                    const prices = response.data.prices || [];
                    if (prices.length === 0) {
                        $details.html('<div class="text-muted small">Мэдээлэл байхгүй</div>');
                        $details.data('loaded', 1);
                        return;
                    }

                    let html = '<div class="small font-weight-bold mb-2">Deliveryprice-р бүлэглэсэн (status=3)</div>';
                    html += '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Үнэ</th><th>Тоо</th><th>Нийт үнэ</th></tr></thead><tbody>';
                    prices.forEach(function(p) {
                        html += '<tr><td>' + Number(p.deliveryprice || 0).toLocaleString() + '</td><td>' + (p.deliveries_count || 0) + '</td><td>' + Number(p.delivery_price_sum || 0).toLocaleString() + '</td></tr>';
                    });
                    html += '</tbody></table>';
                    $details.html(html);
                    $details.data('loaded', 1);
                },
                error: function() {
                    $details.html('<div class="text-danger small">Алдаа гарлаа</div>');
                }
            });
        });

        function loadDriverData() {
            if (!selectedDriverId && !selectedDriverName) {
                return;
            }

            // Load statistics
            loadStatistics();

            // Load driver items
            loadDriverItems();

            // Load deliveries table
            loadDeliveriesTable();
        }

        function loadStatistics() {
            $.ajax({
                url: '{{ route("admin.driver-monitoring.data") }}',
                method: 'GET',
                data: {
                    driver_id: selectedDriverId,
                    driver_name: selectedDriverName,
                    start_date: $('#filterStartDate').val(),
                    end_date: $('#filterEndDate').val(),
                    status: $('#filterStatus').val(),
                    merchant_id: $('#filterMerchant').val()
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const stats = response.data.statistics;
                        $('#statTotalDeliveries').text(stats.total_deliveries || 0);
                        $('#statActiveDeliveries').text(stats.active_deliveries || 0);
                        $('#statCompletedDeliveries').text(stats.completed_deliveries || 0);
                        $('#statCancelledDeliveries').text(stats.cancelled_deliveries || 0);
                        $('#statItemsCarrying').text(stats.total_items_carrying || 0);
                        $('#statItemsDelivered').text(stats.total_items_delivered || 0);
                        $('#statTotalDeliveryPrice').text(Number(stats.total_delivery_price || 0).toLocaleString());
                    }
                },
                error: function() {
                    console.error('Error loading statistics');
                }
            });
        }

        function loadDriverItems() {
            $('#driverItemsList').html('<p class="text-center">Ачааллаж байна...</p>');
            
            $.ajax({
                url: '{{ route("admin.driver-monitoring.data") }}',
                method: 'GET',
                data: {
                    driver_id: selectedDriverId,
                    driver_name: selectedDriverName
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const items = response.data.current_items || [];
                        
                        if (items.length > 0) {
                            let html = '<table class="table table-bordered table-striped"><thead><tr><th>Барааны нэр</th><th>Тоо хэмжээ</th></tr></thead><tbody>';
                            items.forEach(function(item) {
                                html += '<tr><td>' + (item.name || 'Тодорхойгүй') + '</td><td>' + (item.quantity || 0) + '</td></tr>';
                            });
                            html += '</tbody></table>';
                            html += '<p class="mt-3"><strong>Нийт бараа:</strong> ' + items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0) + '</p>';
                            $('#driverItemsList').html(html);
                        } else {
                            $('#driverItemsList').html('<p class="text-center text-muted">Одоогоор тээвэрлэж буй бараа байхгүй байна</p>');
                        }
                    } else {
                        $('#driverItemsList').html('<p class="text-center text-muted">Барааны мэдээлэл авахад алдаа гарлаа</p>');
                    }
                },
                error: function() {
                    $('#driverItemsList').html('<p class="text-center text-danger">Алдаа гарлаа</p>');
                }
            });
        }

        function loadDeliveriesTable() {
            if (deliveriesTable) {
                deliveriesTable.destroy();
            }

            deliveriesTable = $('#deliveriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.driver-monitoring.deliveries") }}',
                    data: function(d) {
                        d.driver_id = selectedDriverId;
                        d.driver_name = selectedDriverName;
                        d.start_date = $('#filterStartDate').val();
                        d.end_date = $('#filterEndDate').val();
                        d.status = $('#filterStatus').val();
                        d.merchant_id = $('#filterMerchant').val();
                    }
                },
                columns: [
                    { data: 'delivery_id', name: 'id' },
                    { data: 'status_badge', name: 'status', orderable: false },
                    { data: 'merchant_name', name: 'merchant.merchantName' },
                    { data: 'customer_address', name: 'address' },
                    { data: 'assigned_date', name: 'created_at' },
                    { data: 'completed_date', name: 'updated_at' },
                    { data: 'item_count', name: 'item_count', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: {
                    processing: "Ачааллаж байна...",
                    search: "Хайх:",
                    lengthMenu: "_MENU_ мөр харуулах",
                    info: "_START_ - _END_ / _TOTAL_",
                    infoEmpty: "Мэдээлэл байхгүй",
                    infoFiltered: "(_MAX_ мөрөөс шүүсэн)",
                    paginate: {
                        first: "Эхний",
                        last: "Сүүлийн",
                        next: "Дараах",
                        previous: "Өмнөх"
                    }
                }
            });
        }

        // View items for a delivery
        $(document).on('click', '.view-items', function() {
            const deliveryId = $(this).data('delivery-id');
            loadDeliveryItems(deliveryId);
        });

        function loadDeliveryItems(deliveryId) {
            $('#itemsList').html('<p class="text-center">Ачааллаж байна...</p>');
            $('#itemsModal').modal('show');

            $.ajax({
                url: '{{ url("admin/driver-monitoring/items") }}/' + deliveryId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        
                        if (response.data.items && response.data.items.length > 0) {
                            html = '<table class="table table-bordered"><thead><tr><th>Барааны нэр</th><th>Тоо хэмжээ</th></tr></thead><tbody>';
                            response.data.items.forEach(function(item) {
                                html += '<tr><td>' + (item.name || 'Тодорхойгүй') + '</td><td>' + (item.quantity || 1) + '</td></tr>';
                            });
                            html += '</tbody></table>';
                            html += '<p class="mt-3"><strong>Нийт бараа:</strong> ' + response.data.total_items + '</p>';
                        } else {
                            // If no structured items, show parcel_info or goodtype if available
                            if (response.data.parcel_info) {
                                html = '<div class="alert alert-info"><strong>Барааны мэдээлэл:</strong><br>' + response.data.parcel_info + '</div>';
                            } else if (response.data.goodtype) {
                                html = '<div class="alert alert-info"><strong>Барааны төрөл:</strong><br>' + response.data.goodtype + '</div>';
                            } else {
                                html = '<p class="text-center text-muted">Барааны мэдээлэл байхгүй</p>';
                            }
                        }
                        
                        $('#itemsList').html(html);
                    } else {
                        $('#itemsList').html('<p class="text-center text-danger">Алдаа: ' + (response.message || 'Мэдээлэл авахад алдаа гарлаа') + '</p>');
                    }
                },
                error: function() {
                    $('#itemsList').html('<p class="text-center text-danger">Алдаа гарлаа</p>');
                }
            });
        }
    </script>
@endsection

