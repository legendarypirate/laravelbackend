<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Онлайн Хүргэлтийн Платформ</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <style>
        body {
            background-image: url('{{ asset('dist/background.jpeg') }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-color: rgba(78, 66, 72, 0.1) !important;
            background-attachment: fixed;
        }
        .welcome-card {
            border-radius: 15px !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}?v=3.2.0">
</head>

<body class="hold-transition">
    <div class="container-fluid py-4 px-lg-5">
        <div class="row justify-content-center align-items-start">
            <!-- Login -->
            <div class="col-lg-6 col-xl-5 mb-4">
                <div class="mx-auto" style="max-width: 420px;">
                    <img class="d-block mx-auto mb-3" style="max-width:240px;width:100%;"
                        src="{{ asset('dist/logoweb.png') }}" alt="Logo">

                    <div class="card welcome-card shadow">
                        <div class="card-body login-card-body">
                            <p class="login-box-msg text-center font-weight-bold">ХАРИЛЦАГЧААР НЭВТРЭХ</p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" placeholder="Харилцагчийн нэр" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text"></div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" placeholder="Нууц үг" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="remember" name="remember">
                                            <label for="remember">Намайг сана</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary btn-block">Нэвтрэх</button>
                                    </div>
                                </div>
                            </form>
                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('register') }}" class="btn btn-success btn-block text-uppercase"
                                        style="min-height:48px; display:flex; align-items:center; justify-content:center;">Яг
                                        одоо туршиж үзье</a>
                                </div>
                            </div>
                            <ul class="list-unstyled text-center mt-3 mb-0 small">
                                <li class="mb-2">
                                    <a class="d-inline-block" href="{{ url('app-debug.apk') }}">Харилцагчийн АПП татах</a>
                                    <div><img src="{{ asset('dist/ps.png') }}" width="150" alt=""></div>
                                </li>
                                <li>
                                    <a class="d-inline-block" href="{{ url('app-debugdriver.apk') }}">Жолоочийн АПП татах</a>
                                    <div><img src="{{ asset('dist/ps.png') }}" width="150" alt=""></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver join request -->
            <div class="col-lg-6 col-xl-5 mb-4">
                <div class="card welcome-card shadow h-100">
                    <div class="card-body">
                        <p class="login-box-msg text-center font-weight-bold mb-3">Жолоочоор элсэх хүсэлт</p>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('driver.join-request') }}" id="driverJoinForm">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="dj_lastname">Овог <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dj_lastname" name="lastname"
                                        value="{{ old('lastname') }}" required maxlength="255" placeholder="Овог">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="dj_firstname">Нэр <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dj_firstname" name="firstname"
                                        value="{{ old('firstname') }}" required maxlength="255" placeholder="Нэр">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="dj_phone">Утас <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dj_phone" name="phone"
                                        value="{{ old('phone') }}" required maxlength="50" placeholder="Утасны дугаар">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="dj_email">Имэйл</label>
                                    <input type="email" class="form-control" id="dj_email" name="email"
                                        value="{{ old('email') }}" maxlength="255" placeholder="name@example.com">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dj_city">Хот</label>
                                <input type="text" class="form-control" id="dj_city" name="city" value="{{ old('city') }}"
                                    maxlength="255" placeholder="Хот / дүүрэг">
                            </div>
                            <div class="form-group">
                                <label for="dj_address">Гэрийн хаяг</label>
                                <textarea class="form-control" id="dj_address" name="address" rows="2" maxlength="1000"
                                    placeholder="Дэлгэрэнгүй хаяг">{{ old('address') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="dj_comment">Нэмэлт тайлбар</label>
                                <textarea class="form-control" id="dj_comment" name="comment" rows="2" maxlength="2000"
                                    placeholder="Нэмэлт мэдээлэл">{{ old('comment') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="dj_gender">Хүйс</label>
                                <select class="form-control" id="dj_gender" name="gender">
                                    <option value="">— Сонгох —</option>
                                    <option value="Эрэгтэй" {{ old('gender') === 'Эрэгтэй' ? 'selected' : '' }}>Эрэгтэй</option>
                                    <option value="Эмэгтэй" {{ old('gender') === 'Эмэгтэй' ? 'selected' : '' }}>Эмэгтэй</option>
                                    <option value="Бусад" {{ old('gender') === 'Бусад' ? 'selected' : '' }}>Бусад</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane mr-1"></i> Хүсэлт илгээх
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}?v=3.2.0"></script>
</body>

</html>

@include('sweetalert::alert')
