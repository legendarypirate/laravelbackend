@extends('admin.master')

@section('mainContent')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.14.0/sweetalert2.min.css" integrity="sha512-A374yR9LJTApGsMhH1Mn4e9yh0ngysmlMwt/uKPpudcFwLNDgN3E9S/ZeHcWTbyhb5bVHCtvqWey9DLXB4MmZg==" crossorigin="anonymous" />

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
  position: fixed;
  z-index: 99999999;
  padding-top: 10%;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
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
        -webkit-print-color-adjust:exact;
    }
    .no-print {
        display: none !important;
    }
    .print-summary {
        background-color: #f8f9fa !important;
        font-weight: bold;
    }
}
.print-summary {
    background-color: #e9ecef;
    font-weight: bold;
    border: 2px solid #dee2e6;
}
.detailed-delivery-row {
    background-color: #f8f9fa;
}
.detailed-delivery-row td {
    padding: 0.5rem 1.25rem !important;
    font-size: 0.9rem;
}

/* Modern Table Styling */
.report-table-wrapper {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.report-table-wrapper .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.report-table-wrapper .table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.report-table-wrapper .table thead th {
    padding: 16px 20px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    border: none;
    white-space: nowrap;
}

.report-table-wrapper .table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #e9ecef;
}

.report-table-wrapper .table tbody tr:hover {
    background-color: #f8f9ff;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.report-table-wrapper .table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    border: none;
    color: #495057;
    font-size: 0.95rem;
}

.report-table-wrapper .table tbody tr:last-child {
    border-bottom: none;
}

.report-table-wrapper .table tbody tr.print-summary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-weight: 700;
    font-size: 1rem;
}

.report-table-wrapper .table tbody tr.print-summary td {
    color: white;
    padding: 18px 20px;
}

.report-table-wrapper .table tbody tr.print-summary:hover {
    transform: none;
    box-shadow: none;
}

/* Empty state */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-data-message i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
}
</style>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Хүргэлтийн тайлан</h1>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row mb-3 no-print">
          <div class="col-12">
          
            <button type="button" id="__btnPrint" class="btn btn-info" style="background-color:#032EF1;">
              <a href="#" style="color:white;">Хэвлэх</a>
            </button>
            <button type="button" id="__btnExcelExport" class="btn btn-info" style="background-color:#032EF1;">
              <a href="#" style="color:white;">Эксэлээр гаргаж авах</a>
            </button>
          
          </div>
        </div>

<?php
// Set default dates to last 7 days if not provided
$default_end_date = date('Y-m-d');
$default_start_date = date('Y-m-d', strtotime('-7 days'));
$start_date = request('start_date', $default_start_date);
$end_date = request('end_date', $default_end_date);
$report_type = request('report_type', 1); // Default to summary

$customers = DB::table('users')
    ->where('role', 'customer')
    ->where('active',1)
    ->orderBy('id', 'DESC')
    ->get();
    
$drivers = DB::table('users')
    ->where('role', 'driver')
    ->where('active',1)
    ->orderBy('id', 'DESC')
    ->get();

// Calculate totals
$total_delivered = 0;
$total_declined = 0;
$total_all = 0;
$total_price = 0;
$total_number = 0;

if(isset($data)) {
    if($report_type == 2 && isset($detailed_data)) {
        // Detailed report totals
        foreach($data as $row) {
            $total_delivered += $row['delivered_count'];
            $total_declined += $row['declined_count'];
            $total_all += $row['total_count'];
            $total_price += $row['total_price'];
            $total_number += $row['total_number'];
        }
    } elseif($data->count() > 0) {
        // Summary report totals
        foreach($data as $row) {
            $total_delivered += $row->delivered_count;
            $total_declined += $row->declined_count;
            $total_all += $row->total_count;
            $total_price += $row->total_price;
            $total_number += $row->total_number;
        }
    }
}
?>

      <div class="row mb-3 d-flex align-items-end no-print">
  @if(auth()->user()->role != 'customer')
