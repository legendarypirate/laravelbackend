@extends('admin.master')

@section('mainContent')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Хэрэглэгчийн удирдлага</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Нүүр</a></li>
              <li class="breadcrumb-item active">Хэрэглэгчид</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Хэрэглэгчийн жагсаалт</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Нэр</th>
                                        <th>Имэйл</th>
                                        <th>Роль</th>
                                        <th>Идэвхтэй</th>
                                        <th>Бүртгэсэн огноо</th>
                                        <th>Үйлдэл</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="{{ $user->active == 0 ? 'text-muted bg-light' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'manager' ? 'warning' : 'info') }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input toggle-status"
                                                       id="switch{{ $user->id }}"
                                                       data-id="{{ $user->id }}"
                                                       {{ $user->active == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="switch{{ $user->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                              
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            {{-- Хэрэв pagination ашиглавал --}}
                            {{-- {{ $users->links() }} --}}
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<script>
$(document).ready(function() {
    // Toggle status switch
    $('.toggle-status').change(function() {
        var userId = $(this).data('id');
        var isActive = $(this).is(':checked') ? 1 : 0;
        var $row = $(this).closest('tr');
        var $switch = $(this); // Store reference to the switch
        
        console.log('Toggling user ID:', userId, 'to status:', isActive);
        
        // AJAX request to update status
        $.ajax({
            url: "{{ url('user/toggle-status') }}/" + userId + "",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                active: isActive
            },
            dataType: 'json',
            beforeSend: function() {
                // Show loading indicator
                $row.addClass('processing');
                $switch.prop('disabled', true);
            },
            success: function(response) {
                console.log('Response:', response);
                
                if (response.success) {
                    // Update row appearance based on status
                    if (isActive == 1) {
                        $row.removeClass('text-muted bg-light');
                    } else {
                        $row.addClass('text-muted bg-light');
                    }
                    
                    // Show success message
                    showToast('Амжилттай', response.message, 'success');
                } else {
                    // Revert checkbox if failed
                    $switch.prop('checked', !isActive);
                    showToast('Алдаа', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('XHR:', xhr);
                
                // Revert checkbox on error
                $switch.prop('checked', !isActive);
                showToast('Алдаа', 'Серверийн алдаа гарлаа: ' + error, 'error');
            },
            complete: function() {
                $row.removeClass('processing');
                $switch.prop('disabled', false);
            }
        });
    });

    // Toast notification function
    function showToast(title, message, type) {
        // Create toast container if it doesn't exist
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container"></div>');
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = $(`
            <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
                <div class="toast-header ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white">
                    <strong class="mr-auto">${title}</strong>
                    <small>саяхан</small>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);
        
        $('.toast-container').append(toast);
        
        // Initialize and show toast
        toast.toast({
            animation: true,
            autohide: true,
            delay: 5000
        }).toast('show');
        
        // Remove toast after hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
});
</script>

<style>
    .text-muted.bg-light {
        opacity: 0.7;
    }
    
    .processing {
        position: relative;
        pointer-events: none;
    }
    
    .processing:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.7);
        z-index: 1;
    }
    
    /* Toast container */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
    }
    
    /* Custom switch colors */
    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #28a745;
        background-color: #28a745;
    }
    
    .custom-switch .custom-control-input:focus ~ .custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>

<div class="toast-container"></div>
@endsection

@push('scripts')

@endpush
