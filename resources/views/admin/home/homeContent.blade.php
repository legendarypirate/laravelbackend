@extends('admin.master')

@section('mainContent')
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Захиалгын хяналтын самбар</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ url('delivery/index') }}" class="btn btn-warning">
                                    <i class="fa fa-plus"></i> Шинэ хүргэлт үүсгэх
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content - Dashboard -->
        <section class="content">
            <div class="container-fluid">
                <!-- Date Filter Card -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-filter"></i> Шүүлт
                                </h3>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ url()->current() }}" class="form-inline">
                                    @if(Auth::user()->role != 'customer')
                                    <div class="form-group mr-3 mb-2">
                                        <label for="merchant_id" class="mr-2">Харилцагч:</label>
                                        <select class="form-control" 
                                                id="merchant_id" 
                                                name="merchant_id">
                                            <option value="">Бүх харилцагч</option>
                                            @if(isset($merchants) && $merchants->count() > 0)
                                                @foreach($merchants as $merchant)
                                                    <option value="{{ $merchant->name }}" 
                                                            {{ request('merchant_id') == $merchant->name ? 'selected' : '' }}>
                                                        {{ $merchant->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    @endif
                                    <div class="form-group mr-3 mb-2">
                                        <label for="start_date" class="mr-2">Эхлэх огноо:</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ $dateRange['start_date'] }}"
                                               max="{{ date('Y-m-d') }}"
                                               required>
                                    </div>
                                    <div class="form-group mr-3 mb-2">
                                        <label for="end_date" class="mr-2">Дуусах огноо:</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ $dateRange['end_date'] }}"
                                               max="{{ date('Y-m-d') }}"
                                               required>
                                    </div>
                                    <button type="submit" class="btn btn-primary mb-2">
                                        <i class="fas fa-search"></i> Шүүх
                                    </button>
                                    <a href="{{ route('home') }}" class="btn btn-secondary mb-2 ml-2">
                                        <i class="fas fa-redo"></i> Арилгах
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards Row 1 -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ number_format($delivery ?? 0) }}</h3>
                                <p>Нийт хүргэлт</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ number_format($new ?? 0) }}</h3>
                                <p>Шинэ захиалга</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="{{ url('delivery/new') }}" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($active ?? 0) }}</h3>
                                <p>Хүргэлтэнд гарсан</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <a href="{{ url('delivery/list') }}" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($success ?? 0) }}</h3>
                                <p>Амжилттай хүргэгдсэн</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <a href="{{ url('delivery/done') }}" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards Row 2 -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($declined ?? 0) }}</h3>
                                <p>Цуцлагдсан / Буцаасан</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3>{{ number_format($urgent ?? 0) }}</h3>
                                <p>Барааны тоо</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3>{{ number_format($totalDeliveryPrice ?? 0, 0) }}₮</h3>
                                <p>Нийт хүргэлтийн үнэ</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3>{{ number_format($totalPrice ?? 0, 0) }}₮</h3>
                                <p>барааны тооцоо</p>
                                @if(Auth::user()->role == 'admin')
                                <button type="button" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#topPhonesByPriceModal">
                                    <i class="fas fa-trophy"></i> TOP
                                </button>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Дэлгэрэнгүй <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Delivery Trends Chart -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header border-transparent">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Хүргэлтийн статистик
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="deliveryTrendChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Status Distribution Chart -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header border-transparent">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    Төлөвийн хуваарилалт
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="statusDistributionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Statistics Row -->
                <div class="row">
                    <div class="col-lg-4 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Хүргэлтийн дундаж хугацаа</span>
                                <span class="info-box-number">10.5 цаг</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Нийт харилцагчийн тоо</span>
                                <span class="info-box-number">{{ number_format($customer ?? 0) }}</span>
                                @if(Auth::user()->role == 'admin')
                                <button type="button" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#topPhonesModal">
                                    <i class="fas fa-trophy"></i> TOP
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1">
                                <i class="fas fa-star"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Жолоочийн дундаж үнэлгээ</span>
                                <span class="info-box-number">{{ number_format($driver ?? 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 10 Successful Deliveries Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-trophy mr-1"></i>
                                    Сүүлийн 100 захиалга (Шинэ ба Хүргэлтэнд гарсан)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                @if($deliveries && $deliveries->count() > 0)
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                @if(Auth::user()->role != 'customer')
                                                <th>Харилцагч</th>
                                                @endif
                                                <th>Захиалгын дугаар</th>
                                                <th>Огноо</th>
                                                <th>Хугацаа</th>
                                                <th>Хаяг</th>
                                                <th>Утас</th>
                                                <th>Төлөв</th>
                                                <th>Жолооч</th>
                                                <th>Барааны мэдээлэл</th>
                                                <th>Тоо ширхэг</th>
                                                <th>Тайлбар</th>
                                                <th>Үйлдэл</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveries as $delivery)
                                                @php
                                                    // Determine status display
                                                    $statusText = '';
                                                    $statusClass = '';
                                                    $statusIcon = '';
                                                    
                                                    if($delivery->status == 1 && $delivery->estimated == 1) {
                                                        $statusText = 'Захиалга баталгаажсан';
                                                        $statusClass = 'badge-info';
                                                        $statusIcon = 'fa-check-circle';
                                                    } elseif($delivery->status == 1) {
                                                        $statusText = 'Шинэ';
                                                        $statusClass = 'badge-primary';
                                                        $statusIcon = 'fa-star';
                                                    } elseif($delivery->status == 2) {
                                                        $statusText = 'Хүргэлтэнд гарсан';
                                                        $statusClass = 'badge-warning';
                                                        $statusIcon = 'fa-truck';
                                                    } elseif($delivery->status == 3) {
                                                        $statusText = 'Амжилттай хүргэгдсэн';
                                                        $statusClass = 'badge-success';
                                                        $statusIcon = 'fa-check';
                                                    } elseif($delivery->status == 4 || $delivery->status == 5) {
                                                        $statusText = 'Цуцлагдсан / Буцаасан';
                                                        $statusClass = 'badge-danger';
                                                        $statusIcon = 'fa-times-circle';
                                                    }
                                                    
                                                    $orderDate = \Carbon\Carbon::parse($delivery->created_at);
                                                    $orderDateFormatted = $orderDate->format('Y-m-d H:i');
                                                    
                                                    $hasLocation = !empty($delivery->latitude) && !empty($delivery->longitude);
                                                @endphp
                                                <tr>
                                                    @if(Auth::user()->role != 'customer')
                                                    <td>{{ $delivery->shop ?? 'N/A' }}</td>
                                                    @endif
                                                    <td>
                                                        <strong>{{ $delivery->track ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>{{ $orderDateFormatted }}</td>
                                                    <td class="time-indicator-cell" 
                                                        data-status="{{ $delivery->status }}" 
                                                        data-order-date="{{ $orderDate->toIso8601String() }}">
                                                        <div class="countdown-container">
                                                            <div class="countdown-timer" data-order-date="{{ $orderDate->toIso8601String() }}">
                                                                <i class="fas fa-clock text-info"></i> 
                                                                <span class="timer-text">Тооцоолж байна...</span>
                                                            </div>
                                                            <div class="progress mt-2" style="height: 6px;">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                                     role="progressbar" 
                                                                     style="width: 100%"
                                                                     aria-valuenow="100" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ Str::limit($delivery->address ?? 'N/A', 30) }}
                                                        @if($hasLocation)
                                                            <button type="button" 
                                                                    class="btn btn-xs btn-primary ml-1"
                                                                    onclick="showMapModal({{ $delivery->latitude }}, {{ $delivery->longitude }}, '{{ addslashes($delivery->address ?? 'N/A') }}')"
                                                                    title="Газрын зураг дээр харах">
                                                                <i class="fas fa-map"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td>{{ $delivery->phone ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge {{ $statusClass }}">
                                                            <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $delivery->driver ?? '-' }}</td>
                                                    <td>{{ Str::limit($delivery->parcel_info ?? '-', 60) }}</td>
                                                    <td>{{ $delivery->number ?? '-' }}</td>
                                                    <td>{{ Str::limit($delivery->comment ?? '-', 50) }}</td>
                                                    <td>
                                                        <a href="{{ url('/delivery/detail/' . $delivery->id) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-info-circle"></i> Дэлгэрэнгүй
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">Одоогоор захиалга байхгүй байна</h4>
                                        <p class="text-muted">Сонгосон огнооны хүрээнд захиалга олдсонгүй.</p>
                                    </div>
                                @endif
                            </div>
                            @if($deliveries && $deliveries->count() >= 100)
                                <div class="card-footer">
                                    <a href="{{ url('delivery/index') }}" class="btn btn-primary btn-sm">
                                        Бүх хүргэлтийг харах <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Map Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">Газрын зураг</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="mapContainer" style="height: 500px; width: 100%;"></div>
                    <div id="mapAddress" class="mt-2">
                        <strong>Хаяг:</strong> <span id="addressText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Phones Modal -->
    <div class="modal fade" id="topPhonesModal" tabindex="-1" role="dialog" aria-labelledby="topPhonesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topPhonesModalLabel">
                        <i class="fas fa-trophy"></i> Топ 10 хамгийн их давтагдсан утасны дугаар - Амжилттай хүргэгдсэн
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="topPhonesLoading" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Уншиж байна...</p>
                    </div>
                    <div id="topPhonesContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Дэлгүүр</th>
                                        <th>Утасны дугаар</th>
                                        <th>Хүлээн авагчийн нэр</th>
                                        <th>Хүргэлтийн тоо</th>
                                    </tr>
                                </thead>
                                <tbody id="topPhonesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="topPhonesError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <span id="topPhonesErrorMessage">Алдаа гарлаа</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Phones By Price Modal -->
    <div class="modal fade" id="topPhonesByPriceModal" tabindex="-1" role="dialog" aria-labelledby="topPhonesByPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topPhonesByPriceModalLabel">
                        <i class="fas fa-trophy"></i> Топ 10 утасны дугаар - Барааны тооцоогоор
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="topPhonesByPriceLoading" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Уншиж байна...</p>
                    </div>
                    <div id="topPhonesByPriceContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Дэлгүүр</th>
                                        <th>Утасны дугаар</th>
                                        <th>Хүлээн авагчийн нэр</th>
                                        <th>Нийт үнэ</th>
                                        <th>Хүргэлтийн тоо</th>
                                    </tr>
                                </thead>
                                <tbody id="topPhonesByPriceTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="topPhonesByPriceError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <span id="topPhonesByPriceErrorMessage">Алдаа гарлаа</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Хаах</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Maps API -->
    @php
        $googleMapsKey = env('GOOGLE_MAPS_API_KEY', '');
    @endphp
    @if($googleMapsKey)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initMapScript" async defer></script>
    @endif

    <style>
        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            display: block;
            margin-bottom: 20px;
            position: relative;
        }
        .small-box:hover {
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 4px 8px rgba(0,0,0,.3);
            transition: all 0.3s ease;
        }
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: 0.25rem;
            background-color: #fff;
            display: flex;
            margin-bottom: 1rem;
            min-height: 80px;
            padding: 0.5rem;
            position: relative;
        }
        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 4px 8px rgba(0,0,0,.3);
            transition: all 0.3s ease;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .card:hover {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 4px 8px rgba(0,0,0,.3);
            transition: all 0.3s ease;
        }
        .countdown-container {
            min-width: 150px;
        }
        .countdown-timer {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }
        .timer-text {
            font-family: 'Courier New', monospace;
            font-size: 0.95em;
            white-space: nowrap;
            font-weight: 600;
        }
        .time-indicator-cell {
            min-width: 150px;
        }
        .progress {
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar {
            transition: width 1s linear, background-color 0.3s ease;
        }
    </style>

    <script>
        let map;
        let currentMarker;
        let mapInitialized = false;
        let deliveryTrendChart;
        let statusDistributionChart;

        // Initialize map script
        function initMapScript() {
            mapInitialized = true;
        }

        // Date validation
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const today = new Date().toISOString().split('T')[0];
            
            if (startDate) startDate.max = today;
            if (endDate) endDate.max = today;
            
            if (startDate) {
                startDate.addEventListener('change', function() {
                    if (this.value && endDate) {
                        endDate.min = this.value;
                    }
                });
            }
            
            if (endDate) {
                endDate.addEventListener('change', function() {
                    if (this.value && startDate) {
                        startDate.max = this.value;
                    }
                });
            }

            // Initialize charts
            initDeliveryTrendChart();
            initStatusDistributionChart();
            
            // Initialize time indicators for deliveries
            initTimeIndicators();
            
            // Initialize top phones modal handlers
            initTopPhonesModal();
            
            // Initialize top phones by price modal handlers
            initTopPhonesByPriceModal();
        });
        
        // Initialize time indicators with countdown timer
        function initTimeIndicators() {
            const timers = document.querySelectorAll('.countdown-timer');
            
            timers.forEach(function(timer) {
                const orderDateStr = timer.getAttribute('data-order-date');
                if (!orderDateStr) return;
                
                const timerText = timer.querySelector('.timer-text');
                if (!timerText) return;
                
                // Find the progress bar in the same container
                const container = timer.closest('.countdown-container');
                const progressBar = container ? container.querySelector('.progress-bar') : null;
                
                // 24 hours in milliseconds
                const COUNTDOWN_DURATION = 24 * 60 * 60 * 1000; // 24 hours
                
                function updateCountdown() {
                    const now = new Date();
                    const orderDate = new Date(orderDateStr);
                    const elapsedMs = now - orderDate; // Time elapsed since order
                    
                    if (elapsedMs < 0) {
                        timerText.textContent = 'Захиалга хүлээгдэж байна...';
                        timerText.className = 'timer-text text-muted';
                        if (progressBar) {
                            progressBar.style.width = '100%';
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-secondary';
                        }
                        return;
                    }
                    
                    // Calculate remaining time (countdown from 24 hours)
                    const remainingMs = COUNTDOWN_DURATION - elapsedMs;
                    
                    // Calculate time components for countdown (can be negative)
                    const totalSeconds = Math.floor(Math.abs(remainingMs) / 1000);
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;
                    
                    // Format as HH:MM:SS with leading zeros, add minus if negative
                    const displayText = remainingMs < 0 
                        ? `-${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                        : `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    
                    timerText.textContent = displayText;
                    
                    // Calculate progress percentage
                    const progressPercent = (remainingMs / COUNTDOWN_DURATION) * 100;
                    
                    // Update progress bar
                    if (progressBar) {
                        progressBar.style.width = Math.max(0, Math.min(100, progressPercent)) + '%';
                        
                        // Color coding based on remaining time
                        if (remainingMs < 0 || progressPercent <= 10) {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-danger';
                            timerText.className = 'timer-text text-danger font-weight-bold';
                        } else if (progressPercent <= 25) {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
                            timerText.className = 'timer-text text-warning font-weight-bold';
                        } else if (progressPercent <= 50) {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-info';
                            timerText.className = 'timer-text text-info font-weight-bold';
                        } else {
                            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
                            timerText.className = 'timer-text text-success font-weight-bold';
                        }
                    } else {
                        // Fallback color coding for text only
                        if (remainingMs < 0 || progressPercent <= 10) {
                            timerText.className = 'timer-text text-danger font-weight-bold';
                        } else if (progressPercent <= 25) {
                            timerText.className = 'timer-text text-warning font-weight-bold';
                        } else if (progressPercent <= 50) {
                            timerText.className = 'timer-text text-info font-weight-bold';
                        } else {
                            timerText.className = 'timer-text text-success font-weight-bold';
                        }
                    }
                }
                
                // Update immediately
                updateCountdown();
                
                // Update every second
                setInterval(updateCountdown, 1000);
            });
        }

        // Initialize Delivery Trends Chart
        function initDeliveryTrendChart() {
            const ctx = document.getElementById('deliveryTrendChart');
            if (!ctx) return;

            const chartData = @json($chart_data ?? ['label' => [], 'data' => []]);
            const labels = chartData.label || [];
            const data = chartData.data || [];

            // If no data, show a message
            if (labels.length === 0 || data.length === 0) {
                ctx.parentElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-line fa-3x text-muted mb-3"></i><h5 class="text-muted">Сонгосон огнооны хүрээнд өгөгдөл олдсонгүй</h5></div>';
                return;
            }

            deliveryTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Хүргэлтийн тоо',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        lineTension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
        }

        // Initialize Status Distribution Chart
        function initStatusDistributionChart() {
            const ctx = document.getElementById('statusDistributionChart');
            if (!ctx) return;

            const newCount = {{ $new ?? 0 }};
            const activeCount = {{ $active ?? 0 }};
            const successCount = {{ $success ?? 0 }};
            const declinedCount = {{ $declined ?? 0 }};

            statusDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Шинэ', 'Хүргэлтэнд гарсан', 'Амжилттай', 'Цуцлагдсан'],
                    datasets: [{
                        data: [newCount, activeCount, successCount, declinedCount],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            });
        }

        // Show map in modal
        function showMapModal(latitude, longitude, address) {
            $('#mapModal').modal('show');
            $('#addressText').text(address || 'Хаяг олдсонгүй');
            
            // Wait for Google Maps to load
            const initMap = () => {
                if (typeof google !== 'undefined' && google.maps) {
                    const position = { lat: latitude, lng: longitude };
                    
                    // Initialize map if not already done
                    if (!map) {
                        map = new google.maps.Map(document.getElementById('mapContainer'), {
                            zoom: 15,
                            center: position,
                            mapTypeId: 'roadmap'
                        });
                    } else {
                        map.setCenter(position);
                        map.setZoom(15);
                    }
                    
                    // Remove previous marker if exists
                    if (currentMarker) {
                        currentMarker.setMap(null);
                    }
                    
                    // Add marker
                    currentMarker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: address || 'Байршил'
                    });
                    
                    // Add info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div><strong>Хаяг:</strong><br>${address || 'N/A'}</div>`
                    });
                    
                    currentMarker.addListener('click', () => {
                        infoWindow.open(map, currentMarker);
                    });
                    
                    // Open info window by default
                    infoWindow.open(map, currentMarker);
                } else {
                    // Retry after a short delay
                    setTimeout(initMap, 100);
                }
            };
            
            // Initialize map when modal is shown
            $('#mapModal').on('shown.bs.modal', function() {
                setTimeout(initMap, 100);
            });
        }

        // Initialize top phones modal
        function initTopPhonesModal() {
            // Load top phones when modal is opened
            $('#topPhonesModal').on('shown.bs.modal', function() {
                loadTopPhones();
            });

            // Reset modal when closed
            $('#topPhonesModal').on('hidden.bs.modal', function() {
                $('#topPhonesLoading').show();
                $('#topPhonesContent').hide();
                $('#topPhonesError').hide();
                $('#topPhonesTableBody').empty();
            });
            
            // Also handle direct button clicks as fallback
            $('button[data-target="#topPhonesModal"]').on('click', function() {
                // Small delay to ensure modal is shown before loading
                setTimeout(function() {
                    if ($('#topPhonesModal').hasClass('show')) {
                        loadTopPhones();
                    }
                }, 300);
            });
        }

        function loadTopPhones() {
            // Reset states
            $('#topPhonesLoading').show();
            $('#topPhonesContent').hide();
            $('#topPhonesError').hide();
            $('#topPhonesTableBody').empty();

            // Get current filter values from the form
            const startDate = document.getElementById('start_date') ? document.getElementById('start_date').value : '';
            const endDate = document.getElementById('end_date') ? document.getElementById('end_date').value : '';
            const merchantId = document.getElementById('merchant_id') ? document.getElementById('merchant_id').value : '';

            // Build query parameters
            const params = {};
            if (startDate) params.start_date = startDate;
            if (endDate) params.end_date = endDate;
            if (merchantId) params.merchant_id = merchantId;

            $.ajax({
                url: '{{ url("/home/top-phones") }}',
                method: 'GET',
                data: params,
                dataType: 'json',
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    // Always hide loading
                    $('#topPhonesLoading').hide();
                    
                    // Update modal title with date range
                    if (response && response.date_range) {
                        // Format dates as YYYY-MM-DD or use a simple format
                        function formatDate(dateStr) {
                            if (!dateStr) return '';
                            const date = new Date(dateStr + 'T00:00:00');
                            if (isNaN(date.getTime())) return dateStr;
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return year + '-' + month + '-' + day;
                        }
                        
                        const startDateFormatted = formatDate(response.date_range.start_date);
                        const endDateFormatted = formatDate(response.date_range.end_date);
                        let titleText = '<i class="fas fa-trophy"></i> Топ 10 хамгийн их давтагдсан утасны дугаар - Амжилттай хүргэгдсэн';
                        if (startDateFormatted && endDateFormatted) {
                            titleText += ' (' + startDateFormatted + ' - ' + endDateFormatted + ')';
                        }
                        if (response.merchant_filter) {
                            titleText += ' - ' + response.merchant_filter;
                        }
                        $('#topPhonesModalLabel').html(titleText);
                    }
                    
                    if (response && response.success && response.data && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item, index) {
                            tableBody += '<tr>';
                            tableBody += '<td><strong>' + (index + 1) + '</strong></td>';
                            tableBody += '<td>' + (item.shop || 'N/A') + '</td>';
                            tableBody += '<td><strong>' + (item.phone || 'N/A') + '</strong></td>';
                            tableBody += '<td>' + (item.receivername || 'N/A') + '</td>';
                            tableBody += '<td><span class="badge badge-primary">' + (item.count || 0) + '</span></td>';
                            tableBody += '</tr>';
                        });
                        
                        $('#topPhonesTableBody').html(tableBody);
                        $('#topPhonesContent').show();
                    } else {
                        $('#topPhonesError').show();
                        $('#topPhonesErrorMessage').text('Өгөгдөл олдсонгүй');
                    }
                },
                error: function(xhr, status, error) {
                    // Always hide loading on error
                    $('#topPhonesLoading').hide();
                    $('#topPhonesError').show();
                    
                    let errorMessage = 'Алдаа гарлаа';
                    if (xhr.status === 403) {
                        errorMessage = 'Та энэ үйлдлийг хийх эрхгүй байна';
                    } else if (status === 'timeout') {
                        errorMessage = 'Холболт удаан байна. Дахин оролдоно уу.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (error) {
                        errorMessage = 'Алдаа: ' + error;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Холболт тасалдсан. Интернэт холболтоо шалгана уу.';
                    } else if (xhr.status >= 500) {
                        errorMessage = 'Серверийн алдаа гарлаа. Дахин оролдоно уу.';
                    }
                    $('#topPhonesErrorMessage').text(errorMessage);
                    console.error('Top phones error:', xhr, status, error);
                },
                complete: function() {
                    // Ensure loading is hidden in all cases
                    $('#topPhonesLoading').hide();
                }
            });
        }

        // Initialize top phones by price modal
        function initTopPhonesByPriceModal() {
            // Load top phones by price when modal is opened
            $('#topPhonesByPriceModal').on('shown.bs.modal', function() {
                loadTopPhonesByPrice();
            });

            // Reset modal when closed
            $('#topPhonesByPriceModal').on('hidden.bs.modal', function() {
                $('#topPhonesByPriceLoading').show();
                $('#topPhonesByPriceContent').hide();
                $('#topPhonesByPriceError').hide();
                $('#topPhonesByPriceTableBody').empty();
            });
            
            // Also handle direct button clicks as fallback
            $('button[data-target="#topPhonesByPriceModal"]').on('click', function() {
                // Small delay to ensure modal is shown before loading
                setTimeout(function() {
                    if ($('#topPhonesByPriceModal').hasClass('show')) {
                        loadTopPhonesByPrice();
                    }
                }, 300);
            });
        }

        function loadTopPhonesByPrice() {
            // Reset states
            $('#topPhonesByPriceLoading').show();
            $('#topPhonesByPriceContent').hide();
            $('#topPhonesByPriceError').hide();
            $('#topPhonesByPriceTableBody').empty();

            // Get current filter values from the form
            const startDate = document.getElementById('start_date') ? document.getElementById('start_date').value : '';
            const endDate = document.getElementById('end_date') ? document.getElementById('end_date').value : '';
            const merchantId = document.getElementById('merchant_id') ? document.getElementById('merchant_id').value : '';

            // Build query parameters
            const params = {};
            if (startDate) params.start_date = startDate;
            if (endDate) params.end_date = endDate;
            if (merchantId) params.merchant_id = merchantId;

            $.ajax({
                url: '{{ url("/home/top-phones-by-price") }}',
                method: 'GET',
                data: params,
                dataType: 'json',
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    // Always hide loading
                    $('#topPhonesByPriceLoading').hide();
                    
                    // Update modal title with date range
                    if (response && response.date_range) {
                        // Format dates as YYYY-MM-DD or use a simple format
                        function formatDate(dateStr) {
                            if (!dateStr) return '';
                            const date = new Date(dateStr + 'T00:00:00');
                            if (isNaN(date.getTime())) return dateStr;
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return year + '-' + month + '-' + day;
                        }
                        
                        const startDateFormatted = formatDate(response.date_range.start_date);
                        const endDateFormatted = formatDate(response.date_range.end_date);
                        let titleText = '<i class="fas fa-trophy"></i> Топ 10 утасны дугаар - Барааны тооцоогоор';
                        if (startDateFormatted && endDateFormatted) {
                            titleText += ' (' + startDateFormatted + ' - ' + endDateFormatted + ')';
                        }
                        if (response.merchant_filter) {
                            titleText += ' - ' + response.merchant_filter;
                        }
                        $('#topPhonesByPriceModalLabel').html(titleText);
                    }
                    
                    if (response && response.success && response.data && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item, index) {
                            tableBody += '<tr>';
                            tableBody += '<td><strong>' + (index + 1) + '</strong></td>';
                            tableBody += '<td>' + (item.shop || 'N/A') + '</td>';
                            tableBody += '<td><strong>' + (item.phone || 'N/A') + '</strong></td>';
                            tableBody += '<td>' + (item.receivername || 'N/A') + '</td>';
                            tableBody += '<td><span class="badge badge-success">' + parseFloat(item.total_price || 0).toLocaleString('en-US') + '₮</span></td>';
                            tableBody += '<td><span class="badge badge-primary">' + (item.count || 0) + '</span></td>';
                            tableBody += '</tr>';
                        });
                        
                        $('#topPhonesByPriceTableBody').html(tableBody);
                        $('#topPhonesByPriceContent').show();
                    } else {
                        $('#topPhonesByPriceError').show();
                        $('#topPhonesByPriceErrorMessage').text('Өгөгдөл олдсонгүй');
                    }
                },
                error: function(xhr, status, error) {
                    // Always hide loading on error
                    $('#topPhonesByPriceLoading').hide();
                    $('#topPhonesByPriceError').show();
                    
                    let errorMessage = 'Алдаа гарлаа';
                    if (xhr.status === 403) {
                        errorMessage = 'Та энэ үйлдлийг хийх эрхгүй байна';
                    } else if (status === 'timeout') {
                        errorMessage = 'Холболт удаан байна. Дахин оролдоно уу.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (error) {
                        errorMessage = 'Алдаа: ' + error;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Холболт тасалдсан. Интернэт холболтоо шалгана уу.';
                    } else if (xhr.status >= 500) {
                        errorMessage = 'Серверийн алдаа гарлаа. Дахин оролдоно уу.';
                    }
                    $('#topPhonesByPriceErrorMessage').text(errorMessage);
                    console.error('Top phones by price error:', xhr, status, error);
                },
                complete: function() {
                    // Ensure loading is hidden in all cases
                    $('#topPhonesByPriceLoading').hide();
                }
            });
        }
    </script>
    @include('sweetalert::alert')
@endsection
