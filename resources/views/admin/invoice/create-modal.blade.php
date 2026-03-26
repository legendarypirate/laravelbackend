<div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createInvoiceModalLabel">Шинэ нэхэмжлэл үүсгэх</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::open(['id' => 'createInvoiceForm', 'method' => 'post']) !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="issuer_profile_id">Нэхэмжлэгчийн профайл</label>
              <select class="form-control" id="issuer_profile_id" name="issuer_profile_id">
                <option value="">Сонгоно уу</option>
                @foreach($profiles as $profile)
                  <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                @endforeach
              </select>
              <small class="form-text text-muted">
                Профайл тохиргоо: <a href="{{ url('/invoice/profile') }}" target="_blank">/invoice/profile</a>
              </small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="issuer_bank_account_id">Банкны данс</label>
              <select class="form-control" id="issuer_bank_account_id" name="issuer_bank_account_id" disabled>
                <option value="">Эхлээд профайл сонгоно уу</option>
              </select>
            </div>
          </div>
        </div>

        <div class="card card-light mb-3" id="issuerProfileDetails" style="display:none;">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Байгууллага:</strong> <span id="issuer_name">-</span></p>
                <p class="mb-1"><strong>Регистр:</strong> <span id="issuer_register">-</span></p>
                <p class="mb-1"><strong>Имэйл:</strong> <span id="issuer_email">-</span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Утас:</strong> <span id="issuer_phone">-</span></p>
                <p class="mb-1"><strong>Хаяг:</strong> <span id="issuer_address">-</span></p>
                <p class="mb-1"><strong>Банк:</strong> <span id="issuer_bank_name">-</span></p>
                <p class="mb-0"><strong>Данс:</strong> <span id="issuer_bank_account">-</span> <small class="text-muted" id="issuer_bank_iban"></small></p>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="invoice_number">Нэхэмжлэлийн дугаар *</label>
              <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="invoice_date">Нэхэмжлэлийн огноо *</label>
              <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="customer_name">Харилцагчийн нэр *</label>
              <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="customer_email">Имэйл</label>
              <input type="email" class="form-control" id="customer_email" name="customer_email">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="customer_phone">Утасны дугаар</label>
              <input type="text" class="form-control" id="customer_phone" name="customer_phone">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="due_date">Төлбөрийн эцсийн огноо *</label>
              <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>
          </div>
        </div>

        <hr>
        <h5>Барааны мэдээлэл</h5>
        <div id="itemsContainer">
          <div class="item-row row mb-2">
            <div class="col-md-5">
              <input type="text" class="form-control item-description" name="items[0][description]" placeholder="Барааны нэр" required>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control item-quantity" name="items[0][quantity]" placeholder="Тоо" min="1" required>
            </div>
            <div class="col-md-3">
              <input type="number" class="form-control item-price" name="items[0][price]" placeholder="Үнэ" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        </div>
        
        <button type="button" id="addItem" class="btn btn-secondary btn-sm mb-3">
          <i class="fas fa-plus"></i> Бараа нэмэх
        </button>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="subtotal">Нийт дүн</label>
              <input type="number" class="form-control" id="subtotal" name="subtotal" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="tax">Татвар (%)</label>
              <input type="number" class="form-control" id="tax_percent" name="tax_percent" min="0" value="10">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="tax">Татварын дүн</label>
              <input type="number" class="form-control" id="tax" name="tax" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="total">Татвартай нийт дүн</label>
              <input type="number" class="form-control" id="total" name="total" readonly>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="notes">Тэмдэглэл</label>
          <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Цуцлах</button>
        <button type="submit" class="btn btn-primary">Хадгалах</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    let itemCount = 1;
    const invoiceProfiles = @json($profiles);
    
    // Add item row
    $('#addItem').click(function() {
        const html = `
        <div class="item-row row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control item-description" name="items[${itemCount}][description]" placeholder="Барааны нэр" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]" placeholder="Тоо" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control item-price" name="items[${itemCount}][price]" placeholder="Үнэ" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
        $('#itemsContainer').append(html);
        itemCount++;
    });
    
    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
        }
    });

    function setProfilePreview(profile, bank) {
        if (!profile) {
            $('#issuerProfileDetails').hide();
            $('#issuer_bank_account_id').prop('disabled', true).html('<option value="">Эхлээд профайл сонгоно уу</option>');
            return;
        }

        $('#issuerProfileDetails').show();
        $('#issuer_name').text(profile.name || '-');
        $('#issuer_register').text(profile.register_number || '-');
        $('#issuer_email').text(profile.email || '-');
        $('#issuer_phone').text(profile.phone || '-');
        $('#issuer_address').text(profile.address || '-');

        if (bank) {
            $('#issuer_bank_name').text(bank.bank_name || '-');
            const accountText = [bank.account_number, bank.account_name].filter(Boolean).join(' | ');
            $('#issuer_bank_account').text(accountText || '-');
            $('#issuer_bank_iban').text(bank.iban ? `(IBAN: ${bank.iban})` : '');
        } else {
            $('#issuer_bank_name').text('Данс бүртгээгүй');
            $('#issuer_bank_account').text('-');
            $('#issuer_bank_iban').text('');
        }
    }

    function populateBanks(profile) {
        const bankSelect = $('#issuer_bank_account_id');
        bankSelect.empty();

        if (!profile || !Array.isArray(profile.bank_accounts) || !profile.bank_accounts.length) {
            bankSelect.prop('disabled', true).append('<option value="">Данс бүртгээгүй</option>');
            setProfilePreview(profile, null);
            return;
        }

        bankSelect.prop('disabled', false);
        let selectedBankId = null;
        profile.bank_accounts.forEach((bank, index) => {
            const selectedAttr = bank.is_primary || (!selectedBankId && index === 0) ? 'selected' : '';
            if (selectedAttr) {
                selectedBankId = bank.id;
            }
            bankSelect.append(`<option value="${bank.id}" ${selectedAttr}>${bank.bank_name} - ${bank.account_number}</option>`);
        });

        const chosen = profile.bank_accounts.find(b => b.id === selectedBankId) || profile.bank_accounts[0];
        setProfilePreview(profile, chosen);
    }

    $('#issuer_profile_id').on('change', function() {
        const profileId = $(this).val();
        const profile = invoiceProfiles.find(p => p.id == profileId);
        populateBanks(profile);
    });

    $('#issuer_bank_account_id').on('change', function() {
        const profileId = $('#issuer_profile_id').val();
        const profile = invoiceProfiles.find(p => p.id == profileId);
        const bank = profile ? profile.bank_accounts.find(b => b.id == $(this).val()) : null;
        setProfilePreview(profile, bank);
    });
    
    // Calculate totals
    $(document).on('input', '.item-quantity, .item-price', function() {
        calculateTotals();
    });
    
    $('#tax_percent').on('input', function() {
        calculateTotals();
    });
    
    function calculateTotals() {
        let subtotal = 0;
        const taxPercent = parseFloat($('#tax_percent').val()) || 0;
        const taxMultiplier = 1 + (taxPercent / 100); // e.g., 1.1 for 10% VAT
        
        // Treat entered price as unit price (with VAT), multiply by quantity to get line total
        $('.item-row').each(function() {
            const unitPriceWithVat = parseFloat($(this).find('.item-price').val()) || 0;
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            // Calculate line total with VAT: unitPriceWithVat * quantity
            const lineTotalWithVat = unitPriceWithVat * quantity;
            // Calculate subtotal for this line: lineTotalWithVat / (1 + taxPercent/100)
            subtotal += lineTotalWithVat / taxMultiplier;
        });
        
        const tax = subtotal * (taxPercent / 100);
        const total = subtotal + tax;
        
        $('#subtotal').val(subtotal.toFixed(2));
        $('#tax').val(tax.toFixed(2));
        $('#total').val(total.toFixed(2));
    }
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    const nextWeekStr = nextWeek.toISOString().split('T')[0];
    
    $('#invoice_date').val(today);
    $('#due_date').val(nextWeekStr);
    
    const defaultProfile = invoiceProfiles.find(p => p.is_default) || invoiceProfiles[0];
    if (defaultProfile) {
        $('#issuer_profile_id').val(defaultProfile.id).trigger('change');
    }
    
    // Generate invoice number
    function generateInvoiceNumber() {
        const prefix = 'INV-';
        const date = new Date();
        const year = date.getFullYear().toString().slice(-2);
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return `${prefix}${year}${month}${random}`;
    }
    
    $('#invoice_number').val(generateInvoiceNumber());
    
    // Form submission
    $('#createInvoiceForm').submit(function(e) {
        e.preventDefault();
        
        // Convert unit prices to line totals (unit price * quantity) before submission
        let itemIndex = 0;
        $('.item-row').each(function() {
            const unitPrice = parseFloat($(this).find('.item-price').val()) || 0;
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            // Calculate line total with VAT: unit price * quantity
            const lineTotalWithVat = unitPrice * quantity;
            // Update the price field value directly in the form
            $(this).find('.item-price').val(lineTotalWithVat);
            itemIndex++;
        });
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/invoice/create',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Хадгалж байна...');
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Амжилттай!',
                        text: 'Нэхэмжлэл амжилттай үүслээ',
                    });
                    $('#createInvoiceModal').modal('hide');
                    // Always refresh so the new invoice shows up immediately
                    window.location.reload();
                    $('#createInvoiceForm')[0].reset();
                    $('#invoice_number').val(generateInvoiceNumber());
                } else {
                    let errorMessage = 'Алдаа гарлаа';
                    if(response.errors) {
                        errorMessage = '';
                        $.each(response.errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });
                    }
                    Swal.fire('Алдаа!', errorMessage, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Алдаа!', 'Алдаа гарлаа. Дахин оролдоно уу.', 'error');
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Хадгалах');
            }
        });
    });
});
</script>