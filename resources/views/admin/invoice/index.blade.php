@extends('admin.master')

@section('mainContent')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Нэхэмжлэлийн удирдлага</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Нүүр</a></li>
              <li class="breadcrumb-item active">Нэхэмжлэл</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Statistics Section -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ number_format($stats['total_invoices']) }}</h3>
                <p>Нийт нэхэмжлэл</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-invoice"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ number_format($stats['pending_invoices']) }}</h3>
                <p>Хүлээгдэж буй</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ number_format($stats['paid_invoices']) }}</h3>
                <p>Төлөгдсөн</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ number_format($stats['overdue_invoices']) }}</h3>
                <p>Хугацаа хэтэрсэн</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Нэхэмжлэлийн жагсаалт</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createInvoiceModal">
                    <i class="fas fa-plus"></i> Шинэ нэхэмжлэл
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Filter Form -->
                <div class="row mb-3">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ url('/invoice/index') }}">
                          <div class="row">
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Төлөв</label>
                                <select class="form-control" name="status" id="statusFilter">
                                  <option value="">Бүгд ({{ $statusCounts['all'] }})</option>
                                  <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Хүлээгдэж буй ({{ $statusCounts['pending'] }})
                                  </option>
                                  <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>
                                    Төлөгдсөн ({{ $statusCounts['paid'] }})
                                  </option>
                                  <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>
                                    Хугацаа хэтэрсэн ({{ $statusCounts['overdue'] }})
                                  </option>
                                  <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Цуцлагдсан ({{ $statusCounts['cancelled'] }})
                                  </option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Огнооноос</label>
                                <input type="date" class="form-control" name="start_date" id="startDateFilter"
                                       value="{{ request('start_date') }}">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Огноо хүртэл</label>
                                <input type="date" class="form-control" name="end_date" id="endDateFilter"
                                       value="{{ request('end_date') }}">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Хайх</label>
                                <input type="text" class="form-control" name="search" placeholder="Нэхэмжлэлийн дугаар, харилцагч..."
                                       value="{{ request('search') }}">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12 text-right">
                              <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Шүүх
                              </button>
                              <a href="{{ url('/invoice/index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Цэвэрлэх
                              </a>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Invoices List Table -->
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Нэхэмжлэлийн дугаар</th>
                        <th>Харилцагч</th>
                        <th>Огноо</th>
                        <th>Дуусах огноо</th>
                        <th>Нийт дүн</th>
                        <th>Төлөв</th>
                        <th>Үйлдэл</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($invoices as $key => $invoice)
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                          <strong>{{ $invoice->invoice_number }}</strong>
                          @if($invoice->notes)
                            <br><small class="text-muted">{{ Str::limit($invoice->notes, 30) }}</small>
                          @endif
                        </td>
                        <td>
                          {{ $invoice->customer_name }}<br>
                          <small class="text-muted">{{ $invoice->customer_email }}</small>
                          @if($invoice->customer_phone)
                            <br><small class="text-muted">{{ $invoice->customer_phone }}</small>
                          @endif
                        </td>
                        <td>{{ $invoice->formatted_date }}</td>
                        <td>{{ $invoice->formatted_due_date }}</td>
                        <td class="text-right">{{ number_format($invoice->total, 2) }} ₮</td>
                        <td>
                          @php
                            $statusText = '';
                            $statusClass = '';
                            switch($invoice->status) {
                              case 'pending':
                                $statusClass = 'warning';
                                $statusText = 'Хүлээгдэж буй';
                                break;
                              case 'paid':
                                $statusClass = 'success';
                                $statusText = 'Төлөгдсөн';
                                break;
                              case 'overdue':
                                $statusClass = 'danger';
                                $statusText = 'Хугацаа хэтэрсэн';
                                break;
                              case 'cancelled':
                                $statusClass = 'secondary';
                                $statusText = 'Цуцлагдсан';
                                break;
                            }
                          @endphp
                          <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                          @if($invoice->status == 'pending' && $invoice->due_date < now())
                            <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Хугацаа хэтэрсэн</small>
                          @endif
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm view-invoice"
                                    data-id="{{ $invoice->id }}" data-toggle="modal" data-target="#viewInvoiceModal">
                              <i class="fas fa-eye"></i>
                            </button>
                            @if($invoice->pdf_url)
                            <button type="button" class="btn btn-primary btn-sm show-qr"
                                    data-id="{{ $invoice->id }}"
                                    data-uuid="{{ $invoice->uuid }}"
                                    data-pdf-url="{{ $invoice->pdf_url }}"
                                    data-toggle="modal" data-target="#qrCodeModal">
                              <i class="fas fa-qrcode"></i>
                            </button>
                            @endif
                            @if($invoice->status == 'pending')
                            <button type="button" class="btn btn-success btn-sm resolve-invoice"
                                    data-id="{{ $invoice->id }}" data-number="{{ $invoice->invoice_number }}"
                                    data-toggle="modal" data-target="#resolveInvoiceModal">
                              <i class="fas fa-check"></i>
                            </button>
                            @endif
                            <button type="button" class="btn btn-danger btn-sm delete-invoice"
                                    data-id="{{ $invoice->id }}" data-number="{{ $invoice->invoice_number }}">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="8" class="text-center">Нэхэмжлэл олдсонгүй</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                <div class="row mt-3">
                  <div class="col-md-6">
                    <div class="dataTables_info">
                      Нийт {{ $invoices->total() }} нэхэмжлэлээс {{ $invoices->firstItem() }}-{{ $invoices->lastItem() }} харуулж байна
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="float-right">
                      {{ $invoices->withQueryString()->links() }}
                    </div>
                  </div>
                </div>
                @endif
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <!-- Create Invoice Modal -->
  @include('admin.invoice.create-modal')

  <!-- View Invoice Modal -->
  @include('admin.invoice.view-modal')

  <!-- Resolve Invoice Modal -->
  @include('admin.invoice.resolve-modal')

  <!-- QR Code Modal -->
  <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrCodeModalLabel">
            <i class="fas fa-qrcode mr-2"></i>
            QR код ба PDF файл
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="pdfTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="qr-tab" data-toggle="tab" href="#qrTab" role="tab">
                <i class="fas fa-qrcode mr-1"></i> QR код
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pdf-tab" data-toggle="tab" href="#pdfTab" role="tab">
                <i class="fas fa-file-pdf mr-1"></i> PDF харах
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active text-center" id="qrTab" role="tabpanel">
              <p class="mb-3">Нэхэмжлэлийн PDF файлыг уншуулахын тулд QR кодыг утасны камероор уншуулна уу</p>
              <div id="qrCodeContainer" class="mb-3">
                <!-- QR code will be generated here -->
              </div>
              <div id="qrPdfUrl" class="mb-3">
                <a href="#" id="pdfLink" target="_blank" class="btn btn-primary">
                  <i class="fas fa-external-link-alt mr-1"></i> PDF файл шинэ цонхонд нээх
                </a>
              </div>
            </div>
            <div class="tab-pane fade" id="pdfTab" role="tabpanel">
              <div style="height: 600px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                <iframe id="pdfViewer" src="" style="width: 100%; height: 100%; border: none;"></iframe>
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

