<div class="modal fade" id="viewInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewInvoiceModalLabel">
          <i class="fas fa-file-invoice mr-2"></i>
          Нэхэмжлэлийн дэлгэрэнгүй: <span id="viewInvoiceNumber"></span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Харилцагчийн мэдээлэл</h6>
              </div>
              <div class="card-body">
                <p><strong>Нэр:</strong> <span id="viewCustomerName"></span></p>
                <p><strong>Имэйл:</strong> <span id="viewCustomerEmail"></span></p>
                <p><strong>Утас:</strong> <span id="viewCustomerPhone"></span></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Нэхэмжлэлийн мэдээлэл</h6>
              </div>
              <div class="card-body">
                <p><strong>Огноо:</strong> <span id="viewInvoiceDate"></span></p>
                <p><strong>Дуусах огноо:</strong> <span id="viewDueDate"></span></p>
                <p><strong>Төлөв:</strong> <span id="viewStatus"></span></p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-warehouse mr-2"></i>Нэхэмжлэгч</h6>
              </div>
              <div class="card-body">
                <p><strong>Байгууллага:</strong> <span id="viewIssuerName">-</span></p>
                <p><strong>Регистр:</strong> <span id="viewIssuerRegister">-</span></p>
                <p><strong>Имэйл:</strong> <span id="viewIssuerEmail">-</span></p>
                <p><strong>Утас:</strong> <span id="viewIssuerPhone">-</span></p>
                <p class="mb-0"><strong>Хаяг:</strong> <span id="viewIssuerAddress">-</span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-university mr-2"></i>Банкны мэдээлэл</h6>
              </div>
              <div class="card-body">
                <p class="mb-1"><strong>Банк:</strong> <span id="viewIssuerBankName">-</span></p>
                <p class="mb-1"><strong>Данс:</strong> <span id="viewIssuerBankAccount">-</span></p>
                <p class="mb-0"><strong>IBAN:</strong> <span id="viewIssuerBankIban">-</span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Бараануудын хүснэгт -->
        <div class="card mb-3">
          <div class="card-header bg-secondary text-white">
            <h6 class="mb-0"><i class="fas fa-boxes mr-2"></i>Бараанууд</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered mb-0" id="viewItemsTable">
                <thead class="thead-light">
                  <tr>
                    <th width="5%">#</th>
                    <th width="45%">Барааны нэр</th>
                    <th width="15%" class="text-right">Тоо</th>
                    <th width="15%" class="text-right">Нэгж үнэ</th>
                    <th width="20%" class="text-right">Нийт</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- AJAX-ээр утгууд бөглөгдөнө -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4" class="text-right"><strong>Нийт дүн:</strong></td>
                    <td class="text-right"><span id="viewTotal"></span></td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-right"><strong>Татвар:</strong></td>
                    <td class="text-right"><span id="viewTax"></span></td>
                  </tr>
                  <tr class="table-active">
                    <td colspan="4" class="text-right"><strong>Татвартай нийт:</strong></td>
                    <td class="text-right"><span id="viewSubtotal"></span></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- Төлбөрийн мэдээлэл -->
        <div class="card mb-3" id="paymentInfo" style="display: none;">
          <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fas fa-credit-card mr-2"></i>Төлбөрийн мэдээлэл</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <p><strong>Төлбөрийн арга:</strong> <span id="viewPaymentMethod"></span></p>
              </div>
              <div class="col-md-4">
                <p><strong>Төлбөрийн огноо:</strong> <span id="viewPaymentDate"></span></p>
              </div>
              <div class="col-md-4">
                <p><strong>Төлсөн огноо:</strong> <span id="viewPaidAt"></span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Тэмдэглэл -->
        <div class="card">
          <div class="card-header bg-warning text-white">
            <h6 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Тэмдэглэл</h6>
          </div>
          <div class="card-body">
            <p id="viewNotes">-</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i> Хаах
        </button>
        <button type="button" class="btn btn-primary" id="printInvoice">
          <i class="fas fa-print mr-1"></i> Хэвлэх
        </button>
      </div>
    </div>
  </div>
