<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Онлайн Хүргэлтийн Платформ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}?v=3.2.0">

    <style>
        :root {
            --wc-customer: #2563eb;
            --wc-customer-dark: #1d4ed8;
            --wc-driver: #0d9488;
            --wc-driver-dark: #0f766e;
            --wc-surface: rgba(255, 255, 255, 0.97);
            --wc-border: rgba(148, 163, 184, 0.35);
            --wc-text: #1e293b;
            --wc-muted: #64748b;
        }

        body.welcome-body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', 'Source Sans Pro', system-ui, sans-serif;
            color: var(--wc-text);
            background: #0f172a url('{{ asset('dist/background.jpeg') }}') center / cover no-repeat fixed;
        }

        .welcome-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(145deg,
                    rgba(15, 23, 42, 0.88) 0%,
                    rgba(30, 58, 95, 0.72) 45%,
                    rgba(15, 23, 42, 0.85) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .welcome-page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 3vw, 2rem);
            box-sizing: border-box;
        }

        .welcome-shell {
            width: 100%;
            max-width: 1120px;
        }

        .welcome-brand {
            text-align: center;
            margin-bottom: clamp(1.25rem, 3vw, 2rem);
        }

        .welcome-brand img {
            max-width: 260px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.35));
        }

        .welcome-brand .tagline {
            margin: 0.75rem 0 0;
            font-size: 0.95rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.88);
            letter-spacing: 0.02em;
        }

        .welcome-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: clamp(1.5rem, 4vw, 2.25rem);
        }

        @media (min-width: 992px) {
            .welcome-grid {
                grid-template-columns: 1fr 1fr;
                gap: clamp(1.75rem, 3.5vw, 3rem);
                align-items: stretch;
            }
        }

        .welcome-panel {
            background: var(--wc-surface);
            backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 20px 40px -15px rgba(0, 0, 0, 0.25),
                0 0 0 1px var(--wc-border);
        }

        .welcome-panel__head {
            padding: 1.25rem 1.5rem 1.1rem;
            color: #fff;
            text-align: center;
        }

        .welcome-panel__head h2 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .welcome-panel__head p {
            margin: 0.35rem 0 0;
            font-size: 0.8rem;
            opacity: 0.92;
            font-weight: 400;
        }

        .welcome-panel__head--customer {
            background: linear-gradient(135deg, var(--wc-customer) 0%, var(--wc-customer-dark) 100%);
        }

        .welcome-panel__head--driver {
            background: linear-gradient(135deg, var(--wc-driver) 0%, var(--wc-driver-dark) 100%);
        }

        .welcome-panel__head i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }

        .welcome-panel__body {
            padding: 1.5rem 1.5rem 1.75rem;
            flex: 1;
        }

        .welcome-input-wrap {
            position: relative;
            margin-bottom: 0.9rem;
        }

        .welcome-input-wrap .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--wc-muted);
            font-size: 0.95rem;
            z-index: 2;
            pointer-events: none;
        }

        .welcome-input-wrap--stack .input-icon {
            top: 13px;
            transform: none;
        }

        .welcome-input-wrap .form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 0.55rem 0.75rem 0.55rem 2.5rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .welcome-input-wrap textarea.form-control {
            padding-left: 2.5rem;
            min-height: 72px;
        }

        .welcome-input-wrap .form-control:focus {
            border-color: var(--wc-customer);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .welcome-panel--driver .welcome-input-wrap .form-control:focus {
            border-color: var(--wc-driver);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18);
        }

        .welcome-input-wrap label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--wc-muted);
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .btn-welcome-customer {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.55rem 1rem;
            background: linear-gradient(135deg, var(--wc-customer), var(--wc-customer-dark));
            border: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }

        .btn-welcome-customer:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }

        .btn-welcome-trial {
            border-radius: 12px;
            font-weight: 600;
            border: none;
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
        }

        .btn-welcome-driver {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.65rem 1rem;
            background: linear-gradient(135deg, var(--wc-driver), var(--wc-driver-dark));
            border: none;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4);
        }

        .btn-welcome-driver:hover {
            filter: brightness(1.05);
        }

        .welcome-app-links {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #e2e8f0;
        }

        .welcome-app-links a {
            color: var(--wc-customer);
            font-weight: 600;
            font-size: 0.88rem;
        }

        .welcome-app-links a:hover {
            text-decoration: underline !important;
        }

        .welcome-app-links .app-badge {
            display: inline-block;
            margin-top: 0.35rem;
        }

        .welcome-app-links .app-badge img {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .welcome-form-compact .form-group {
            margin-bottom: 0.85rem;
        }

        .welcome-form-compact select.form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }

        .welcome-form-compact select.form-control:focus {
            border-color: var(--wc-driver);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }

        @media (max-width: 991.98px) {
            .welcome-panel {
                border-radius: 20px !important;
            }
        }
    </style>
</head>