<div class="col-auto form-group me-2">
    <label for="filterByDriver">Жолооч:</label>
    <select id="filterByDriver" class="form-control">
        <option value="">Бүгд</option>
        @foreach($drivers as $driver)
        <option value="{{ $driver->name }}" @if(request('driver_id') == $driver->name) selected @endif>{{ $driver->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-auto form-group me-2">
    <label for="filterByCustomer">Харилцагч:</label>
    <select id="filterByCustomer" class="form-control">
        <option value="">Бүгд</option>
        @foreach($customers as $customer)
        <option value="{{ $customer->name }}" @if(request('customer_id') == $customer->name) selected @endif>{{ $customer->name }}</option>
        @endforeach
    </select>
</div>

  @endif

  <div class="col-auto form-group me-2">
    <label>Эхлэх:</label>
    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date }}" />
  </div>

  <div class="col-auto form-group me-2">
    <label>Дуусах:</label>
    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date }}" />
  </div>

  <div class="col-auto form-group me-2">
    <label>Тайлангийн төрөл:</label>
    <select id="report_type" class="form-control">
        <option value="1" @if($report_type == 1) selected @endif>Хураангуй</option>
        <option value="2" @if($report_type == 2) selected @endif>Дэлгэрэнгүй</option>
    </select>
  </div>

  <div class="col-auto form-group">
    <button type="button" id="filterByDateRange" class="btn btn-info" style="margin-top: 0;">Шүүх</button>
  </div>
