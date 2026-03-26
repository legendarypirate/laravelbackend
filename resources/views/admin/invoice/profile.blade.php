@extends('admin.master')

@section('mainContent')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Нэхэмжлэгчийн профайл</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Нүүр</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/invoice/index') }}">Нэхэмжлэх</a></li>
                        <li class="breadcrumb-item active">Профайл</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">Өөрсдийн компаниуд</h3>
                        <p class="text-muted mb-0">Нэхэмжлэх дээр автоматаар ашиглах бүртгэл</p>
                    </div>
                    <button class="btn btn-primary" id="newProfileBtn">
                        <i class="fas fa-plus mr-1"></i> Профайл нэмэх
                    </button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="22%">Профайл</th>
                                <th width="20%">Холбоо барих</th>
                                <th>Банкны данснууд</th>
                                <th width="10%">Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profiles as $profile)
                                <tr data-profile-id="{{ $profile->id }}">
                                    <td>
                                        <strong>{{ $profile->name }}</strong><br>
                                        <small class="text-muted">Регистр: {{ $profile->register_number ?? '-' }}</small><br>
                                        <small class="text-muted">Хаяг: {{ $profile->address ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-phone mr-1 text-muted"></i>{{ $profile->phone ?? '-' }}</div>
                                        <div><i class="fas fa-envelope mr-1 text-muted"></i>{{ $profile->email ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="bank-list">
                                            @forelse($profile->bankAccounts as $bank)
                                                <div class="border rounded p-2 mb-2 bank-row" data-bank-id="{{ $bank->id }}">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ $bank->bank_name }}</strong>
                                                            @if($bank->is_primary)
                                                                <span class="badge badge-success ml-2">Анхдагч</span>
                                                            @endif
                                                            <div class="text-muted small">
                                                                {{ $bank->account_number }}
                                                                @if($bank->account_name)
                                                                    | {{ $bank->account_name }}
                                                                @endif
                                                            </div>
                                                            @if($bank->iban)
                                                                <div class="text-muted small">IBAN: {{ $bank->iban }}</div>
                                                            @endif
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-danger delete-bank" data-profile-id="{{ $profile->id }}" data-bank-id="{{ $bank->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted mb-2">Данс нэмээгүй байна</div>
                                            @endforelse
                                        </div>
                                        <form class="add-bank-form mt-2" data-profile-id="{{ $profile->id }}">
                                            <div class="form-row">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" name="bank_name" placeholder="Банк" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" name="account_number" placeholder="Дансны дугаар" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" name="account_name" placeholder="Дансны нэр">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" name="iban" placeholder="IBAN">
                                                </div>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input type="checkbox" class="form-check-input" name="is_primary" value="1" id="primary-{{ $profile->id }}">
                                                <label class="form-check-label" for="primary-{{ $profile->id }}">Анхдагч данс болгох</label>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-plus mr-1"></i> Данс нэмэх
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-sm btn-outline-info edit-profile" data-profile='@json($profile)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-profile" data-profile-id="{{ $profile->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Профайл бүртгээгүй байна</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Profile modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Профайл нэмэх</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="profileForm">
                <div class="modal-body">
                    <input type="hidden" id="profile_id" name="profile_id">
                    <div class="form-group">
                        <label>Байгууллагын нэр *</label>
                        <input type="text" class="form-control" id="profile_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Регистрийн дугаар</label>
                        <input type="text" class="form-control" id="profile_register" name="register_number">
                    </div>
                    <div class="form-group">
                        <label>Имэйл</label>
                        <input type="email" class="form-control" id="profile_email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Утас</label>
                        <input type="text" class="form-control" id="profile_phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Хаяг</label>
                        <textarea class="form-control" id="profile_address" name="address" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="profile_is_default" name="is_default" value="1">
                        <label class="form-check-label" for="profile_is_default">Анхдагч профайл болгох</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Цуцлах</button>
                    <button type="submit" class="btn btn-primary">Хадгалах</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#newProfileBtn').on('click', function() {
        resetProfileForm();
        $('#profileModalLabel').text('Профайл нэмэх');
        $('#profileModal').modal('show');
    });

    $(document).on('click', '.edit-profile', function() {
        const profile = $(this).data('profile');
        resetProfileForm();
        $('#profileModalLabel').text('Профайл засах');
        $('#profile_id').val(profile.id);
        $('#profile_name').val(profile.name);
        $('#profile_register').val(profile.register_number);
        $('#profile_email').val(profile.email);
        $('#profile_phone').val(profile.phone);
        $('#profile_address').val(profile.address);
        $('#profile_is_default').prop('checked', !!profile.is_default);
        $('#profileModal').modal('show');
    });

    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const profileId = $('#profile_id').val();
        const url = profileId ? `/invoice/profile/${profileId}` : '/invoice/profile';

        $.ajax({
            url,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: $(this).serialize(),
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Алдаа', xhr.responseJSON?.message || 'Хадгалахад алдаа гарлаа', 'error');
            }
        });
    });

    $(document).on('click', '.delete-profile', function() {
        const profileId = $(this).data('profile-id');
        Swal.fire({
            title: 'Устгах уу?',
            text: 'Энэ профайл болон дагалдах дансууд устахыг анхаарна уу.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Тийм',
            cancelButtonText: 'Болих'
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/invoice/profile/${profileId}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('Алдаа', xhr.responseJSON?.message || 'Устгахад алдаа гарлаа', 'error');
                }
            });
        });
    });

    $(document).on('submit', '.add-bank-form', function(e) {
        e.preventDefault();
        const profileId = $(this).data('profile-id');
        const form = $(this);
        $.ajax({
            url: `/invoice/profile/${profileId}/bank`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: form.serialize(),
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Алдаа', xhr.responseJSON?.message || 'Данс нэмэхэд алдаа гарлаа', 'error');
            }
        });
    });

    $(document).on('click', '.delete-bank', function() {
        const profileId = $(this).data('profile-id');
        const bankId = $(this).data('bank-id');
        Swal.fire({
            title: 'Данс устгах уу?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Тийм',
            cancelButtonText: 'Болих'
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/invoice/profile/${profileId}/bank/${bankId}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('Алдаа', xhr.responseJSON?.message || 'Данс устгахад алдаа гарлаа', 'error');
                }
            });
        });
    });

    function resetProfileForm() {
        $('#profileForm')[0].reset();
        $('#profile_id').val('');
    }
});
</script>
@endsection

