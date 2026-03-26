<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        @if (\Auth::user()->checkPermissionTo('хянах_самбар'))
            <li class="nav-item menu-open">
                @if (request()->is('home*'))
                    <a href="{{ route('home') }}" class="nav-link active">
                    @else
                        <a href="{{ route('home') }}" class="nav-link">
                @endif
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    CHUCHU ДАШБОАРД
                    {{-- <i class="right fas fa-angle-left"></i> --}}
                </p>
                </a>

        @endif


        {{-- <li class="nav-item">
          @if (request()->is('order*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">

              @endif
              <i class="nav-icon fas fa-book"></i>
              <p>
                Хүргэлтийн дуудлага
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            @if (\Auth::user()->checkPermissionTo('захиалга_үүсгэх'))

              <li class="nav-item">
                <a href="{{url('/order/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Дуудлага үүсгэх</p>
                </a>
              </li>
              @endif

              @if (\Auth::user()->checkPermissionTo('захиалга_жагсаалт'))

              <li class="nav-item">
                <a href="{{url('/order/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Дуудлагын жагсаалт</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/driver')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолооч хүлээн авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="{{url('/order/finished')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Биелэсэн дуудлага</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/report')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан</p>
                </a>
              </li>
              @endif
            </ul>
          </li> --}}

        </li>

        @if (request()->is('delivery*'))
            <li class="nav-item">
                <a href="#" class="nav-link active">
                @else
            <li class="nav-item">
                <a href="#" class="nav-link">
        @endif
        @if (auth()->user()->role != 'driver')
            <i class="nav-icon fas fa-truck"></i>
            <p>
                Хүргэлт
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{ url('/delivery/new') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Шинэ хүргэлт</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/delivery/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Хүргэлтийн жагсаалт</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/delivery/done') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Амжилттай хүргэгдсэн</p>
                    </a>
                </li>

                <!-- <li class="nav-item">
                <a href="{{ url('/delivery/received') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Бараа хүлээн авсан</p>
                </a>
            </li> -->
        @endif
        @if (auth()->user()->role == 'admin')
            <li class="nav-item">
                <a href="{{ url('/delivery/deleted') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Устгасан</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('/delivery/delivery_download') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Хүргэлтийн татан авалт</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/delivery/all') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Бүх хүргэлтийн мэдээлэл</p>
                </a>
            </li>
        @endif
        <li class="nav-item">
                <a href="{{ url('/delivery/report') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Тайлан</p>
                </a>
            </li> 
    </ul>
    </li>
    @if (\Auth::user()->checkPermissionTo('бүс_жагсаалт'))

        <li class="nav-item">
            @if (request()->is('region*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-map"></i>
            <p>
                Бүс
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                @if (\Auth::user()->checkPermissionTo('бүс_үүсгэх'))
                    <li class="nav-item">
                        <a href="{{ url('/region/index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Бүс үүсгэх</p>
                        </a>
                    </li>
                @endif

                @if (\Auth::user()->checkPermissionTo('бүс_жагсаалт'))
                    <li class="nav-item">
                        <a href="{{ url('/region/list') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Жагсаалт</p>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if (auth()->user()->role == 'admin')
        <li class="nav-item">
            @if (request()->is('setting*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-cog"></i>
            <p>
                Тохиргоо
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{ url('/setting/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жагсаалт</p>
                    </a>
                </li>
            </ul>
        </li>
    @endif
    @if (auth()->user()->role != 'driver')
        <li class="nav-item">
            @if (request()->is('feedback*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-map"></i>
            <p>
                Санал хүсэлт
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">



                <li class="nav-item">
                    <a href="{{ url('/feedback/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Хүсэлтүүд харах</p>
                    </a>
                </li>

            </ul>
        </li>
    @endif
    @if (auth()->user()->role == 'admin')
        <li class="nav-item">
            @if (request()->is('notif*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-image"></i>
            <p>
                Сурталчилгаа
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/banner/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Баннер жагсаалт</p>
                    </a>
                </li>
            </ul>
        </li>
    @endif
    @if (\Auth::user()->checkPermissionTo('масс_мэдэгдэл'))
        <li class="nav-item">
            @if (request()->is('notif*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-bell"></i>
            <p>
                Масс мэдэгдэл
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/notification/index') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Мэдэгдэл илгээх</p>
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if (\Auth::user()->checkPermissionTo('барааны_цэс'))

        <li class="nav-item">
            @if (request()->is('ware*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
                Барааны агуулах
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                           <li class="nav-item">
                    <a href="{{ url('/item/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Барааны жагсаалт</p>
                    </a>
                </li>


            </ul>
        </li>

         <li class="nav-item">
            @if (request()->is('invoice*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
                Нэхэмжлэх
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                           <li class="nav-item">
                    <a href="{{ url('/invoice/index') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Нэхэмжлэх үүсгэх</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/invoice/profile') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Нэхэмжлэгч профайл</p>
                    </a>
                </li>
            </ul>
        </li>
    @endif
    @if (auth()->user()->role == 'admin')

        <li class="nav-item">
            @if (request()->is('report*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif
            <i class="nav-icon fas fa-list"></i>
            <p>
                Жолооч
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/driver/request') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жолоочийн хүсэлт </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/driver/drivermonitoring') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жолоочийн мониторинг</p>
                    </a>
                </li>
                <li class="nav-item">
                    @if (request()->is('admin/driver-monitoring*'))
                        <a href="{{ url('/admin/driver-monitoring') }}" class="nav-link active">
                    @else
                        <a href="{{ url('/admin/driver-monitoring') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жолооч мониторинг (New)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/driver/location') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Байршил</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ url('/report/driverdone') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жолоочийн тайлан </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/report/customer') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Харилцагчийн тайлан</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/report/customerdone') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Харилцагчийн нийлсэн тайлан</p>
                    </a>
                </li> --}}
                {{-- <li class="nav-item">
                    <a href="{{ url('/report/general') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Ерөнхий тайлан</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/report/ware') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Агуулахын тайлан</p>
                    </a>
                </li> --}}

            </ul>
        </li>
    @endif
    @if (\Auth::user()->checkPermissionTo('эрхийн_зохицуулалт'))

        <li class="nav-item">
            @if (request()->is('role*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif <i class="nav-icon fas fa-key"></i>
            <p>
                Эрхийн зохицуулалт
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/role/index') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Эрх үүсгэх</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/role/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жагсаалт</p>
                    </a>
                </li>

            </ul>
        </li>
    @endif

    @if (\Auth::user()->checkPermissionTo('хэрэглэгч'))

        <li class="nav-item">
            @if (request()->is('user*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif <i class="nav-icon fas fa-user"></i>
            <p>
                Системийн хэрэглэгч
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/user/index') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Хэрэглэгч үүсгэх</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('/phone/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Утасны жагсаалт харах</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/address/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Хаягийн жагсаалт харах</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/user/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Хэрэглэгчийн жагсаалт</p>
                    </a>
                </li>

            </ul>
        </li>
    @endif
    @if (\Auth::user()->checkPermissionTo('үйлдлийн_лог'))

        <li class="nav-item">
            @if (request()->is('log*'))
                <a href="#" class="nav-link active">
                @else
                    <a href="#" class="nav-link">
            @endif <i class="nav-icon fas fa-table"></i>
            <p>
                Үйлдлийн түүх харах
                <i class="fas fa-angle-left right"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">


                <li class="nav-item">
                    <a href="{{ url('/log/list') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Жагсаалт</p>
                    </a>
                </li>

            </ul>
        </li>
        <br> <br>


    @endif
    <li class="nav-item">

        <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
            <i class="fas fa-power-off"></i> {{ __('Гарах') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

    </li>
    <br> <br>
    <li class="nav-item">

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
    </ul>
</nav>