</div>

        <!-- Report Table -->
        <div class="row">
          <div class="col-12">
            <div id="report_table">
              @if(isset($data) && (is_array($data) ? count($data) > 0 : $data->count() > 0))
              
              <!-- Summary Row for Print -->
              <div class="row mb-3 print-only" style="display: none;">
                <div class="col-12">
                  <div class="card print-summary">
                    <div class="card-body">
                      <h5>Нийт дүн:</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <p><strong>Нийт хүргэсэн:</strong> {{ number_format($total_delivered) }}</p>
                          <p><strong>Нийт татгалзсан:</strong> {{ number_format($total_declined) }}</p>
                          <p><strong>Нийт хүргэлт:</strong> {{ number_format($total_all) }}</p>
                        </div>
                        <div class="col-md-6">
                          <p><strong>Нийт үнэ:</strong> {{ number_format($total_price) }} ₮</p>
                          <p><strong>Нийт тоо:</strong> {{ number_format($total_number) }}</p>
                        </div>
                      </div>
                      @if(request()->has('start_date') && request()->has('end_date'))
                      <p><strong>Хугацаа:</strong> {{ request('start_date') }} - {{ request('end_date') }}</p>
                      @endif
                      @if(request()->has('driver_id'))
                      <p><strong>Жолооч:</strong> {{ request('driver_id') }}</p>
                      @endif
                      @if(request()->has('customer_id'))
                      <p><strong>Харилцагч:</strong> {{ request('customer_id') }}</p>
                      @endif
                      @if(request()->has('report_type'))
                      <p><strong>Тайлангийн төрөл:</strong> {{ request('report_type') == 1 ? 'Хураангуй' : 'Дэлгэрэнгүй' }}</p>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              @if($report_type == 1)
              <!-- Summary Report Table -->
              <div class="report-table-wrapper">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Огноо</th>
                      <th>Хүргэсэн</th>
                      <th>Цуцалсан</th>
                      <th>Нийт</th>
                      <th>Нийт үнэ</th>
                      <th>Нийт тоо</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data as $row)
                    <tr>
                      <td>{{ $row->date }}</td>
                      <td>{{ $row->delivered_count }}</td>
                      <td>{{ $row->declined_count }}</td>
                      <td>{{ $row->total_count }}</td>
                      <td>{{ number_format($row->total_price) }} ₮</td>
                      <td>{{ number_format($row->total_number) }}</td>
                    </tr>
                    @endforeach
                    <!-- Total Row -->
                    <tr class="print-summary">
                      <td><strong>Нийт:</strong></td>
                      <td><strong>{{ number_format($total_delivered) }}</strong></td>
                      <td><strong>{{ number_format($total_declined) }}</strong></td>
                      <td><strong>{{ number_format($total_all) }}</strong></td>
                      <td><strong>{{ number_format($total_price) }} ₮</strong></td>
                      <td><strong>{{ number_format($total_number) }}</strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              @elseif($report_type == 2)
              <!-- Detailed Report Table -->
              <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">Хүргэлтийн дэлгэрэнгүй тайлан</h5>
                </div>
                <div class="card-body">
                  @foreach($data as $dateGroup)
                  @php
                    // Convert to object if it's an array
                    if (is_array($dateGroup)) {
                        $dateGroup = (object) $dateGroup;
                    }
                  @endphp
                  <div class="date-group mb-4">
                    <h6 class="mb-3">
                      <strong>Огноо: {{ $dateGroup->date }}</strong>
                      <span class="badge bg-info ms-2">Хүргэсэн: {{ $dateGroup->delivered_count }}</span>
                      <span class="badge bg-warning ms-2">Цуцалсан: {{ $dateGroup->declined_count }}</span>
                      <span class="badge bg-secondary ms-2">Нийт: {{ $dateGroup->total_count }}</span>
                      <span class="badge bg-success ms-2">Үнэ: {{ number_format($dateGroup->total_price) }} ₮</span>
                      <span class="badge bg-dark ms-2">Тоо: {{ number_format($dateGroup->total_number) }}</span>
                    </h6>
                    
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Үүсгэсэн</th>
                          <th>Хүргэсэн</th>
                          <th>Харилцагч</th>
                          <th>Утас</th>
                          <th>Хаяг</th>
                          <th>Төлөв</th>
                          <th>Жолооч</th>
                          <th>Гарын үсэг</th>
                          <th>Үнэ</th>
                          <th>Тоо</th>
                          <th>Тэмдэглэл</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($dateGroup->deliveries as $delivery)
                        @php
                          $status_text = '';
                          switch($delivery->status) {
                            case 1: $status_text = '<span class="badge bg-primary">Шинэ</span>'; break;
                            case 2: $status_text = '<span class="badge bg-warning">Жолоочид</span>'; break;
                            case 3: $status_text = '<span class="badge bg-success">Хүргэгдсэн</span>'; break;
                            case 4: $status_text = '<span class="badge bg-danger">Цуцалсан</span>'; break;
                            default: $status_text = '<span class="badge bg-secondary">Тодорхойгүй</span>';
                          }
                        @endphp
                        <tr class="detailed-delivery-row">
                          <td>{{ $delivery->track }}</td>
                          <td>{{ $delivery->created_at }}</td>
                          <td>{{ $delivery->delivered_at }}</td>
                          <td>{{ $delivery->shop }}</td>
                          <td>{{ $delivery->phone }}</td>
                          <td>{{ $delivery->address }}</td>
                          <td>{!! $status_text !!}</td>
                          <td>{{ $delivery->driver }}</td>
                          <td>
                            @if($delivery->sign_image)
                              @php
                                // Check if it's a full URL or just a filename
                                $imageUrl = $delivery->sign_image;
                                if (!Str::startsWith($imageUrl, ['http://', 'https://'])) {
                                  // Assuming images are stored in storage/app/public/signImage
                                  $imageUrl = asset('storage/signImage/' . basename($delivery->sign_image));
                                }
                              @endphp
                            <button type="button" class="btn btn-sm btn-outline-primary view-signature"
                                  data-image="{{ $imageUrl }}"
                                  data-delivery-id="{{ $delivery->id }}"
                                  data-bs-toggle="tooltip"
                                  title="Гарын үсгийг харах"
                                  style="padding: 0; border: none; background: none;">
                            <img src="{{ $imageUrl }}"
                                 alt="Гарын үсэг"
                                 style="max-width: 80px; max-height: 50px; object-fit: contain; cursor: pointer;"
                                 class="signature-thumbnail">
                            </button>
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                          <td>{{ $delivery->status == 3 ? number_format($delivery->price) : '0' }}</td>
                          <td>{{ $delivery->status == 3 ? number_format($delivery->number) : '0' }}</td>
                          <td>{{ $delivery->note }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  @endforeach
                  
                  <!-- Total Summary -->
                  <div class="alert alert-info mt-4">
                    <div class="row">
                      <div class="col-md-6">
                        <p><strong>Нийт хүргэсэн:</strong> {{ number_format($total_delivered) }}</p>
                        <p><strong>Нийт цуцалсан:</strong> {{ number_format($total_declined) }}</p>
                        <p><strong>Нийт хүргэлт:</strong> {{ number_format($total_all) }}</p>
                      </div>
                      <div class="col-md-6">
                        <p><strong>Нийт үнэ:</strong> {{ number_format($total_price) }} ₮</p>
                        <p><strong>Нийт тоо:</strong> {{ number_format($total_number) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              
              @else
              <div class="no-data-message">
                <i class="fas fa-inbox"></i>
                <h4>Хүргэлт олдсонгүй</h4>
                <p>Сонгосон хугацаанд хүргэлтийн мэдээлэл байхгүй байна.</p>
              </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </section>

    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signatureModalLabel">Гарын үсэг</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Хаах"></button>
      </div>
      <div class="modal-body text-center">
        <img id="signatureImage" src="" alt="Гарын үсэг" class="img-fluid" style="max-height: 70vh;">
      </div>
      <div class="modal-footer">
        <div class="me-auto">
          <span id="deliveryInfo" class="text-muted"></span>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Хаах</button>
        <a id="downloadSignature" href="#" class="btn btn-primary" download>
          <i class="fas fa-download"></i> Татах
        </a>
      </div>
    </div>
  </div>
</div>
</div>

@include('sweetalert::alert')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
// Excel Export functionality - Direct download approach
$('#__btnExcelExport').on('click', function() {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    let start_date = urlParams.get('start_date');
    let end_date = urlParams.get('end_date');
    let driver_id = urlParams.get('driver_id');
    let customer_id = urlParams.get('customer_id');
    let report_type = urlParams.get('report_type');

    // If dates are not in URL, try to get from form inputs
    if (!start_date || !end_date) {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        
        // Check if dates are in form inputs
        if(!start_date || !end_date){
            alert('Хугацаа хоёуланг нь сонгоно уу');
            return;
        }
    }

    // Build URL for Excel export
    let url = '{{ url("/admin/report") }}' + '?start_date=' + start_date + '&end_date=' + end_date + '&export=excel';
    
    // Add report type
    const selectedReportType = $('#report_type').val();
    if (selectedReportType) {
        url += '&report_type=' + selectedReportType;
    }
    
    // Add driver filter (check URL params first, then dropdown)
    if(driver_id) {
        url += '&driver_id=' + driver_id;
    } else {
        const selectedDriver = $('#filterByDriver').val();
        if (selectedDriver) url += '&driver_id=' + selectedDriver;
    }
    
    // Add customer filter (check URL params first, then dropdown)
    @if(auth()->user()->role == 'customer')
        url += '&customer_id=' + '{{ auth()->user()->name }}';
    @else
        if(customer_id) {
            url += '&customer_id=' + customer_id;
        } else {
            const selectedCustomer = $('#filterByCustomer').val();
            if (selectedCustomer) url += '&customer_id=' + selectedCustomer;
        }
    @endif

    // Show loading indicator
    const originalHtml = $('#__btnExcelExport').html();
    $('#__btnExcelExport').html('<i class="fas fa-spinner fa-spin"></i> Тайлан бэлтгэж байна...');
    $('#__btnExcelExport').prop('disabled', true);

    // Use direct download - create anchor and click it
    // This is more reliable than AJAX for file downloads
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after a short delay
    setTimeout(function() {
        $('#__btnExcelExport').html(originalHtml);
        $('#__btnExcelExport').prop('disabled', false);
    }, 1000);
});

// Filter button
$('#filterByDateRange').on('click', function() {
    let driver_id = $('#filterByDriver').val();
    let customer_id = $('#filterByCustomer').val();
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();
    let report_type = $('#report_type').val();

    if(!start_date || !end_date){
        alert('Хугацаа хоёуланг нь сонгоно уу');
        return;
    }

    // Build URL with selected filters (both driver and customer can be selected simultaneously)
    let url = '{{ url("/admin/report") }}' + '?start_date=' + start_date + '&end_date=' + end_date + '&report_type=' + report_type;
    
    if(driver_id) url += '&driver_id=' + driver_id;
    
    // If user is customer, use their name instead of the dropdown value
    @if(auth()->user()->role == 'customer')
        url += '&customer_id=' + '{{ auth()->user()->name }}';
    @else
        if(customer_id) url += '&customer_id=' + customer_id;
    @endif

    window.location.href = url;
});

// Print functionality - updated for both report types
$('#__btnPrint').on('click', function() {
    const report_type = $('#report_type').val();
    
    // Create a new window for printing
    let printWindow = window.open('', '_blank');
    
    if (report_type == 1) {
        // Print for Summary Report
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Хүргэлтийн тайлан - Хураангуй</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .print-header h1 { margin: 0; color: #333; }
                    .summary-card { background-color: #f8f9fa; border: 2px solid #dee2e6; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                    .summary-card h5 { margin-top: 0; color: #495057; }
                    .summary-row { display: flex; justify-content: space-between; flex-wrap: wrap; }
                    .summary-col { flex: 1; min-width: 200px; }
                    .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    .table th, .table td { border: 1px solid #333; padding: 8px 12px; text-align: left; }
                    .table th { background-color: #e9ecef; font-weight: bold; }
                    .total-row { background-color: #f8f9fa; font-weight: bold; }
                    .filter-info { margin-bottom: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px; }
                    @media print {
                        body { margin: 0; }
                        .table th { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
                        .total-row { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
                        .summary-card { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
                        .filter-info { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>Хүргэлтийн тайлан - Хураангуй</h1>
                </div>
        `);

        // Add filter information
        let filterInfo = '';
        @if(request()->has('start_date') && request()->has('end_date'))
            filterInfo += `<p><strong>Хугацаа:</strong> {{ request('start_date') }} - {{ request('end_date') }}</p>`;
        @endif
        @if(request()->has('driver_id'))
            filterInfo += `<p><strong>Жолооч:</strong> {{ request('driver_id') }}</p>`;
        @endif
        @if(request()->has('customer_id'))
            filterInfo += `<p><strong>Харилцагч:</strong> {{ request('customer_id') }}</p>`;
        @endif
        
        if (filterInfo) {
            printWindow.document.write(`<div class="filter-info"><strong>Шүүлт:</strong>${filterInfo}</div>`);
        }

        // Add summary
        printWindow.document.write(`
            <div class="summary-card">
                <h5>Нийт дүн</h5>
                <div class="summary-row">
                    <div class="summary-col">
                        <p><strong>Нийт хүргэсэн:</strong> {{ number_format($total_delivered) }}</p>
                        <p><strong>Нийт цуцалсан:</strong> {{ number_format($total_declined) }}</p>
                        <p><strong>Нийт хүргэлт:</strong> {{ number_format($total_all) }}</p>
                    </div>
                    <div class="summary-col">
                        <p><strong>Нийт үнэ:</strong> {{ number_format($total_price) }} ₮</p>
                        <p><strong>Нийт тоо:</strong> {{ number_format($total_number) }}</p>
                    </div>
                </div>
            </div>
        `);

        // Add table
        printWindow.document.write(`
            <table class="table">
                <thead>
                    <tr>
                        <th>Огноо</th>
                        <th>Хүргэсэн тоо</th>
                        <th>Цуцалсан</th>
                        <th>Нийт</th>
                        <th>Нийт үнэ</th>
                        <th>Т/Ширхэг</th>
                    </tr>
                </thead>
                <tbody>
        `);

        // Add table rows
        @if(isset($data) && $report_type == 1)
            @foreach($data as $row)
                printWindow.document.write(`
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->delivered_count }}</td>
                        <td>{{ $row->declined_count }}</td>
                        <td>{{ $row->total_count }}</td>
                        <td>{{ number_format($row->total_price) }}</td>
                        <td>{{ number_format($row->total_number) }}</td>
                    </tr>
                `);
            @endforeach
        @endif

        // Add total row
        printWindow.document.write(`
                    <tr class="total-row">
                        <td><strong>Нийт:</strong></td>
                        <td><strong>{{ number_format($total_delivered) }}</strong></td>
                        <td><strong>{{ number_format($total_declined) }}</strong></td>
                        <td><strong>{{ number_format($total_all) }}</strong></td>
                        <td><strong>{{ number_format($total_price) }}</strong></td>
                        <td><strong>{{ number_format($total_number) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        `);
    } else {
        // Print for Detailed Report
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Хүргэлтийн тайлан - Дэлгэрэнгүй</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .print-header h1 { margin: 0; color: #333; }
                    .summary-card { background-color: #f8f9fa; border: 2px solid #dee2e6; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                    .date-group { margin-bottom: 30px; page-break-inside: avoid; }
                    .date-header { background-color: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold; }
                    .badge { padding: 3px 8px; border-radius: 10px; font-size: 12px; margin-right: 5px; }
                    .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
                    .table th, .table td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
                    .table th { background-color: #e9ecef; font-weight: bold; }
                    .filter-info { margin-bottom: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px; }
                    @media print {
                        body { margin: 0; }
                        .table th { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
                        .date-header { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
                        .summary-card { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
                        .filter-info { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>Хүргэлтийн тайлан - Дэлгэрэнгүй</h1>
                </div>
        `);

        // Add filter information
        let filterInfo = '';
        @if(request()->has('start_date') && request()->has('end_date'))
            filterInfo += `<p><strong>Хугацаа:</strong> {{ request('start_date') }} - {{ request('end_date') }}</p>`;
        @endif
        @if(request()->has('driver_id'))
            filterInfo += `<p><strong>Жолооч:</strong> {{ request('driver_id') }}</p>`;
        @endif
        @if(request()->has('customer_id'))
            filterInfo += `<p><strong>Харилцагч:</strong> {{ request('customer_id') }}</p>`;
        @endif
        
        if (filterInfo) {
            printWindow.document.write(`<div class="filter-info"><strong>Шүүлт:</strong>${filterInfo}</div>`);
        }

        // Add summary
        printWindow.document.write(`
            <div class="summary-card">
                <h5>Нийт дүн</h5>
                <div class="row">
                    <div class="col-6">
                        <p><strong>Нийт хүргэсэн:</strong> {{ number_format($total_delivered) }}</p>
                        <p><strong>Нийт цуцалсан:</strong> {{ number_format($total_declined) }}</p>
                        <p><strong>Нийт хүргэлт:</strong> {{ number_format($total_all) }}</p>
                    </div>
                    <div class="col-6">
                        <p><strong>Нийт үнэ:</strong> {{ number_format($total_price) }} ₮</p>
                        <p><strong>Нийт тоо:</strong> {{ number_format($total_number) }}</p>
                    </div>
                </div>
            </div>
        `);

        // Add detailed data by date groups
        @if(isset($data) && $report_type == 2)
            @foreach($data as $dateGroup)
                @php
                    // Convert to array for print
                    if (is_object($dateGroup)) {
                        $dateGroup = (array) $dateGroup;
                    }
                @endphp
                printWindow.document.write(`
                    <div class="date-group">
                        <div class="date-header">
                            Огноо: {{ $dateGroup['date'] }}
                            <span style="background-color: #0dcaf0; color: white;" class="badge">Хүргэсэн: {{ $dateGroup['delivered_count'] }}</span>
                            <span style="background-color: #ffc107; color: black;" class="badge">Цуцалсан: {{ $dateGroup['declined_count'] }}</span>
                            <span style="background-color: #6c757d; color: white;" class="badge">Нийт: {{ $dateGroup['total_count'] }}</span>
                            <span style="background-color: #198754; color: white;" class="badge">Үнэ: {{ number_format($dateGroup['total_price']) }} ₮</span>
                            <span style="background-color: #212529; color: white;" class="badge">Тоо: {{ number_format($dateGroup['total_number']) }}</span>
                        </div>
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Үүсгэсэн</th>
                                    <th>Хүргэсэн</th>
                                    <th>Харилцагч</th>
                                    <th>Утас</th>
                                    <th>Хаяг</th>
                                    <th>Төлөв</th>
                                    <th>Жолооч</th>
                                    <th>Гарын үсэг</th>
                                    <th>Үнэ</th>
                                    <th>Тоо</th>
                                    <th>Тэмдэглэл</th>
                                </tr>
                            </thead>
                            <tbody>
                `);
                
                @foreach($dateGroup['deliveries'] as $delivery)
                    @php
                        $status_text = '';
                        switch($delivery->status) {
                            case 1: $status_text = '<span style="background-color: #0d6efd; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">Шинэ</span>'; break;
                            case 2: $status_text = '<span style="background-color: #ffc107; color: black; padding: 2px 6px; border-radius: 4px; font-size: 10px;">Жолоочид</span>'; break;
                            case 3: $status_text = '<span style="background-color: #198754; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">Хүргэгдсэн</span>'; break;
                            case 4: $status_text = '<span style="background-color: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">Цуцалсан</span>'; break;
                            default: $status_text = '<span style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">Тодорхойгүй</span>';
                        }
                    @endphp
                    printWindow.document.write(`
                        <tr>
                            <td>{{ $delivery->track }}</td>
                            <td>{{ $delivery->created_at }}</td>
                            <td>{{ $delivery->delivered_at }}</td>
                            <td>{{ $delivery->shop }}</td>
                            <td>{{ $delivery->phone }}</td>
                            <td>{{ $delivery->address }}</td>
                            <td>${@json($status_text)}</td>
                            <td>{{ $delivery->driver }}</td>
                            <td>
                              @if($delivery->sign_image)
                                @php
                                  // Check if it's a full URL or just a filename
                                  $imageUrl = $delivery->sign_image;
                                  if (!Str::startsWith($imageUrl, ['http://', 'https://'])) {
                                    // Assuming images are stored in storage/app/public/signImage
                                    $imageUrl = asset('storage/signImage/' . basename($delivery->sign_image));
                                  }
                                @endphp
                              <img src="{{ $imageUrl }}"
                                   alt="Гарын үсэг"
                                   style="max-width: 80px; max-height: 50px; object-fit: contain; cursor: pointer;"
                                   class="signature-thumbnail">
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td>{{ $delivery->status == 3 ? number_format($delivery->price) : '0' }}</td>
                            <td>{{ $delivery->status == 3 ? number_format($delivery->number) : '0' }}</td>
                            <td>{{ $delivery->note }}</td>
                        </tr>
                    `);
                @endforeach
                
                printWindow.document.write(`
                            </tbody>
                        </table>
                    </div>
                `);
            @endforeach
        @endif
    }
    
    // Add footer and close
    printWindow.document.write(`
            <div style="margin-top: 20px; text-align: right; font-size: 12px; color: #666;">
                Хэвлэсэн: ${new Date().toLocaleString()}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    
    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
});

// Function to populate form inputs from URL parameters when page loads
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Auto-load report with default dates if no dates are provided and no data exists
    const startDate = urlParams.get('start_date');
    const endDate = urlParams.get('end_date');
    const hasData = @json(isset($data) && (is_array($data) ? count($data) > 0 : (is_object($data) && method_exists($data, 'count') ? $data->count() > 0 : false)));
    
    if ((!startDate || !endDate) && !hasData) {
        // Set default dates (last 7 days)
        const today = new Date();
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);
        
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        const defaultStart = formatDate(sevenDaysAgo);
        const defaultEnd = formatDate(today);
        
        $('#start_date').val(defaultStart);
        $('#end_date').val(defaultEnd);
        
        // Build URL with default dates and current filters
        let url = '{{ url("/admin/report") }}' + '?start_date=' + defaultStart + '&end_date=' + defaultEnd + '&report_type=1';
        
        // Preserve existing filters if any
        const driverId = urlParams.get('driver_id');
        const customerId = urlParams.get('customer_id');
        const reportType = urlParams.get('report_type');
        
        if (driverId) url += '&driver_id=' + driverId;
        if (customerId) url += '&customer_id=' + customerId;
        if (reportType) url += '&report_type=' + reportType;
        
        // Auto-load the report
        window.location.href = url;
    }
});


// Signature image modal functionality
$(document).ready(function() {
    // Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Handle signature image click
    $(document).on('click', '.view-signature', function() {
        const imageUrl = $(this).data('image');
        const deliveryId = $(this).data('delivery-id');
        
        // Set the image source
        $('#signatureImage').attr('src', imageUrl);
        
        // Set delivery info
        $('#deliveryInfo').text('Хүргэлтийн дугаар: ' + deliveryId);
        
        // Set download link
        $('#downloadSignature').attr('href', imageUrl);
        
        // Show modal
        const signatureModal = new bootstrap.Modal(document.getElementById('signatureModal'));
        signatureModal.show();
    });
    
    // Handle modal image loading error
    $('#signatureImage').on('error', function() {
        $(this).attr('src', '{{ asset("images/no-image.png") }}');
        $(this).attr('alt', 'Зураг олдсонгүй');
    });
});
</script>
@endsection