</div>

    <script>
    // Хэвлэх товч
    // Хялбар хэвлэх функц
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
                        
                        .signature-section {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 20px;
                            margin-bottom: 20px;
                        }
                        
                        .signature-image {
                            max-width: 5cm;
                            max-height: 100px;
                            object-fit: contain;
                        }
                        
                        .signature-text {
                            text-align: left;
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

    // Тоог үсгээр хувиргах (МН)
    function numberToMongolianWords(value) {
        if (value === undefined || value === null) return '';

        // Валютын тэмдэг, таслал зэргийг цэвэрлэх
        var sanitized = value.toString().replace(/[^0-9.,-]/g, '').replace(/,/g, '');
        if (!sanitized) return '';

        var num = parseFloat(sanitized);
        if (isNaN(num)) return '';

        var negative = num < 0;
        num = Math.abs(num);

        var ones = ['нэг', 'хоёр', 'гурав', 'дөрөв', 'тав', 'зургаа', 'долоо', 'найм', 'ес'];
        var attributiveOnes = ['нэг', 'хоёр', 'гурван', 'дөрвөн', 'таван', 'зургаан', 'долоон', 'найман', 'есөн'];
        var tensWords = [null, 'арав', 'хорин', 'гучин', 'дөчин', 'тавин', 'жаран', 'далан', 'наян', 'ерэн'];
        var scales = ['', 'мянга', 'сая', 'тэрбум', 'их наяд'];

        function twoDigit(n, useAttributive) {
            if (n === 0) return '';
            if (n < 10) return (useAttributive ? attributiveOnes : ones)[n - 1];
            if (n === 10) return tensWords[1];
            if (n < 20) return 'арван ' + (useAttributive ? attributiveOnes : ones)[n - 10 - 1];

            var tens = Math.floor(n / 10);
            var units = n % 10;
            var unitsWord = units ? (useAttributive ? attributiveOnes : ones)[units - 1] : '';
            return tensWords[tens] + (units ? ' ' + unitsWord : '');
        }

        function chunkToWords(n, useAttributiveForEnd) {
            var words = [];
            var hundred = Math.floor(n / 100);
            var remainder = n % 100;

            if (hundred > 0) {
                words.push(attributiveOnes[hundred - 1] + ' зуун');
            }
            if (remainder > 0) {
                words.push(twoDigit(remainder, useAttributiveForEnd));
            }

            return words.join(' ');
        }

        var integerPart = Math.floor(num);
        var decimalPart = Math.round((num - integerPart) * 100);

        var parts = [];
        var chunkIndex = 0;

        if (integerPart === 0) {
            parts.push('тэг');
        }

        while (integerPart > 0) {
            var chunk = integerPart % 1000;
            if (chunk > 0) {
                var chunkWords = chunkToWords(chunk, chunkIndex > 0);
                var scale = scales[chunkIndex] ? ' ' + scales[chunkIndex] : '';
                parts.unshift(chunkWords + scale);
            }
            integerPart = Math.floor(integerPart / 1000);
            chunkIndex++;
        }

        var wordsResult = (negative ? 'сөрөг ' : '') + parts.join(' ');
        var result = wordsResult + ' төгрөг';

        if (decimalPart > 0) {
            result += ' ' + twoDigit(decimalPart, true) + ' мөнгө';
        }

        return result.trim();
    }

    // Хялбар нэхэмжлэл үүсгэх
    function generateSimplePrintableInvoice() {
        // Note: viewTotal shows subtotal (without tax), viewSubtotal shows total (with tax)
        var invoiceData = {
            number: $('#viewInvoiceNumber').text(),
            customer: $('#viewCustomerName').text(),
            email: $('#viewCustomerEmail').text(),
            phone: $('#viewCustomerPhone').text(),
            date: $('#viewInvoiceDate').text(),
            dueDate: $('#viewDueDate').text(),
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

        var totalInWords = numberToMongolianWords(invoiceData.total) || invoiceData.total;
        
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
                    <span>НӨАТ (10%):</span>
                    <span>${invoiceData.tax}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Татвартай нийт дүн:</span>
                    <span>${invoiceData.total}</span>
                </div>
                <div class="total-row">
                    <span>Дүн (үсгээр):</span>
                    <span>${totalInWords}</span>
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
                    <div class="signature-section">
                        <img src="/tamga.jpeg" alt="Tamga" class="signature-image">
                        <div class="signature-text">
                           
                            <p>Дарга............................... /......................./</p>
                            <p>Хүлээн авсан............................... /......................./</p>
                            <p>Нягтлан бодогч............................... /......................./</p>
                        </div>
                    </div>
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
    </script>