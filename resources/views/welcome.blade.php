<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Онлайн Хүргэлтийн Платформ</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <style>
        body {
            background-image: url('{{ asset('dist') }}/background.jpeg');
            background-repeat: no-repeat;
            background-size: cover;
            background-color: rgba(78, 66, 72, 0.1) !important;

        }
    </style>
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css?v=3.2.0">
    <script nonce="fc8f1398-c281-4d1b-8f16-4181bb7fcb32">
        (function(w, d) {
            ! function(a, b, c, d) {
                a[c] = a[c] || {};
                a[c].executed = [];
                a.zaraz = {
                    deferred: [],
                    listeners: []
                };
                a.zaraz.q = [];
                a.zaraz._f = function(e) {
                    return function() {
                        var f = Array.prototype.slice.call(arguments);
                        a.zaraz.q.push({
                            m: e,
                            a: f
                        })
                    }
                };
                for (const g of ["track", "set", "debug"]) a.zaraz[g] = a.zaraz._f(g);
                a.zaraz.init = () => {
                    var h = b.getElementsByTagName(d)[0],
                        i = b.createElement(d),
                        j = b.getElementsByTagName("title")[0];
                    j && (a[c].t = b.getElementsByTagName("title")[0].text);
                    a[c].x = Math.random();
                    a[c].w = a.screen.width;
                    a[c].h = a.screen.height;
                    a[c].j = a.innerHeight;
                    a[c].e = a.innerWidth;
                    a[c].l = a.location.href;
                    a[c].r = b.referrer;
                    a[c].k = a.screen.colorDepth;
                    a[c].n = b.characterSet;
                    a[c].o = (new Date).getTimezoneOffset();
                    if (a.dataLayer)
                        for (const n of Object.entries(Object.entries(dataLayer).reduce(((o, p) => ({
                                ...o[1],
                                ...p[1]
                            })), {}))) zaraz.set(n[0], n[1], {
                            scope: "page"
                        });
                    a[c].q = [];
                    for (; a.zaraz.q.length;) {
                        const q = a.zaraz.q.shift();
                        a[c].q.push(q)
                    }
                    i.defer = !0;
                    for (const r of [localStorage, sessionStorage]) Object.keys(r || {}).filter((t => t.startsWith(
                        "_zaraz_"))).forEach((s => {
                        try {
                            a[c]["z_" + s.slice(7)] = JSON.parse(r.getItem(s))
                        } catch {
                            a[c]["z_" + s.slice(7)] = r.getItem(s)
                        }
                    }));
                    i.referrerPolicy = "origin";
                    i.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(a[c])));
                    h.parentNode.insertBefore(i, h)
                };
                ["complete", "interactive"].includes(b.readyState) ? zaraz.init() : a.addEventListener(
                    "DOMContentLoaded", zaraz.init)
            }(w, d, "zarazData", "script");
        })(window, document);
    </script>
</head>

<body class="hold-transition login-page">
    <div class="login-box" style="border-radius:15px !important;position:absolute;left:5%;top:0%;">

        <img style="margin-left:60px;width:240px;" src="{{ asset('dist') }}/logoweb.png">

        <div class="login-logo">
        </div>

        <div class="card" style="border-radius:15px !important;">
            <div class="card-body login-card-body" style="border-radius:15px !important;">
                <p class="login-box-msg">ХАРИЛЦАГЧААР НЭВТРЭХ</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Харилцагчийн нэр">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Нууц үг">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Намайг сана
                                </label>
                            </div>
                        </div>

                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Нэвтрэх</button>
                        </div>

                    </div>
                </form>
                <div class="row" style="margin-top:30px">
                    <div class="col-md-12 text-center ">
                        <a href="{{ route('register') }}" class="btn btn-success text-uppercase text-color-white"
                            style="width:100%; height:50px; display: flex;
  justify-content: center;
  align-items: center;">Яг
                            одоо туршиж
                            үзье</a>
                    </div>
                    <br> <br>
                    <li>

                        <a class="dropdown-item" href="{{ url('app-debug.apk') }}"> Харилцагчийн АПП татах
                             <div><img src="{{ asset('dist') }}/ps.png" width=150></div>
                        </a>

                        <!-- <a class="dropdown-item" href="{{ url('#') }}">
                         <div><img src="{{ asset('dist') }}/as.png" width=150></div>
                        </a> -->
  

                          <a class="dropdown-item" href="{{ url('app-debugdriver.apk') }}"> Жолоочийн АПП татах
                                <div><img src="{{ asset('dist') }}/ps.png" width=150></div>
                          </a>
                     </li>
                </div>

                <div class="social-auth-links text-center mb-3">

                </div>

            </div>

        </div>
    </div>


    <script src="../../plugins/jquery/jquery.min.js"></script>

    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
</body>

</html>

@include('sweetalert::alert')