<script>
$(document).ready(function() {
    // View invoice details
   $(document).on('click', '.view-invoice', function() {
    var invoiceId = $(this).data('id');
    console.log('Нэхэмжлэлийн ID:', invoiceId);
    
    $.ajax({
        url: '/invoice/' + invoiceId,
        type: 'GET',
        success: function(response) {
            console.log('Бүтэн Response:', response); // Энд бүх мэдээллийг харах
            console.log('Items:', response.data.items);
            console.log('Subtotal:', response.data.subtotal);
            console.log('Tax:', response.data.tax);
            console.log('Total:', response.data.total);
            
            if(response.success) {
                // Нэхэмжлэлийн мэдээлэл бөглөх
                populateInvoiceModal(response.data);
                $('#viewInvoiceModal').modal('show');
            } else {
                Swal.fire('Алдаа!', response.message || 'Мэдээлэл олдсонгүй', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX алдаа:', error);
            Swal.fire('Алдаа!', 'Серверт холбогдоход алдаа гарлаа', 'error');
        }
    });
});
    // Огноог форматлах функц
    function formatDate(dateString) {
        if (!dateString) return '';
        
        try {
            // ISO форматыг задлах
            if (dateString.includes('T')) {
                // 2025-12-21T00:00:00.000000Z -> 2025-12-21
                return dateString.split('T')[0];
            }
            
            return dateString;
        } catch (error) {
            console.error('Огноо форматлахад алдаа:', error);
            return dateString;
        }
    }

    // Нэхэмжлэлийн мэдээлэл бөглөх функц
    // Нэхэмжлэлийн мэдээлэл бөглөх функц
function populateInvoiceModal(invoice) {
    // Үндсэн мэдээлэл
    $('#viewInvoiceNumber').text(invoice.invoice_number);
    $('#viewCustomerName').text(invoice.customer_name);
    $('#viewCustomerEmail').text(invoice.customer_email);
    $('#viewCustomerPhone').text(invoice.customer_phone || '-');

    const issuer = invoice.issuer_profile_snapshot || null;
    const bank = invoice.issuer_bank_snapshot || null;

    $('#viewIssuerName').text(issuer?.name || 'Сонгоогүй');
    $('#viewIssuerRegister').text(issuer?.register_number || '-');
    $('#viewIssuerEmail').text(issuer?.email || '-');
    $('#viewIssuerPhone').text(issuer?.phone || '-');
    $('#viewIssuerAddress').text(issuer?.address || '-');

    $('#viewIssuerBankName').text(bank?.bank_name || '-');
    const bankAccountText = bank ? [bank.account_number, bank.account_name].filter(Boolean).join(' | ') : '-';
    $('#viewIssuerBankAccount').text(bankAccountText || '-');
    $('#viewIssuerBankIban').text(bank?.iban || '-');
    
    // Огноог форматлах
    $('#viewInvoiceDate').text(formatDate(invoice.invoice_date));
    $('#viewDueDate').text(formatDate(invoice.due_date));
    
    // Дүнг форматлах
    // Note: viewTotal shows "Нийт дүн" (subtotal without tax), viewSubtotal shows "Татвартай нийт" (total with tax)
    $('#viewTotal').text(formatCurrency(invoice.subtotal));
    $('#viewTax').text(formatCurrency(invoice.tax));
    $('#viewSubtotal').text(formatCurrency(invoice.total));
    
    // Төлөв
    let statusText = getStatusText(invoice.status);
    $('#viewStatus').html('<span class="badge badge-' + getStatusClass(invoice.status) + '">' + statusText + '</span>');
    
    // Бараанууд
    // item.price is stored as the unit price WITHOUT VAT
    let itemsHtml = '';
    
    if (invoice.items && Array.isArray(invoice.items)) {
        invoice.items.forEach(function(item, index) {
            let quantity = parseFloat(item.quantity) || 0;
            let unitPrice = parseFloat(item.price) || 0; // Unit price WITHOUT VAT
            
            // Calculate line total WITHOUT VAT
            let lineTotal = unitPrice * quantity;
            
            itemsHtml += '<tr>';
            itemsHtml += '<td>' + (index + 1) + '</td>';
            itemsHtml += '<td>' + (item.description || '') + '</td>';
            itemsHtml += '<td class="text-right">' + quantity + '</td>';
            itemsHtml += '<td class="text-right">' + formatCurrency(unitPrice) + '</td>';
            itemsHtml += '<td class="text-right">' + formatCurrency(lineTotal) + '</td>';
            itemsHtml += '</tr>';
        });
    }
    $('#viewItemsTable tbody').html(itemsHtml);
    
    // Тэмдэглэл
    $('#viewNotes').text(invoice.notes || '-');
    
    // Төлбөрийн мэдээлэл
    if (invoice.status === 'paid') {
        $('#paymentInfo').show();
        $('#viewPaymentMethod').text(invoice.payment_method || '-');
        $('#viewPaymentDate').text(formatDate(invoice.payment_date) || '-');
        $('#viewPaidAt').text(formatDate(invoice.paid_at) || '-');
    } else {
        $('#paymentInfo').hide();
    }
}
    // Төлөв класс авах
    function getStatusClass(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'paid': return 'success';
            case 'overdue': return 'danger';
            case 'cancelled': return 'secondary';
            default: return 'info';
        }
    }

    // Төлөв текст авах
    function getStatusText(status) {
        switch(status) {
            case 'pending': return 'Хүлээгдэж буй';
            case 'paid': return 'Төлөгдсөн';
            case 'overdue': return 'Хугацаа хэтэрсэн';
            case 'cancelled': return 'Цуцлагдсан';
            default: return status;
        }
    }

    // Мөнгөн дүнг форматлах
    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString('mn-MN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ₮';
    }

    // Хэвлэх товч
    $(document).on('click', '#printInvoice', function() {
        // Modal хаах
        $('#viewInvoiceModal').modal('hide');
        
        // Хэвлэх хуудас үүсгэх
        var printWindow = window.open('', '_blank');
        
        // Нэхэмжлэлийн HTML үүсгэх
        var invoiceHTML = `
            <!DOCTYPE html>
            <html lang="mn">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Нэхэмжлэл - ${$('#viewInvoiceNumber').text()}</title>
                <style>
                    /* Хэвлэх стиллүүд */
                    @page {
                        margin: 0.5cm;
                        size: A4 portrait;
                    }
                    
                    @media print {
                        body {
                            font-family: 'Helvetica', 'Arial', sans-serif;
                            font-size: 12pt;
                            line-height: 1.4;
                            color: #000;
                            margin: 0;
                            padding: 0;
                        }
                        
                        .invoice-container {
                            max-width: 21cm;
                            margin: 0 auto;
                            padding: 1cm;
                            background: white;
                        }
                        
                        .invoice-header {
                            text-align: center;
                            border-bottom: 2px solid #333;
                            padding-bottom: 15px;
                            margin-bottom: 30px;
                        }
                        
                        .invoice-title {
                            font-size: 24pt;
                            font-weight: bold;
                            margin: 0;
                            color: #2c3e50;
                        }
                        
                        .invoice-number {
                            font-size: 14pt;
                            color: #7f8c8d;
                            margin: 5px 0;
                        }
                        
                        .invoice-date {
                            font-size: 12pt;
                            color: #7f8c8d;
                        }
                        
                        .company-info, .customer-info {
                            margin-bottom: 20px;
                            padding: 15px;
                            background: #f8f9fa;
                            border-radius: 5px;
                        }
                        
                        .info-title {
                            font-size: 14pt;
                            font-weight: bold;
                            margin-bottom: 10px;
                            color: #2c3e50;
                            border-bottom: 1px solid #ddd;
                            padding-bottom: 5px;
                        }
                        
                        .info-row {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 10px;
                        }
                        
                        .info-label {
                            font-weight: bold;
                            min-width: 120px;
                        }
                        
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                        }
                        
                        th {
                            background-color: #34495e;
                            color: white;
                            padding: 12px 8px;
                            text-align: left;
                            font-weight: bold;
                        }
                        
                        td {
                            padding: 10px 8px;
                            border-bottom: 1px solid #ddd;
                        }
                        
                        .text-right {
                            text-align: right;
                        }
                        
                        .text-center {
                            text-align: center;
                        }
                        
                        .total-section {
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 2px solid #333;
                        }
                        
                        .total-row {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 10px;
                            font-size: 12pt;
                        }
                        
                        .grand-total {
                            font-size: 14pt;
                            font-weight: bold;
                            color: #e74c3c;
                            margin-top: 10px;
                            padding-top: 10px;
                            border-top: 1px solid #ddd;
                        }
                        
                        .footer {
                            margin-top: 50px;
                            text-align: center;
                            font-size: 10pt;
                            color: #7f8c8d;
                            border-top: 1px solid #ddd;
                            padding-top: 20px;
                        }
                        
                        .no-print {
                            display: none !important;
                        }
                        
                        /* Хэвлэхэд зориулсан нэмэлт */
                        .print-only {
                            display: block !important;
                        }
                        
                        .screen-only {
                            display: none !important;
                        }
                        
                        /* Break page avoid */
                        .break-avoid {
                            page-break-inside: avoid;
                        }
                    }
                    
                    /* Screen styles (хэвлэхгүй үед) */
                    @media screen {
                        .invoice-container {
                            max-width: 800px;
                            margin: 20px auto;
                            padding: 20px;
                            background: white;
                            box-shadow: 0 0 20px rgba(0,0,0,0.1);
                        }
                        
                        .print-only {
                            display: none;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="invoice-container">
                    ${generateSimplePrintableInvoice()}
                </div>
                <script>
                    // Автоматаар хэвлэх
                    window.onload = function() {
                        window.print();
                        // Хэвлэсний дараа хаах (сарны 1 секундын дараа)
                        setTimeout(function() {
                            window.close();
                        }, 1000);
                    };
                    
                    // Хэрэглэгч хэвлэх цонхыг хаасан эсэхийг шалгах
                    window.onbeforeunload = function() {
                        return 'Нэхэмжлэл хэвлэх цонхыг хаах уу?';
                    };
                <\/script>
            </body>
            </html>
        `;
        
        printWindow.document.write(invoiceHTML);
        printWindow.document.close();
    });

    // Хялбар нэхэмжлэл үүсгэх
    function generateSimplePrintableInvoice() {
        // Note: viewTotal shows subtotal (without tax), viewSubtotal shows total (with tax)
        var invoiceData = {
            number: $('#viewInvoiceNumber').text(),
            customer: $('#viewCustomerName').text(),
            email: $('#viewCustomerEmail').text(),
            phone: $('#viewCustomerPhone').text(),
            date: $('#viewInvoiceDate').text(), // Энд аль хэдийн форматлагдсан байна
            dueDate: $('#viewDueDate').text(),  // Энд аль хэдийн форматлагдсан байна
            subtotal: $('#viewTotal').text(), // Subtotal without tax
            tax: $('#viewTax').text(),
            total: $('#viewSubtotal').text(), // Total with tax
            status: $('#viewStatus').text().trim(),
            notes: $('#viewNotes').text(),
            issuer: {
                name: $('#viewIssuerName').text(),
                register: $('#viewIssuerRegister').text(),
                email: $('#viewIssuerEmail').text(),
                phone: $('#viewIssuerPhone').text(),
                address: $('#viewIssuerAddress').text(),
                bank_name: $('#viewIssuerBankName').text(),
                bank_account: $('#viewIssuerBankAccount').text(),
                bank_iban: $('#viewIssuerBankIban').text()
            }
        };
        
        // Бараануудыг цуглуулах
        var items = [];
        $('#viewItemsTable tbody tr').each(function() {
            var row = $(this);
            items.push({
                no: row.find('td:eq(0)').text(),
                description: row.find('td:eq(1)').text(),
                quantity: row.find('td:eq(2)').text(),
                price: row.find('td:eq(3)').text(),
                total: row.find('td:eq(4)').text()
            });
        });
        
        // HTML үүсгэх
        var html = `
            <div class="invoice-header">
                <h1 class="invoice-title">НЭХЭМЖЛЭЛ</h1>
                <div class="invoice-number">Дугаар: ${invoiceData.number}</div>
                <div class="invoice-date">Огноо: ${invoiceData.date}</div>
                <div style="margin-top: 10px; font-size: 14pt;">
                    Төлөв: <strong>${invoiceData.status}</strong>
                </div>
            </div>
            
            <div class="info-row" style="margin-bottom: 30px;">
                <div class="company-info" style="width: 48%;">
                    <div class="info-title">Нэхэмжлэгч</div>
                    <div class="info-row">
                        <span class="info-label">Байгууллагын нэр:</span>
                        <span>${invoiceData.issuer.name}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Хаяг:</span>
                        <span>${invoiceData.issuer.address}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Утас:</span>
                        <span>${invoiceData.issuer.phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Имэйл:</span>
                        <span>${invoiceData.issuer.email}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Банкны данс:</span>
                        <span>${invoiceData.issuer.bank_name} - ${invoiceData.issuer.bank_account} ${invoiceData.issuer.bank_iban ? '(' + invoiceData.issuer.bank_iban + ')' : ''}</span>
                    </div>
                </div>
                
                <div class="customer-info" style="width: 48%;">
                    <div class="info-title">Төлөгч</div>
                    <div class="info-row">
                        <span class="info-label">Байгууллагын нэр:</span>
                        <span>${invoiceData.customer}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Имэйл:</span>
                        <span>${invoiceData.email}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Утас:</span>
                        <span>${invoiceData.phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Нэхэмжлэл:</span>
                        <span>${invoiceData.date}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Дуусах:</span>
                        <span>${invoiceData.dueDate}</span>
                    </div>
                </div>
            </div>
            
            <table class="break-avoid">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Бараа/Үйлчилгээ</th>
                        <th class="text-right">Тоо ширхэг</th>
                        <th class="text-right">Нэгж үнэ</th>
                        <th class="text-right">Нийт дүн</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Бараанууд нэмэх
        items.forEach(function(item) {
            html += `
                <tr>
                    <td>${item.no}</td>
                    <td>${item.description}</td>
                    <td class="text-right">${item.quantity}</td>
                    <td class="text-right">${item.price}</td>
                    <td class="text-right">${item.total}</td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
            
            <div class="total-section">
                <div class="total-row">
                    <span>Нийт дүн:</span>
                    <span>${invoiceData.subtotal}</span>
                </div>
                <div class="total-row">
                    <span>Татвар (10%):</span>
                    <span>${invoiceData.tax}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Татвартай нийт дүн:</span>
                    <span>${invoiceData.total}</span>
                </div>
            </div>
            
            ${invoiceData.notes !== '-' ? `
                <div class="break-avoid" style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <div class="info-title">ТЭМДЭГЛЭЛ</div>
                    <p>${invoiceData.notes}</p>
                </div>
            ` : ''}
            
            <div class="footer">
                <div class="print-only">
                    <p><strong>Хүндэтгэсэн,</strong></p>
                    <p>${invoiceData.issuer.name || 'Байгууллага'}</p>
                    <p>Гарын үсэг: _________________________</p>
                </div>
                <p style="margin-top: 20px;">
                    Хэвлэсэн: ${new Date().toLocaleDateString('mn-MN', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                </p>
            </div>
        `;
        
        return html;
    }

    // Delete invoice
    $(document).on('click', '.delete-invoice', function() {
        var invoiceId = $(this).data('id');
        var invoiceNumber = $(this).data('number');
        
        Swal.fire({
            title: 'Та итгэлтэй байна уу?',
            text: "Нэхэмжлэл дугаар: " + invoiceNumber + " устгах уу?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Тийм, устга!',
            cancelButtonText: 'Цуцлах'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/delete/' + invoiceId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Амжилттай!',
                                'Нэхэмжлэл амжилттай устгагдлаа.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Алдаа!',
                            'Нэхэмжлэл устгахад алдаа гарлаа.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Set up resolve invoice modal
    $(document).on('click', '.resolve-invoice', function() {
        var invoiceId = $(this).data('id');
        var invoiceNumber = $(this).data('number');
        
        $('#resolve_invoice_id').val(invoiceId);
        $('#resolveInvoiceModalLabel').text('Төлбөр тооцоо - ' + invoiceNumber);
        
        // Set today's date
        const today = new Date().toISOString().split('T')[0];
        $('#payment_date').val(today);
    });

    // Auto calculate days remaining
    function updateDaysRemaining() {
        $('.days-remaining').each(function() {
            var dueDate = new Date($(this).data('due-date'));
            var today = new Date();
            var diffTime = dueDate - today;
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if(diffDays < 0) {
                $(this).html('<span class="text-danger">' + Math.abs(diffDays) + ' хоног хэтэрсэн</span>');
            } else if(diffDays == 0) {
                $(this).html('<span class="text-warning">Өнөөдөр</span>');
            } else {
                $(this).html('<span class="text-success">' + diffDays + ' хоног үлдсэн</span>');
            }
        });
    }
    
    // Initialize days remaining calculation
    updateDaysRemaining();

    // Show QR Code
    $(document).on('click', '.show-qr', function() {
        var pdfUrl = $(this).data('pdf-url');
        var invoiceId = $(this).data('id');
        var invoiceUuid = $(this).data('uuid');
        
        // Use proxy route for PDF viewing with UUID
        var proxyPdfUrl = '/invoice/pdf/' + invoiceUuid;
        var fullProxyUrl = window.location.origin + proxyPdfUrl;
        
        // Set PDF link to use proxy route
        $('#pdfLink').attr('href', proxyPdfUrl);
        
        // Set PDF iframe source
        $('#pdfViewer').attr('src', proxyPdfUrl);
        
        // Reset tabs to QR code tab
        $('#qr-tab').tab('show');
        
        // Generate QR code using backend with proxy URL
        $('#qrCodeContainer').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
        
        $.ajax({
            url: '/invoice/qr-code',
            type: 'GET',
            data: { url: fullProxyUrl },
            success: function(response) {
                if(response.success) {
                    // Handle SVG format QR code
                    if(response.qr_code.startsWith('data:image/svg+xml')) {
                        // For SVG, we can embed it directly or use as img src
                        $('#qrCodeContainer').html('<img src="' + response.qr_code + '" alt="QR Code" style="max-width: 100%; height: auto;">');
                    } else {
                        // For other formats (PNG, etc.)
                        $('#qrCodeContainer').html('<img src="' + response.qr_code + '" alt="QR Code" style="max-width: 100%; height: auto;">');
                    }
                } else {
                    $('#qrCodeContainer').html('<p class="text-danger">QR код үүсгэхэд алдаа гарлаа</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('QR code error:', error);
                $('#qrCodeContainer').html('<p class="text-danger">QR код үүсгэхэд алдаа гарлаа</p>');
            }
        });
    });
});
</script>
@endsection
