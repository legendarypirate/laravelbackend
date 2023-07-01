<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="{{route('home')}}" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Хянах самбар
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
          
          </li>
       
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
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
                <a href="{{url('/order/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Шинэ хүргэлт</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хүлээн авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Устгасан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Хүргэлтийн жагсаалт</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Дууссан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Захиалга
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/order/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга үүсгэх</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолооч хүлээж авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бараа хүлээж авсан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан захиалгаар</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Бүс
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/region/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бүс үүсгэх</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/region/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жагсаалт</p>
                </a>
              </li>
            
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Барааны цэс
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/good/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бараа үүсгэх</p>
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
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Тайлан
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/order/index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга үүсгэх</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/order/list')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Захиалга</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Жолооч хүлээж авсан</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Бараа хүлээж авсан</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/jsgrid.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Тайлан захиалгаар</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>