@extends('admin.master')
@section('mainContent')
    <style>
        .image-container {
            position: relative;
            display: inline-block;
            margin-left: 35%;
        }

        .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #fff;
            padding: 5px;
            border-radius: 50%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            margin-right: 10px;
        }

        .fa-pencil-alt {
            font-size: 24px;
            color: white;
        }
        
        /* New styles for stamp section */
        .stamp-container {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .stamp-image {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background-color: #f9f9f9;
        }
        
        .stamp-placeholder {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #777;
            font-size: 14px;
        }
        
        .stamp-actions {
            margin-top: 10px;
        }
        
        .stamp-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #fff;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Хувийн мэдээлэл</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <!-- Profile Image Section -->
                                <div class="text-center image-container">
                                    @if (Auth::user()->image)
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="{{ asset('storage/' . Auth::user()->image) }}"
                                            alt="User profile picture" />
                                    @else
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="{{ asset('dist') }}/user.png" alt="User profile picture">
                                    @endif
                                    <div class="camera-icon" data-toggle="modal" data-target="#imageModal">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>

                                <!-- Organization Stamp Section -->
                                <div class="stamp-container">
                                    <h5>Байгууллагын тамга</h5>
                                    <div class="image-container" style="margin-left: 0;">
                                        @if (Auth::user()->stamp)
                                            <img class="stamp-image"
                                                src="{{ asset('storage/' . Auth::user()->stamp) }}"
                                                alt="Organization stamp" />
                                        @else
                                            <div class="stamp-placeholder">
                                                Тамга байхгүй байна
                                            </div>
                                        @endif
                                        <div class="stamp-icon" data-toggle="modal" data-target="#stampModal">
                                            <i class="fas fa-stamp"></i>
                                        </div>
                                    </div>
                                    <div class="stamp-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#stampModal">
                                            <i class="fas fa-edit"></i> Тамга солих
                                        </button>
                                    </div>
                                </div>

                                <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
                                <p class="text-muted text-center">Бизнес харилцагч</p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Гишүүнчлэл</b> <a class="float-right">Энгийн</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Оноо</b> <a class="float-right">543</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Эрхийн төрөл</b> <a class="float-right">Хязгааргүй</a>
                                    </li>
                                </ul>
                                <a href="#" class="btn btn-primary btn-block"><b>Багц сонгох</b></a>
                            </div>
                        </div>

                        <!-- Stamp Modal -->
                        <div class="modal fade" id="stampModal" tabindex="-1" role="dialog"
                            aria-labelledby="stampModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    {!! Form::open([
                                        'url' => 'user/updatestamp',
                                        'method' => 'post',
                                        'name' => 'stampForm',
                                        'role' => 'form',
                                        'files' => true,
                                        'enctype' => 'multipart/form-data',
                                    ]) !!}

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="stampModalLabel">Байгууллагын тамга солих</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="stamp">Шинэ тамга оруулах</label>
                                            <input type="file" class="form-control-file" name="stamp"
                                                accept="image/*">
                                            <small class="form-text text-muted">
                                                Зөвшөөрөгдөх формат: JPG, PNG, GIF. Хамгийн их хэмжээ: 2MB.
                                            </small>
                                        </div>
                                        
                                        @if (Auth::user()->stamp)
                                        <div class="form-group">
                                            <label>Одоогийн тамга:</label>
                                            <div class="text-center">
                                                <img class="stamp-image"
                                                    src="{{ asset('storage/' . Auth::user()->stamp) }}"
                                                    alt="Current stamp" />
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="userId" value="{{ auth()->user()->id }}">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Хаах</button>
                                        <button type="submit" class="btn btn-primary">Хадгалах</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                        <!-- Profile Image Modal -->
                        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog"
                            aria-labelledby="imageModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    {!! Form::open([
                                        'url' => 'user/updateimage',
                                        'method' => 'post',
                                        'name' => 'editForm',
                                        'role' => 'form',
                                        'files' => true,
                                        'enctype' => 'multipart/form-data',
                                    ]) !!}

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imageModalLabel">Профайлын зураг солих</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="newImage">Шинэ зураг оруулах</label>
                                            <input type="file" class="form-control-file" name="image"
                                                accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="breadId" value="{{ auth()->user()->id }}">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Хаах</button>
                                        <button type="submit" class="btn btn-primary">Хадгалах</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                        <!-- Edit Personal Info Modal -->
                        <div class="modal fade" id="editPhoneNumberModal" tabindex="-1" role="dialog"
                            aria-labelledby="editPhoneNumberModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPhoneNumberModalLabel">Хувийн мэдээлэл засах
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {!! Form::open([
                                            'url' => 'user/updateinfo',
                                            'method' => 'post',
                                            'name' => 'editForm',
                                            'role' => 'form',
                                            'files' => true,
                                            'enctype' => 'multipart/form-data',
                                        ]) !!}

                                        <div class="form-group">
                                            <label for="phoneNumber">Товч мэдээлэл</label>
                                            <input type="text" class="form-control" id="phoneNumber"
                                                value="{{ auth()->user()->about }}" name="about">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Регистрийн дугаар</label>
                                            <input type="text" class="form-control" name="register"
                                                value="{{ auth()->user()->register }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Имэйл хаяг</label>
                                            <input type="text" class="form-control" name="email"
                                                value="{{ auth()->user()->email }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Facebook</label>
                                            <input type="text" class="form-control" name="facebook"
                                                value="{{ auth()->user()->facebook }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Instagram</label>
                                            <input type="text" class="form-control" name="instagram"
                                                value="{{ auth()->user()->instagram }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Банкны нэр</label>
                                            <input type="text" class="form-control" name="bank"
                                                value="{{ auth()->user()->bank }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Дансны дугаар</label>
                                            <input type="text" class="form-control" name="account"
                                                value="{{ auth()->user()->account }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">What3words</label>
                                            <input type="text" class="form-control" name="what3words"
                                                value="{{ auth()->user()->what3words }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="address">Энгийн</label>
                                            <input type="text" class="form-control" name="engiin"
                                                value="{{ auth()->user()->engiin }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">1-3 хайрцаг</label>
                                            <input type="text" class="form-control" name="tsagtai"
                                                value="{{ auth()->user()->tsagtai }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">6-10 хайрцаг</label>
                                            <input type="text" class="form-control" name="yaraltai"
                                                value="{{ auth()->user()->yaraltai }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Бөөний хүргэлт</label>
                                            <input type="text" class="form-control" name="ontsYaraltai"
                                                value="{{ auth()->user()->onts_yaraltai }}" disabled>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Хаах</button>
                                        <button type="submit" class="btn btn-primary">Хадгалах</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                        <!-- Business Information Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Бизнесийн товч мэдээлэл</h3>
                                <i class="fas fa-pencil-alt edit-icon"></i>
                            </div>

                            <div class="card-body">
                                <strong><i class="fas fa-book mr-1"></i> Бидний тухай</strong>
                                <p class="text-muted">{{ $user['about'] }}</p>
                                <hr>
                                <strong><i class="fas fa-globe"></i> Регистрийн дугаар</strong>
                                <p class="text-muted">Регистрийн дугаар</p>

                                <hr>
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Байршил </strong>
                                <p class="text-muted">
                                    <?php $address = DB::table('addresses')
                                        ->where('userid', Auth::user()->id)
                                        ->get();
                                    foreach ($address as $addresses) {
                                        echo $addresses->address . ' ';
                                    }
                                    ?>
                                </p>
                                <hr>
                                <strong><i class="fas fa-phone"></i> Утас</strong>
                                <p class="text-muted">
                                    <?php $phone = DB::table('phones')
                                        ->where('userid', Auth::user()->id)
                                        ->get();
                                    foreach ($phone as $phones) {
                                        echo $phones->phone . ' ';
                                    }
                                    ?>
                                </p>
                                <hr>
                                <strong><i class="fas fa-envelope"></i> Имэйл</strong>
                                <p class="text-muted">{{ $user['email'] }}</p>
                                <hr>
                                <strong><i class="fab fa-facebook"></i> Фэйсбүүк</strong>
                                <p class="text-muted">{{ $user['facebook'] }}</p>

                                <hr>
                                <strong><i class="fab fa-instagram"></i> Инстаграм хаяг</strong>
                                <p class="text-muted">{{ $user['instagram'] }}</p>

                                <hr>
                                <strong><i class="fas fa-money-bill"></i> Банкны нэр</strong>
                                <p class="text-muted">{{ $user['bank'] }}</p>

                                <hr>
                                <strong><i class="fas fa-credit-card"></i> Дансны дугаар</strong>
                                <p class="text-muted">{{ $user['account'] }}</p>

                                <hr>
                                <strong><i class="fas fa-map-pin"></i> What3words</strong>
                                <p class="text-muted">{{ $user['what3words'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#list"
                                            data-toggle="tab">Салбар-мерчант</a></li>
                                    <li class="nav-item"><a class="nav-link " href="#activity"
                                            data-toggle="tab">Мэдэгдэл</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Санал
                                            хүсэлт илгээх</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content ">
                                    <!-- Create New Merchant Modal -->
                                    <div class="modal fade" id="createMerchantModal" tabindex="-1" role="dialog"
                                        aria-labelledby="createMerchantModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="createMerchantModalLabel">Шинэ салбар нэмэх
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('createMerchant') }}" method="post">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="merchantName">Салбарын нэр</label>
                                                            <input type="text" class="form-control" id="merchantName"
                                                                name="merchantName">
                                                            @error('merchantName')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="address">Салбарын хаяг</label>
                                                            <input type="text" class="form-control"
                                                                name="merchantAddress" id="address">
                                                            @error('merchantAddress')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="phone1">Салбарын утас-1</label>
                                                            <input type="text" class="form-control"
                                                                name="merchantPhone1" id="phone1">
                                                            @error('merchantPhone1')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="phone2">Салбарын утас-2</label>
                                                            <input type="text" class="form-control"
                                                                name="merchantPhone2" id="phone2">
                                                            @error('merchantPhone2')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="what3words">What3words</label>
                                                            <input type="text" class="form-control"
                                                                name="merchantWhat3Words" id="what3words">
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Хаах</button>
                                                    <button type="submit" class="btn btn-primary">Хадгалах</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Merchant List Tab -->
                                    <div class="active tab-pane table-responsive text-nowrap" id="list">
                                        <button class="btn btn-primary float-left " style="margin-bottom:10px;"><i
                                                class="fa fa-plus-circle create-merchant"></i></button>
                                        <table class="table table-light table-striped border-primary w-auto small">
                                            <thead>
                                                <tr>
                                                    <th>Салбарын нэр</th>
                                                    <th>Утас</th>
                                                    <th class="th-sm">Хаяг</th>
                                                    <th>what3words</th>
                                                    <th>Засах</th>
                                                    <th>Устгах</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $merchant = DB::table('merchant')
                                                    ->where('user_id', Auth::user()->id)
                                                    ->where('deleted', 0)
                                                    ->orderBy('id', 'desc')
                                                    ->get();
                                                ?>
                                                @foreach ($merchant as $item)
                                                    <?php $id = $item->id; ?>
                                                    <tr>
                                                        <td>{{ $item->merchantName }}</td>
                                                        <td>{{ $item->merchantPhone1 }},{{ $item->merchantPhone2 }} </td>
                                                        <td>
                                                            {{ $item->merchantAddress }}</td>
                                                        <td>{{ $item->merchantWhat3Words }}</td>
                                                        <td>
                                                            <a href="#" data-toggle="modal"
                                                                id="editMerchantInfo{{ $item->id }}"
                                                                data-target="#editMerchantInfo{{ $item->id }}"
                                                                class="btn  btn-primary btn-xs"
                                                                style="color:white!important;">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td>
                                                        <td> <a href="{{ route('deleteMerchant', $item->id) }}"
                                                                onclick="return confirm('Та устгахдаа итгэлтэй байна уу?');"
                                                                class="btn  btn-primary btn-xs"
                                                                style="color:white!important;">
                                                                <i class="fas fa-trash"></i>
                                                            </a></td>

                                                        <!-- Edit Merchant Modal -->
                                                        <div class="modal fade" id="editMerchantInfo{{ $item->id }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="editMerchantInfoLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="editMerchantInfoLabel">
                                                                            Салбар-мерчантын мэдээлэл өөрчлөх
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="{{ route('editMerchant') }}"
                                                                            method="post">
                                                                            @csrf
                                                                            <input type="hidden" class="form-control"
                                                                                id="merchantId" name="merchantId"
                                                                                value="{{ $item->id }}">

                                                                            <div class="form-group">
                                                                                <label for="merchantName">Салбарын нэр</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="merchantName" name="merchantName"
                                                                                    value="{{ $item->merchantName }}">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="merchantPhone1">Утас</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="merchantPhone1"
                                                                                    value="{{ $item->merchantPhone1 }}">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="merchantPhone2">Утас</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="merchantPhone2"
                                                                                    value="{{ $item->merchantPhone2 }}">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="merchantAddress">Хаяг</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="merchantAddress"
                                                                                    value="{{ $item->merchantAddress }}">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="merchantWhat3Words">What3words</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="merchantWhat3Words"
                                                                                    value="{{ $item->merchantWhat3Words }}">
                                                                            </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Хаах</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Хадгалах</button>
                                                                    </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Edit Merchant Modal -->
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- End Merchant List Tab -->

                                    <!-- Activity Tab -->
                                    <div class=" tab-pane" id="activity">
                                        <?php $notification = DB::table('notifications')->get(); ?>

                                        @foreach ($notification as $notifications)
                                            <div class="post">
                                                <div class="user-block">
                                                    <img class="img-circle img-bordered-sm"
                                                        src="/{{ $notifications->sent_by_image }}" alt="user image">
                                                    <span class="username">
                                                        <a href="#"> {{ $notifications->title }}</a>
                                                        <a href="#" class="float-right btn-tool"><i
                                                                class="fas fa-times"></i></a>
                                                    </span>
                                                    <span class="description"> {{ $notifications->created_at }}</span>
                                                </div>

                                                <p>
                                                    {{ $notifications->description }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Timeline Tab -->
                                    <div class="tab-pane" id="timeline">
                                        {!! Form::open([
                                            'url' => 'feedback/create',
                                            'method' => 'post',
                                            'role' => 'form',
                                            'files' => true,
                                            'enctype' => 'multipart/form-data',
                                        ]) !!}
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Гарчиг</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="title" class="form-control" id="inputName"
                                                    placeholder="Гарчиг">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Агуулга</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="description" class="form-control"
                                                    id="inputEmail" placeholder="Агуулга">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger">Илгээх</button>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>

                                    <!-- Settings Tab -->
                                    <div class="tab-pane" id="settings">
                                        <form class="form-horizontal">
                                            <div class="form-group row">
                                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="inputName"
                                                        placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="inputEmail"
                                                        placeholder="Email">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="inputName2"
                                                        placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputExperience"
                                                    class="col-sm-2 col-form-label">Experience</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="inputSkills"
                                                        placeholder="Skills">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox"> I agree to the <a href="#">terms
                                                                and conditions</a>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" class="btn btn-danger">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle the click event on the edit icon
            $('.edit-icon').click(function() {
                // Show the modal when the edit icon is clicked
                $('#editPhoneNumberModal').modal('show');
            });

            // Handle the click event on the "Save changes" button in the modal
            $('#savePhoneNumber').click(function() {
                // Retrieve the edited phone number from the form field
                var editedPhoneNumber = $('#phoneNumber').val();

                // Here, you can update the phone number as needed
                // For demonstration purposes, we'll just log it
                console.log('Edited phone number:', editedPhoneNumber);

                // Close the modal
                $('#editPhoneNumberModal').modal('hide');
            });

            // Handle the click event on the create merchant button
            $('.create-merchant').click(function() {
                // Show the modal when the edit icon is clicked
                $('#createMerchantModal').modal('show');
            });
        });
    </script>
@endsection