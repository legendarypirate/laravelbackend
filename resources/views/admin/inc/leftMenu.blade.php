<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               

               @if(\Auth::user()->hasPermissionTo('хянах_самбар'))
          <li class="nav-item menu-open">
          @if(request()->is('home*'))
            <a href="{{route('home')}}" class="nav-link active">
              @else 
              <a href="{{route('home')}}" class="nav-link">

              @endif
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Хянах самбар
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            @endif
         
        
          <li class="nav-item">
          @if(request()->is('order*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">

              @endif
              <i class="nav-icon fas fa-book"></i>
              <p>
                Захиалга
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            @if(\Auth::user()->hasPermissionTo('захиалга_үүсгэх'))

              <li class="nav-item">
                <a href="{{url('/order/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга үүсгэх</p>
                </a>
              </li>
              @endif

              @if(\Auth::user()->hasPermissionTo('захиалга_жагсаалт'))

              <li class="nav-item">
                <a href="{{url('/order/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/driver')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолооч хүлээж авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="{{url('/order/finished')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бараа авсан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/report')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан захиалгаар</p>
                </a>
              </li>
              @endif
            </ul>
          </li>

          </li>

          @if(request()->is('delivery*'))
          <li class="nav-item">
            <a href="#" class="nav-link active">
              @else 
              <li class="nav-item">
            <a href="#" class="nav-link">
              @endif
              <i class="nav-icon fas fa-truck"></i>
              <p>
                Хүргэлт
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/delivery/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хүргэлт үүсгэх</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/delivery/new')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Үүссэн хүргэлт</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/delivery/received')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хүлээн авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="{{url('/delivery/deleted')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Устгасан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/delivery/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хүргэлтийн жагсаалт</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/delivery/done')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Дууссан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/delivery/report')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
          @if(request()->is('region*'))
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
              @if(\Auth::user()->hasPermissionTo('бүс_үүсгэх'))
              <li class="nav-item">
                <a href="{{url('/region/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бүс үүсгэх</p>
                </a>
              </li>
              @endif

              @if(\Auth::user()->hasPermissionTo('бүс_жагсаалт'))
              <li class="nav-item">
                <a href="{{url('/region/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жагсаалт</p>
                </a>
              </li>
              @endif
            </ul>
          </li>

          <li class="nav-item">
          @if(request()->is('notif*'))
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
                <a href="{{url('/notification/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Мэдэгдэл илгээх</p>
                </a>
              </li>
            
            
            </ul>
          </li>

          <li class="nav-item">
          @if(request()->is('ware*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">
               @endif
               <i class="nav-icon fas fa-warehouse"></i>
              <p>
                Агуулах
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{url('/good/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Агуулах үүсгэх</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('/good/income')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бараа орлогодох, зарлагадах</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/good/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жагсаалт</p>
                </a>
              </li>

            
            </ul>
          </li>

          <li class="nav-item">
          @if(request()->is('report*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">
               @endif
              <i class="nav-icon fas fa-list"></i>
              <p>
                Тайлан
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/report/driver')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолоочийн тайлан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/report/driverdone')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолоочийн нийлсэн тайлан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/report/customer')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Харилцагчийн тайлан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="{{url('/report/customerdone')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Харилцагчийн нийлсэн тайлан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/report/general')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ерөнхий тайлан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/report/ware')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Агуулахын тайлан</p>
                </a>
              </li>
            
            </ul>
          </li>

          @if(\Auth::user()->hasPermissionTo('эрхийн_зохицуулалт'))

          <li class="nav-item">
          @if(request()->is('role*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">
               @endif              <i class="nav-icon fas fa-key"></i>
              <p>
                Эрхийн зохицуулалт
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item" >
                <a href="{{url('/role/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon" ></i>
                  <p >Эрх үүсгэх</p>
                </a>
              </li>
             
              <li class="nav-item" >
                <a href="{{url('/role/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жагсаалт</p>
                </a>
              </li>
            
            </ul>
          </li>
          @endif

          @if(\Auth::user()->hasPermissionTo('хэрэглэгч'))

          <li class="nav-item">
          @if(request()->is('user*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">
               @endif              <i class="nav-icon fas fa-user"></i>
              <p>
                Хэрэглэгч
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/user/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хэрэглэгч үүсгэх</p>
                </a>
              </li>
             
            
              <li class="nav-item">
                <a href="{{url('/phone/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Утас жагсаалт</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="{{url('/address/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хаяг жагсаалт</p>
                </a>
              </li>
            
              <li class="nav-item">
                <a href="{{url('/user/list')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                  <p>Хэрэглэгчийн жагсаалт</p>
                </a>
              </li>
             
            </ul>
          </li>
          @endif
          <li class="nav-item">
          @if(request()->is('log*'))
            <a href="#" class="nav-link active">
              @else
              <a href="#" class="nav-link">
               @endif              <i class="nav-icon fas fa-table"></i>
              <p>
                Үйлдлийн лог
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
             
              <li class="nav-item">
                <a href="{{url('/log/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жагсаалт</p>
                </a>
              </li>
            
            </ul>
          </li>
          <li class="nav-item">
            
                  <a class="dropdown-item" href="{{ route('logout') }}"
                      onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
<i class="fas fa-power-off"></i>                {{ __('Гарах') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                      @csrf
                  </form>
             
          </li>
        </ul>
      </nav>