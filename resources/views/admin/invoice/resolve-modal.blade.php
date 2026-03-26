<div class="modal fade" id="resolveInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="resolveInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resolveInvoiceModalLabel">Төлбөрийн статус шинэчлэх</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::open(['id' => 'resolveInvoiceForm']) !!}
      <div class="modal-body">
        <input type="hidden" id="resolve_invoice_id" name="invoice_id">
        
        <div class="form-group">
          <label for="resolve_status">Төлбөрийн статус *</label>
          <select class="form-control" id="resolve_status" name="status" required>
            <option value="paid">Төлөгдсөн</option>
            <option value="pending">Хүлээгдэж буй</option>
            <option value="overdue">Хугацаа хэтэрсэн</option>
            <option value="cancelled">Цуцлагдсан</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="payment_method">Төлбөрийн арга</label>
          <select class="form-control" id="payment_method" name="payment_method">
            <option value="">Сонгох</option>
            <option value="cash">Бэлнээр</option>
            <option value="bank_transfer">Банкны шилжүүлэг</option>
            <option value="card">Картаар</option>
            <option value="mobile">Мобайл банк</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="payment_date">Төлбөрийн огноо</label>
          <input type="date" class="form-control" id="payment_date" name="payment_date">
        </div>
        
        <div class="form-group">
          <label for="resolve_notes">Тэмдэглэл</label>
          <textarea class="form-control" id="resolve_notes" name="notes" rows="3" placeholder="Нэмэлт тэмдэглэл..."></textarea>
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
    // Set today's date as default payment date
    const today = new Date().toISOString().split('T')[0];
    $('#payment_date').val(today);
    
    // Form submission
    $('#resolveInvoiceForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/invoice/resolve',
            type: 'POST',
            data: $(this).serialize(),
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
                        text: 'Статус амжилттай шинэчлэгдлээ',
                    });
                    $('#resolveInvoiceModal').modal('hide');
                    $('#viewInvoiceModal').modal('hide');
                    // Force refresh so the updated status is visible right away
                    window.location.reload();
                } else {
                    Swal.fire('Алдаа!', response.message || 'Алдаа гарлаа', 'error');
                }
            },
            error: function() {
                Swal.fire('Алдаа!', 'Алдаа гарлаа. Дахин оролдоно уу.', 'error');
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Хадгалах');
            }
        });
    });
    
    // Open resolve modal from view modal
    $('#resolveBtn').click(function() {
        const invoiceId = $('#viewInvoiceModal').data('invoice-id');
        $('#resolve_invoice_id').val(invoiceId);
        $('#resolveInvoiceModal').modal('show');
    });
});
</script>