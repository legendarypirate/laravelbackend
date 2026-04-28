@extends('admin.master')

@section('mainContent')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Агуулах</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Агуулах</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Барааны жагсаалт</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="itemSearchInput" class="form-control float-right" placeholder="Барааны нэрээр хайх...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default" id="clearSearchBtn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-head-fixed text-nowrap" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Бараа</th>
                                    <th>Зураг</th>
                                    <th>Үлдэгдэл</th>
                                    <th>Хүргэлтэнд</th>
                                    <th>Хүргэгдсэн</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @foreach ($good as $goods)
                                    <tr data-item-name="{{ strtolower($goods->name) }}">
                                        <td>{{ $goods->id }}</td>
                                        <td>{{ $goods->name }}</td>
                                        <td><img src="{{ asset('storage/' . $goods->image) }}" width=50></td>
                                        <td>{{ $goods->quantity }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="in-delivery-link"
                                               data-item-id="{{ $goods->id }}"
                                               data-item-name="{{ $goods->name }}"
                                               style="color: #007bff; text-decoration: underline; cursor: pointer;">
                                                {{ $goods->in_delivery }}
                                            </a>
                                        </td>
                                        <td>{{ $goods->delivered }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Add Quantity Button -->
                                                <button type="button" class="btn btn-success btn-sm add-quantity-btn"
                                                        data-item-id="{{ $goods->id }}"
                                                        data-item-name="{{ $goods->name }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                
                                                <!-- Decrease Quantity Button -->
                                                <button type="button" class="btn btn-warning btn-sm decrease-quantity-btn"
                                                        data-item-id="{{ $goods->id }}"
                                                        data-item-name="{{ $goods->name }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                
                                                <!-- View History Button -->
                                                <button type="button" class="btn btn-info btn-sm view-history-btn"
                                                        data-item-id="{{ $goods->id }}"
                                                        data-item-name="{{ $goods->name }}">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                
                                                <!-- Delete Button -->
<!-- Delete Button - Only show for admin role -->
@if(auth()->user()->name == 'subuser')
    <button type="submit" class="btn btn-danger btn-sm">
        <a href="{{ url('/item/delete/' . $goods->id) }}"
           onclick="return confirm('Are you sure you want to delete this item?');"
           style="color:white; text-decoration:none;">
            <i class="fas fa-trash"></i>
        </a>
    </button>
@endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                            </div>
                            <div class="card-footer" id="searchResultsInfo" style="display: none;">
                                <small class="text-muted">
                                    <span id="visibleCount">0</span> / <span id="totalCount">{{ count($good) }}</span> бараа харуулж байна
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <!-- Floating Add Button -->
    <button class="btn btn-primary btn-lg floating-button" id="addItemBtn">
        + Бараа нэмэх
    </button>

    <!-- Drawer Overlay -->
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <!-- Drawer -->
    <div class="drawer" id="itemDrawer">
        <div class="drawer-header">
            <h4 class="mb-0">Шинэ бараа нэмэх</h4>
            <button type="button" class="btn-close" id="closeDrawerBtn">×</button>
        </div>
        <div class="drawer-body">
            <form id="addItemForm" action="{{ url('/item/create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="goodname" class="form-label">Барааны нэр</label>
                    <input type="text" class="form-control" id="name" name="goodname" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Зураг</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="count" class="form-label">Үлдэгдэл</label>
                    <input type="number" class="form-control" id="quantity" name="count" min="0" required>
                </div>
         
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Хадгалах</button>
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Цуцлах</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Driver Distribution Modal -->
    <div class="modal" id="driverDistributionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Жолооч дахь барааны хуваарилалт</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="driverDistributionContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Quantity Modal -->
    <div class="modal" id="addQuantityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Барааны тоо хэмжээ нэмэх</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addQuantityForm" action="{{ url('/item/add-quantity') }}" method="POST">
                        @csrf
                        <input type="hidden" id="add_item_id" name="item_id">
                        <div class="mb-3">
                            <label for="add_quantity" class="form-label">Нэмэх тоо хэмжээ</label>
                            <input type="number" class="form-control" id="add_quantity" name="quantity" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_driver" class="form-label">Жолооч</label>
                            <select class="form-control" id="add_driver" name="driver_id">
                                <option value="">Жолооч сонгох (заавал биш)</option>
                                @php
                                    $drivers = DB::table('users')
                                        ->where('role', 'driver')
                                        ->where('active', 1)
                                        ->get();
                                @endphp
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_reason" class="form-label">Шалтгаан</label>
                            <textarea class="form-control" id="add_reason" name="reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Цуцлах</button>
                    <button type="button" class="btn btn-primary" id="saveAddQuantityBtn">Хадгалах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decrease Quantity Modal -->
    <div class="modal" id="decreaseQuantityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Барааны тоо хэмжээ хасах</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="decreaseQuantityForm" action="{{ url('/item/decrease-quantity') }}" method="POST">
                        @csrf
                        <input type="hidden" id="decrease_item_id" name="item_id">
                        <div class="mb-3">
                            <label for="decrease_quantity" class="form-label">Хасах тоо хэмжээ</label>
                            <input type="number" class="form-control" id="decrease_quantity" name="quantity" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="decrease_driver" class="form-label">Жолооч</label>
                            <select class="form-control" id="decrease_driver" name="driver_id">
                                <option value="">Жолооч сонгох (заавал биш)</option>
                                @php
                                    $drivers = DB::table('users')
                                        ->where('role', 'driver')
                                        ->where('active', 1)
                                        ->get();
                                @endphp
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="decrease_reason" class="form-label">Шалтгаан</label>
                            <textarea class="form-control" id="decrease_reason" name="reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Цуцлах</button>
                    <button type="button" class="btn btn-primary" id="saveDecreaseQuantityBtn">Хадгалах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced History Modal -->
    <div class="modal" id="historyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Барааны түүх - <span id="historyItemName"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <!-- Filters Section -->
                    <div class="p-3 border-bottom bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Хайх</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="historySearch" class="form-control" placeholder="Шалтгаан, хэрэглэгч эсвэл тоо хэмжээгээр хайх...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Төрөл</label>
                                    <select id="historyTypeFilter" class="form-control form-control-sm">
                                        <option value="">Бүх төрөл</option>
                                        <option value="in">Орлого</option>
                                        <option value="out">Зарлага</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Жолооч</label>
                                    <select id="historyDriverFilter" class="form-control form-control-sm">
                                        <option value="">Бүх жолооч</option>
                                        @php
                                            $drivers = DB::table('users')
                                                ->where('role', 'driver')
                                                ->where('active', 1)
                                                ->get();
                                        @endphp
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Тоо хэмжээ</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="quantityMin" class="form-control" placeholder="Доод">
                                        <input type="number" id="quantityMax" class="form-control" placeholder="Дээд">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clearQuantityFilter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="small text-muted mb-1">Огноогоор шүүх</label>
                                    <div class="mb-1">
                                        <select id="historyDateSource" class="form-control form-control-sm">
                                            <option value="delivery_delivered_at" selected>Хүргэгдсэн огноо (delivery.delivered_at)</option>
                                            <option value="delivery_created_at">Үүсгэсэн огноо (delivery.created_at)</option>
                                            <option value="history_created_at">Түүхийн огноо (history.created_at)</option>
                                        </select>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <input type="date" id="historyDateFrom" class="form-control" placeholder="Эхлэх огноо">
                                        <input type="date" id="historyDateTo" class="form-control" placeholder="Дуусах огноо">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clearDateFilter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group btn-group-sm mt-3">
                                    <button type="button" class="btn btn-outline-secondary" id="clearAllFilters">
                                        <i class="fas fa-broom"></i> Цэвэрлэх
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="exportHistory">
                                        <i class="fas fa-download"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Section -->
                    <div id="historyStats" class="p-3 border-bottom bg-white" style="display: none;">
                        <div class="row text-center">
                            <div class="col-3">
                                <small class="text-muted d-block">Нийт үйлдэл</small>
                                <span class="h6 mb-0 text-primary" id="totalTransactions">0</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Нийт орлого</small>
                                <span class="h6 mb-0 text-success" id="totalIncome">+0</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Нийт зарлага</small>
                                <span class="h6 mb-0 text-danger" id="totalOutcome">-0</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Цэвэр өөрчлөлт</small>
                                <span class="h6 mb-0 text-info" id="netBalance">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="table-container" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th style="width: 150px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                                        <span>Огноо</span>
                                        <button class="btn btn-sm btn-link p-0 ml-1 sort-btn" data-sort="date">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </th>
                                    <th style="width: 100px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">Төрөл</th>
                                    <th style="width: 100px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                                        <span>Тоо хэмжээ</span>
                                        <button class="btn btn-sm btn-link p-0 ml-1 sort-btn" data-sort="quantity">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </th>
                                    <th style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">Шалтгаан</th>
                                    <th style="width: 120px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">Жолооч</th>
                                    <th style="width: 120px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">Хэрэглэгч</th>
                                    <th style="width: 140px; position: sticky; top: 0; background: #f8f9fa; z-index: 10;">Хүргэлт</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading State -->
                    <div id="historyLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Түүх ачаалж байна...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="historyEmpty" class="text-center py-5" style="display: none;">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Энэ барааны түүх олдсонгүй</p>
                    </div>

                    <!-- No Results State -->
                    <div id="historyNoResults" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Илэрц олдсонгүй. Шүүлтүүрээ өөрчилнө үү.</p>
                    </div>

                    <!-- Pagination -->
                    <div id="historyPagination" class="p-3 border-top bg-light" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted" id="paginationInfo"></small>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="History pagination">
                                    <ul class="pagination pagination-sm justify-content-end mb-0" id="paginationControls">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .floating-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .drawer {
            position: fixed;
            top: 0;
            right: -450px;
            width: 450px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .drawer.open {
            right: 0;
        }
        
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        
        .drawer-overlay.show {
            display: block;
        }
        
        .drawer-header {
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .drawer-body {
            padding: 20px;
        }
        
        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1060;
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 10% auto;
            max-width: 500px;
        }
        
        .modal-xl {
            max-width: 95%;
        }
        
        .modal-lg {
            max-width: 800px;
        }
        
        .modal-content {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
        }
        
        .close:hover {
            opacity: 1;
        }
        
        .btn-group .btn {
            margin-right: 2px;
        }
        
        /* Enhanced Table Styles */
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
        }
        
        .table-container thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-container tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.075);
        }
        
        .comment-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: help;
        }
        
        /* Sort button styles */
        .sort-btn {
            font-size: 0.8em;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .modal-xl {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            
            .table-container {
                max-height: 300px;
            }
            
            .comment-text {
                max-width: 150px;
            }
        }
        
        @media (max-width: 576px) {
            .drawer {
                width: 100%;
                right: -100%;
            }
            
            .modal-dialog {
                margin: 5% auto;
                width: 95%;
            }
        }
    </style>

    <script>
    // Item Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('itemSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const itemsTable = document.getElementById('itemsTable');
        const itemsTableBody = document.getElementById('itemsTableBody');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const visibleCountSpan = document.getElementById('visibleCount');
        const totalCountSpan = document.getElementById('totalCount');
        
        const totalItems = {{ count($good) }};
        
        // Search function
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const rows = itemsTableBody.querySelectorAll('tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const itemName = row.getAttribute('data-item-name') || '';
                if (itemName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update results info
            if (searchTerm) {
                visibleCountSpan.textContent = visibleCount;
                totalCountSpan.textContent = totalItems;
                searchResultsInfo.style.display = 'block';
            } else {
                searchResultsInfo.style.display = 'none';
            }
        }
        
        // Search input event listener
        searchInput.addEventListener('input', function() {
            performSearch();
        });
        
        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            performSearch();
            searchInput.focus();
        });
        
        // Allow Enter key to focus search
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search (common shortcut)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    });
    
    // Enhanced History Functions
    let currentHistoryData = [];
    let currentFilteredHistoryData = [];


    function getHistoryFilterDate(item) {
        const source = document.getElementById('historyDateSource')?.value || 'delivery_delivered_at';
        if (source === 'delivery_created_at') {
            return item.delivery_created_at || item.history_created_at || item.created_at || null;
        }
        if (source === 'history_created_at') {
            return item.history_created_at || item.created_at || item.delivery_created_at || null;
        }
        // default: delivery_delivered_at
        return item.delivery_delivered_at || item.delivery_created_at || item.history_created_at || item.created_at || null;
    }
    let currentSort = { field: 'created_at', direction: 'desc' };
    let currentPage = 1;
    const itemsPerPage = 20;

    document.addEventListener('DOMContentLoaded', function() {
        // Drawer functionality
        const addItemBtn = document.getElementById('addItemBtn');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const drawerOverlay = document.getElementById('drawerOverlay');
        const itemDrawer = document.getElementById('itemDrawer');
        
        // Open drawer
        addItemBtn.addEventListener('click', function() {
            itemDrawer.classList.add('open');
            drawerOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
        
        // Close drawer
        function closeDrawer() {
            itemDrawer.classList.remove('open');
            drawerOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        closeDrawerBtn.addEventListener('click', closeDrawer);
        cancelBtn.addEventListener('click', closeDrawer);
        drawerOverlay.addEventListener('click', closeDrawer);
        
        // Modal functionality
        const addQuantityModal = document.getElementById('addQuantityModal');
        const decreaseQuantityModal = document.getElementById('decreaseQuantityModal');
        const historyModal = document.getElementById('historyModal');
        const driverDistributionModal = document.getElementById('driverDistributionModal');
        
        // Add click handler for in_delivery links
        document.querySelectorAll('.in-delivery-link').forEach(link => {
            link.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const itemName = this.getAttribute('data-item-name');
                
                console.log('Opening driver distribution for item:', itemId, itemName);
                
                document.querySelector('#driverDistributionModal .modal-title').textContent =
                    `Жолооч дахь бараа - ${itemName}`;
                
                loadDriverDistribution(itemId);
                showModal(driverDistributionModal);
            });
        });
        
        // Add Quantity Button Handlers
        document.querySelectorAll('.add-quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const itemName = this.getAttribute('data-item-name');
                
                console.log('Add Quantity - Item ID:', itemId, 'Item Name:', itemName);
                
                document.getElementById('add_item_id').value = itemId;
                document.querySelector('#addQuantityModal .modal-title').textContent =
                    `Барааны тоо хэмжээ нэмэх - ${itemName}`;
                
                showModal(addQuantityModal);
            });
        });
        
        // Decrease Quantity Button Handlers
        document.querySelectorAll('.decrease-quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const itemName = this.getAttribute('data-item-name');
                
                console.log('Decrease Quantity - Item ID:', itemId, 'Item Name:', itemName);
                
                document.getElementById('decrease_item_id').value = itemId;
                document.querySelector('#decreaseQuantityModal .modal-title').textContent =
                    `Барааны тоо хэмжээ хасах - ${itemName}`;
                
                showModal(decreaseQuantityModal);
            });
        });
        
        // History Button Handlers
        document.querySelectorAll('.view-history-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const itemName = this.getAttribute('data-item-name');
                
                console.log('View History - Item ID:', itemId, 'Item Name:', itemName);
                
                document.getElementById('historyItemName').textContent = itemName;
                loadItemHistory(itemId);
                showModal(historyModal);
            });
        });
        
        // Modal close handlers
        document.querySelectorAll('.modal .close, .modal [data-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                hideModal(modal);
            });
        });
        
        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this);
                }
            });
        });
        
        // Save button handlers with validation
        document.getElementById('saveAddQuantityBtn').addEventListener('click', function() {
            const form = document.getElementById('addQuantityForm');
            const quantity = document.getElementById('add_quantity').value;
            
            if (!quantity || quantity < 1) {
                alert('Тоо хэмжээ оруулна уу');
                return;
            }
            
            console.log('Submitting Add Quantity Form');
            form.submit();
        });
        
        document.getElementById('saveDecreaseQuantityBtn').addEventListener('click', function() {
            const form = document.getElementById('decreaseQuantityForm');
            const quantity = document.getElementById('decrease_quantity').value;
            const reason = document.getElementById('decrease_reason').value;
            
            if (!quantity || quantity < 1) {
                alert('Тоо хэмжээ оруулна уу');
                return;
            }
            
            if (!reason.trim()) {
                alert('Шалтгаан оруулна уу');
                return;
            }
            
            console.log('Submitting Decrease Quantity Form');
            form.submit();
        });
        
        // Modal utility functions
        function showModal(modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function hideModal(modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Close drawer on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDrawer();
                document.querySelectorAll('.modal.show').forEach(modal => {
                    hideModal(modal);
                });
            }
        });
    });

    // Function to load item history from API
    function loadItemHistory(itemId) {
        const loadingElement = document.getElementById('historyLoading');
        const emptyElement = document.getElementById('historyEmpty');
        const noResultsElement = document.getElementById('historyNoResults');
        const tableBody = document.getElementById('historyTableBody');
        const statsElement = document.getElementById('historyStats');
        const paginationElement = document.getElementById('historyPagination');

        // Show loading, hide others
        loadingElement.style.display = 'block';
        emptyElement.style.display = 'none';
        noResultsElement.style.display = 'none';
        tableBody.innerHTML = '';
        statsElement.style.display = 'none';
        paginationElement.style.display = 'none';

        fetch(`/item/history/${itemId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                loadingElement.style.display = 'none';
                
                if (data.success && data.data.length > 0) {
                    currentHistoryData = data.data;
                    currentPage = 1;
                    applyFiltersAndRender();
                    initializeFilters();
                } else {
                    emptyElement.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading history:', error);
                loadingElement.style.display = 'none';
                emptyElement.style.display = 'block';
                emptyElement.innerHTML = `
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                    <p class="text-danger">Түүх ачаалахад алдаа гарлаа</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadItemHistory(${itemId})">
                        Дахин оролдох
                    </button>
                `;
            });
    }

    // Apply filters and render table
    function applyFiltersAndRender() {
        let filteredData = [...currentHistoryData];

        // Apply search filter (includes delivery id/address)
        const searchTerm = document.getElementById('historySearch').value.toLowerCase();
        if (searchTerm) {
            filteredData = filteredData.filter(item =>
                (item.comment && item.comment.toLowerCase().includes(searchTerm)) ||
                (item.operation_by && item.operation_by.toLowerCase().includes(searchTerm)) ||
                (item.driver_name && item.driver_name.toLowerCase().includes(searchTerm)) ||
                (item.quantity && item.quantity.toString().includes(searchTerm)) ||
                (item.delivery_display_id != null && item.delivery_display_id.toString().includes(searchTerm)) ||
                (item.delivery_address && item.delivery_address.toLowerCase().includes(searchTerm))
            );
        }

        // Apply type filter
        const typeFilter = document.getElementById('historyTypeFilter').value;
        if (typeFilter) {
            filteredData = filteredData.filter(item => item.type === typeFilter);
        }

        // Apply driver filter
        const driverFilter = document.getElementById('historyDriverFilter').value;
        if (driverFilter) {
            filteredData = filteredData.filter(item => item.driver_id == driverFilter);
        }

        // Apply quantity range filter
        const quantityMin = document.getElementById('quantityMin').value;
        const quantityMax = document.getElementById('quantityMax').value;
        if (quantityMin) {
            filteredData = filteredData.filter(item => Math.abs(item.quantity) >= parseInt(quantityMin));
        }
        if (quantityMax) {
            filteredData = filteredData.filter(item => Math.abs(item.quantity) <= parseInt(quantityMax));
        }

        // Apply date filter
        const dateFrom = document.getElementById('historyDateFrom').value;
        const dateTo = document.getElementById('historyDateTo').value;
        if (dateFrom) {
            filteredData = filteredData.filter(item => {
                const dateValue = getHistoryFilterDate(item);
                return dateValue ? new Date(dateValue) >= new Date(dateFrom) : false;
            });
        }
        if (dateTo) {
            const endDate = new Date(dateTo);
            endDate.setHours(23, 59, 59, 999);
            filteredData = filteredData.filter(item => {
                const dateValue = getHistoryFilterDate(item);
                return dateValue ? new Date(dateValue) <= endDate : false;
            });
        }

        // Apply sorting
        filteredData.sort((a, b) => {
            let aValue = a[currentSort.field];
            let bValue = b[currentSort.field];

            if (currentSort.field === 'created_at') {
                aValue = new Date(getHistoryFilterDate(a));
                bValue = new Date(getHistoryFilterDate(b));
            }

            if (currentSort.direction === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        currentFilteredHistoryData = filteredData;
        renderTable(filteredData);
        updateStatistics(filteredData);
        renderPagination(filteredData.length);
    }

    // Render table with data
    function renderTable(data) {
        const tableBody = document.getElementById('historyTableBody');
        const emptyElement = document.getElementById('historyEmpty');
        const noResultsElement = document.getElementById('historyNoResults');
        const loadingElement = document.getElementById('historyLoading');

        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedData = data.slice(startIndex, startIndex + itemsPerPage);

        // Hide all states first
        loadingElement.style.display = 'none';
        emptyElement.style.display = 'none';
        noResultsElement.style.display = 'none';

        if (data.length === 0 && currentHistoryData.length === 0) {
            emptyElement.style.display = 'block';
            return;
        }

        if (data.length === 0 && currentHistoryData.length > 0) {
            noResultsElement.style.display = 'block';
            return;
        }

        if (paginatedData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>Илэрц олдсонгүй</p>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = paginatedData.map(record => {
            const quantityClass = record.type === 'in' ? 'text-success' : 'text-danger';
            const quantitySign = record.type === 'in' ? '+' : '-';
            const typeBadge = record.type === 'in' ?
                '<span class="badge badge-success">Орлого</span>' :
                '<span class="badge badge-danger">Зарлага</span>';
            
            const recordDateValue = getHistoryFilterDate(record);
            const date = recordDateValue ? new Date(recordDateValue).toLocaleString('mn-MN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : '-';
            
            const driverDisplay = record.driver_name || (record.driver_id ? `Жолооч #${record.driver_id}` : '-');
            const deliveryDisplay = (record.delivery_display_id != null)
                ? (record.delivery_address ? `#${record.delivery_display_id} - ${record.delivery_address}` : `#${record.delivery_display_id}`)
                : '-';
            
            return `
                <tr>
                    <td class="small">${date}</td>
                    <td>${typeBadge}</td>
                    <td class="${quantityClass} font-weight-bold">${quantitySign}${Math.abs(record.quantity)}</td>
                    <td>
                        <div class="comment-text" title="${record.comment || '-'}">
                            ${record.comment || '-'}
                        </div>
                    </td>
                    <td class="small">${driverDisplay}</td>
                    <td class="small">${record.operation_by || record.user_id || 'Систем'}</td>
                    <td class="small">${deliveryDisplay}</td>
                </tr>
            `;
        }).join('');
    }

    // Update statistics
    function updateStatistics(data) {
        const statsElement = document.getElementById('historyStats');
        const totalIn = data.filter(item => item.type === 'in').reduce((sum, item) => sum + item.quantity, 0);
        const totalOut = data.filter(item => item.type === 'out').reduce((sum, item) => sum + item.quantity, 0);
        
        document.getElementById('totalTransactions').textContent = data.length;
        document.getElementById('totalIncome').textContent = `+${totalIn}`;
        document.getElementById('totalOutcome').textContent = `-${Math.abs(totalOut)}`;
        document.getElementById('netBalance').textContent = totalIn + totalOut; // totalOut is negative
        
        statsElement.style.display = 'block';
    }

    // Render pagination
    function renderPagination(totalItems) {
        const paginationElement = document.getElementById('historyPagination');
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        if (totalPages <= 1) {
            paginationElement.style.display = 'none';
            return;
        }
        
        document.getElementById('paginationInfo').textContent =
            `Нийт ${totalItems} бичлэг, ${currentPage} - ${totalPages} хуудас`;
        
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Өмнөх</a>
            </li>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Next button
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Дараах</a>
            </li>
        `;
        
        document.getElementById('paginationControls').innerHTML = paginationHTML;
        paginationElement.style.display = 'block';
    }

    // Change page
    function changePage(page) {
        currentPage = page;
        applyFiltersAndRender();
        // Scroll to top of table
        document.querySelector('.table-container').scrollTop = 0;
    }

    // Initialize filters
    function initializeFilters() {
        // Search with debounce
        let searchTimeout;
        document.getElementById('historySearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                applyFiltersAndRender();
            }, 300);
        });
        
        // Other filters
        const filterElements = [
            'historyTypeFilter',
            'historyDriverFilter',
            'quantityMin',
            'quantityMax',
            'historyDateSource',
            'historyDateFrom',
            'historyDateTo'
        ];
        
        filterElements.forEach(elementId => {
            document.getElementById(elementId).addEventListener('change', function() {
                currentPage = 1;
                applyFiltersAndRender();
            });
        });
        
        // Clear buttons
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('historySearch').value = '';
            currentPage = 1;
            applyFiltersAndRender();
        });
        
        document.getElementById('clearQuantityFilter').addEventListener('click', function() {
            document.getElementById('quantityMin').value = '';
            document.getElementById('quantityMax').value = '';
            currentPage = 1;
            applyFiltersAndRender();
        });
        
        document.getElementById('clearDateFilter').addEventListener('click', function() {
            document.getElementById('historyDateFrom').value = '';
            document.getElementById('historyDateTo').value = '';
            currentPage = 1;
            applyFiltersAndRender();
        });
        
        document.getElementById('clearAllFilters').addEventListener('click', function() {
            document.getElementById('historySearch').value = '';
            document.getElementById('historyTypeFilter').value = '';
            document.getElementById('historyDriverFilter').value = '';
            document.getElementById('quantityMin').value = '';
            document.getElementById('quantityMax').value = '';
            document.getElementById('historyDateFrom').value = '';
            document.getElementById('historyDateTo').value = '';
            currentPage = 1;
            currentSort = { field: 'created_at', direction: 'desc' };
            applyFiltersAndRender();
        });
        
        // Sort buttons
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const field = this.getAttribute('data-sort') === 'date' ? 'created_at' : 'quantity';
                if (currentSort.field === field) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.field = field;
                    currentSort.direction = 'desc';
                }
                applyFiltersAndRender();
            });
        });
        
        // Export button - export filtered history to CSV (includes delivery info)
        document.getElementById('exportHistory').addEventListener('click', function() {
            const data = currentFilteredHistoryData || [];
            if (data.length === 0) {
                alert('Экспортлох өгөгдөл байхгүй байна.');
                return;
            }
            const headers = ['Огноо', 'Төрөл', 'Тоо хэмжээ', 'Шалтгаан', 'Жолооч', 'Хэрэглэгч', 'Хүргэлт ID', 'Хаяг', 'Утас', 'Тоо ширхэг', 'Дэлгүүр'];
            const escapeCsv = (v) => (v == null ? '' : String(v).replace(/"/g, '""'));
            const quote = (v) => '"' + escapeCsv(v) + '"';
            const rows = data.map(record => {
                const typeLabel = record.type === 'in' ? 'Орлого' : 'Зарлага';
                const quantitySign = record.type === 'in' ? '+' : '-';
                const qty = quantitySign + Math.abs(record.quantity);
                const driverDisplay = record.driver_name || (record.driver_id ? 'Жолооч #' + record.driver_id : '');
                const opBy = record.operation_by || record.user_id || 'Систем';
                const deliveryId = (record.delivery_display_id != null) ? String(record.delivery_display_id) : '';
                const date = record.created_at ? new Date(record.created_at).toLocaleString('mn-MN') : '';
                return [
                    quote(date),
                    quote(typeLabel),
                    quote(qty),
                    quote(record.comment || ''),
                    quote(driverDisplay),
                    quote(opBy),
                    quote(deliveryId),
                    quote(record.delivery_address),
                    quote(record.delivery_phone),
                    quote(record.delivery_number),
                    quote(record.delivery_shop)
                ];
            });
            const headerRow = headers.map(h => '"' + String(h).replace(/"/g, '""') + '"').join(',');
            const csvContent = '\uFEFF' + [headerRow, ...rows.map(r => r.join(','))].join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'baraa_tuuh_' + (document.getElementById('historyItemName') && document.getElementById('historyItemName').textContent ? document.getElementById('historyItemName').textContent.replace(/[^a-zA-Z0-9_\-\s]/g, '') : 'export') + '_' + new Date().toISOString().slice(0,10) + '.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        });
    }

    // Function to load driver distribution
    function loadDriverDistribution(itemId) {
        const content = document.getElementById('driverDistributionContent');
        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';

        fetch(`/item/drivers/${itemId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayDriverDistribution(data.data, data.total_in_delivery);
                } else {
                    content.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading driver distribution:', error);
                content.innerHTML = '<div class="alert alert-danger">Мэдээлэл ачаалахад алдаа гарлаа.</div>';
            });
    }

    // Function to display driver distribution
    function displayDriverDistribution(data, totalInDelivery) {
        const content = document.getElementById('driverDistributionContent');
        
        if (!data || data.length === 0) {
            content.innerHTML = '<div class="alert alert-info">Энэ бараа жолооч дээр байхгүй байна.</div>';
            return;
        }
        
        let html = `
            <div class="mb-3">
                <h6>Нийт жолооч дээр: <span class="badge badge-primary">${totalInDelivery}</span></h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Жолооч</th>
                            <th>Тоо хэмжээ</th>
                            <th>Эзлэх хувь</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.forEach(item => {
            const percentage = ((item.quantity / totalInDelivery) * 100).toFixed(1);
            
            html += `
                <tr>
                    <td>${item.driver_name}</td>
                    <td><strong>${item.quantity}</strong></td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: ${percentage}%;" 
                                 aria-valuenow="${percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${percentage}%
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }

    // Function to reset modals when closed
    function resetModalForms() {
        // Reset add quantity form
        document.getElementById('add_quantity').value = '';
        document.getElementById('add_driver').selectedIndex = 0;
        document.getElementById('add_reason').value = '';
        
        // Reset decrease quantity form
        document.getElementById('decrease_quantity').value = '';
        document.getElementById('decrease_driver').selectedIndex = 0;
        document.getElementById('decrease_reason').value = '';
    }

    // Add reset functionality when modals are closed
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                resetModalForms();
            });
        });

        // Reset history modal when closed
        document.getElementById('historyModal').addEventListener('hidden.bs.modal', function() {
            currentHistoryData = [];
            currentPage = 1;
            currentSort = { field: 'created_at', direction: 'desc' };
            
            // Reset filters
            document.getElementById('historySearch').value = '';
            document.getElementById('historyTypeFilter').value = '';
            document.getElementById('historyDriverFilter').value = '';
            document.getElementById('quantityMin').value = '';
            document.getElementById('quantityMax').value = '';
            document.getElementById('historyDateFrom').value = '';
            document.getElementById('historyDateTo').value = '';
        });
    });
    </script>
@endsection