<body class="hold-transition welcome-body">
    <div class="welcome-overlay" aria-hidden="true"></div>

    <div class="welcome-page">
        <div class="welcome-shell">
            <header class="welcome-brand">
                <img src="{{ asset('dist/logoweb.png') }}" alt="Онлайн Хүргэлтийн Платформ">
                <p class="tagline">Таны бизнесийн хүргэлтийн найдвартай түнш</p>
            </header>

            <div class="welcome-grid">
                <!-- Харилцагч -->
                <article class="welcome-panel welcome-panel--customer">
                    <div class="welcome-panel__head welcome-panel__head--customer">
                        <i class="fas fa-store d-block"></i>
                        <h2>Харилцагч</h2>
                        <p>Системд нэвтрэх</p>
                    </div>
                    <div class="welcome-panel__body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="welcome-input-wrap">
                                <span class="input-icon"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" placeholder="Харилцагчийн нэр" required autocomplete="username">
                            </div>
                            <div class="welcome-input-wrap">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" placeholder="Нууц үг" required autocomplete="current-password">
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <div class="icheck-primary mb-2 mb-sm-0">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember" class="text-muted small">Намайг сана</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-welcome-customer px-4">
                                    Нэвтрэх <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </form>

                        <a href="{{ route('register') }}" class="btn btn-success btn-welcome-trial btn-block text-uppercase py-2 mb-0">
                            Яг одоо туршиж үзье
                        </a>

                        <div class="welcome-app-links text-center">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ url('app-debug.apk') }}">Харилцагчийн АПП</a>
                                    <div class="app-badge"><img src="{{ asset('dist/ps.png') }}" width="120" alt=""></div>
                                </div>
                                <div class="col-6">
                                    <a href="{{ url('app-debugdriver.apk') }}">Жолоочийн АПП</a>
                                    <div class="app-badge"><img src="{{ asset('dist/ps.png') }}" width="120" alt=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Жолоочоор элсэх -->
                <article class="welcome-panel welcome-panel--driver">
                    <div class="welcome-panel__head welcome-panel__head--driver">
                        <i class="fas fa-truck d-block"></i>
                        <h2>Жолоочоор элсэх</h2>
                        <p>Хүсэлт илгээх</p>
                    </div>
                    <div class="welcome-panel__body welcome-form-compact">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-lg border-0 small">
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
                                <div class="form-group col-md-6 mb-2">
                                    <label for="dj_lastname">Овог <span class="text-danger">*</span></label>
                                    <div class="welcome-input-wrap mb-0">
                                        <span class="input-icon"><i class="fas fa-signature"></i></span>
                                        <input type="text" class="form-control" id="dj_lastname" name="lastname"
                                            value="{{ old('lastname') }}" required maxlength="255" placeholder="Овог">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 mb-2">
                                    <label for="dj_firstname">Нэр <span class="text-danger">*</span></label>
                                    <div class="welcome-input-wrap mb-0">
                                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="dj_firstname" name="firstname"
                                            value="{{ old('firstname') }}" required maxlength="255" placeholder="Нэр">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-2">
                                    <label for="dj_phone">Утас <span class="text-danger">*</span></label>
                                    <div class="welcome-input-wrap mb-0">
                                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control" id="dj_phone" name="phone"
                                            value="{{ old('phone') }}" required maxlength="50" placeholder="Утас">
                                    </div>
                                </div>
                                <div class="form-group col-md-6 mb-2">
                                    <label for="dj_email">Имэйл</label>
                                    <div class="welcome-input-wrap mb-0">
                                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="dj_email" name="email"
                                            value="{{ old('email') }}" maxlength="255" placeholder="email@...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="dj_city">Хот</label>
                                <div class="welcome-input-wrap mb-0">
                                    <span class="input-icon"><i class="fas fa-city"></i></span>
                                    <input type="text" class="form-control" id="dj_city" name="city" value="{{ old('city') }}"
                                        maxlength="255" placeholder="Хот / дүүрэг">
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="dj_address">Гэрийн хаяг</label>
                                <div class="welcome-input-wrap welcome-input-wrap--stack mb-0">
                                    <span class="input-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea class="form-control" id="dj_address" name="address" rows="2" maxlength="1000"
                                        placeholder="Дэлгэрэнгүй хаяг">{{ old('address') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="dj_comment">Нэмэлт тайлбар</label>
                                <div class="welcome-input-wrap welcome-input-wrap--stack mb-0">
                                    <span class="input-icon"><i class="fas fa-comment-alt"></i></span>
                                    <textarea class="form-control" id="dj_comment" name="comment" rows="2" maxlength="2000"
                                        placeholder="Нэмэлт мэдээлэл">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="dj_gender">Хүйс</label>
                                <select class="form-control" id="dj_gender" name="gender">
                                    <option value="">— Сонгох —</option>
                                    <option value="Эрэгтэй" {{ old('gender') === 'Эрэгтэй' ? 'selected' : '' }}>Эрэгтэй</option>
                                    <option value="Эмэгтэй" {{ old('gender') === 'Эмэгтэй' ? 'selected' : '' }}>Эмэгтэй</option>
                                    <option value="Бусад" {{ old('gender') === 'Бусад' ? 'selected' : '' }}>Бусад</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-welcome-driver btn-block">
                                <i class="fas fa-paper-plane mr-2"></i> Хүсэлт илгээх
                            </button>
                        </form>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}?v=3.2.0"></script>
</body>

</html>

@include('sweetalert::alert')
