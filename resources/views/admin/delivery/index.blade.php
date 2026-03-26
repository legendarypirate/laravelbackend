@extends('admin.master')

@section('mainContent')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content ">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-6">
                        <div id="accordion">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <a class="card-link">
                                        1. Илгээгчийн мэдээлэл оруулах хэсэг (Хаанаас ямар илгээмж авах тухай мэдээллээ энд
                                        бөглөнө үү)
                                    </a>
                                </div>
                                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                    @if (auth()->user()->role == 'customer')
                                        {!! Form::open([
                                            'url' => 'delivery/create',
                                            'method' => 'post',
                                            'role' => 'form',
                                            'files' => true,
                                            'enctype' => 'multipart/form-data',
                                        ]) !!}
                                        <div class="card-body">

                                            <?php $merchant = DB::table('merchant')
                                                ->where('user_id', '=', auth()->user()->id)
                                                ->where('deleted', '=', 0)
                                                ->get(); ?>

                                            @if ($merchant->isNotEmpty())
                                                <div style="position: absolute; right: 10px;margin-top:-18px">
                                                    <input type="checkbox" id="toggleCheckbox" class="form-group"> <label
                                                        for="toggleCheckbox" class="btn btn-warning"
                                                        style="color:#FFF!important;">Шинэ илгээгч
                                                        нэмэх</label>
                                                </div>
                                                <div id="merchantDivShow" style="display:'block'">
                                                    <div class="form-group">
                                                        <label for="exampleSelectRounded0">Салбарын нэр
                                                            <code></code></label>

                                                        <select class="custom-select rounded-0" name="merchant_id"
                                                            id="merchantsRequest">

                                                            <option value="">Салбар-мерчант сонгох</option>
                                                            @foreach ($merchant as $merchants)
                                                                <option value="{{ $merchants->id }}">
                                                                    {{ $merchants->merchantName }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Утас</label>
                                                        <select class="custom-select rounded-0"
                                                            aria-label=".form-select-lg example" name="phone"
                                                            id="merchantPhone1">
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Утас</label>
                                                        <select class="custom-select rounded-0"
                                                            aria-label=".form-select-lg example" name="phone"
                                                            id="merchantPhone2">
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Хаяг</label>
                                                        <select class="custom-select rounded-0"
                                                            aria-label=".form-select-lg example" name="address"
                                                            id="merchantAddress">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="display: none;" id="merchantsName">
                                                    <label for="exampleSelectRounded0">Салбарын нэр
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantName"
                                                        placeholder="Илгээгчийн нэр">
                                                </div>
                                                <div class="form-group" style="display: none;" id="merchantsPhone1">
                                                    <label for="exampleSelectRounded0">Утас-1
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantPhone1"
                                                        type="number" oninput="limitInputLength(this, 8)"
                                                        placeholder="Илгээгчийн утас заавал оруулах">
                                                </div>
                                                <div class="form-group" style="display: none;" id="merchantsPhone2">
                                                    <label for="exampleSelectRounded0">Утас-2
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantPhone2"
                                                        oninput="limitInputLength(this, 8)"
                                                        placeholder="Нэмэлт утасны дугаар">
                                                </div>
                                                <div class="form-group" style="display: none;"id="merchantsAddress">
                                                    <label for="exampleSelectRounded0">Дэлгэрэнгүй хаяг
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantAddress"
                                                        placeholder="Илгээгчийн хаяг оруулах">
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label for="exampleSelectRounded0">Салбарын нэр
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantName"
                                                        placeholder="Илгээгчийн нэр">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleSelectRounded0">Утас-1
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantPhone1"
                                                        type="number" id="numberInput" oninput="limitInputLength(this, 8)"
                                                        placeholder="Илгээгчийн утас заавал оруулах">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleSelectRounded0">Утас-2
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantPhone2"
                                                        oninput="limitInputLength(this, 8)"
                                                        placeholder="Нэмэлт утасны дугаар">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleSelectRounded0">Дэлгэрэнгүй хаяг
                                                        <code></code></label>
                                                    <input class="form-control rounded-0" name="merchantAddress"
                                                        placeholder="Илгээгчийн хаяг оруулах">
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Илгээмжийн мэдээлэл</label>
                                                <input type="text" name="parcel_info" class="form-control auto-save"
                                                    id="parcelInfo"
                                                    placeholder="Илгээмжийн нэр төрөл, тоо ширхэг, барааны код, сериал дугаар, овор хэмжээ болон очиж авахдаа анхаарах зүйлсийг бичнэ үү   ">

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Захиалгын код</label>
                                                <input type="text" name="order_code" class="form-control auto-save"
                                                    id="code"
                                                    placeholder="Та өөрийн ( дэлгүүрийн) захилагын кодоо оруулж өгнө үү (Нэг хүлээн авачид олон бараа хүргэх бол)">

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleSelectRounded0">Татан авалтын цаг
                                                    сонгох<code></code></label>
                                                <select class="custom-select rounded-0" name="download_time"
                                                    id="deliver">

                                                    <option value="Өглөө">Өглөө (08-14 цаг)</option>
                                                    <option value="Орой">Үдээс хойш (14-20 цаг)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- right column -->
                    <div class="col-md-6">
                        <div class="card  card-primary">
                            <div class="card-header">
                                <a class=" card-link">
                                    2. Хүлээн авагчийн мэдээлэл бөглөх хэсэг
                                </a>
                            </div>
                            <div>
                                <div class="card-body">
                                    @if (auth()->user()->role != 'customer')
                                        <div class="form-group">
                                            <label for="exampleSelectRounded0">Захиалга <code></code></label>
                                            <select class="custom-select rounded-0" name="shop" id="good">
                                                <?php $user = DB::table('users')
                                                    ->where('role', '=', 'customer')
                                                    ->get(); ?>
                                                @foreach ($user as $users)
                                                    <option value="{{ $users->name }}">{{ $users->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <!-- <div class="form-group">
                                            <label for="exampleInputEmail1">Дэлгүүр</label>
                                            <input type="text" name="shop" class="form-control"
                                                value="{{ auth()->user()->name }}" placeholder="Дэлгүүр" disabled>
                                        </div> -->
                                    @endif
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Хүлээн авагчийн нэр</label> <label
                                            style="color:red;">*</label>
                                        <input type="text" name="receivername" class="form-control auto-save"
                                            placeholder="Жишээ нь: Азжаргал" id="zName">
                                        @error('receivername')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Утас</label><label style="color:red;">*</label>
                                        <input type="text" name="phone" class="form-control"
                                            oninput="limitInputLength(this, 8)" placeholder="Утас">
                                        @error('phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Утас 2</label>
                                        <input type="text" name="phone2" class="form-control"
                                            oninput="limitInputLength(this, 8)" placeholder="Утас 2">
                                        @error('phone2')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Байршил сонгох<code></code></label>
                                        <select class="custom-select rounded-0" name="district">

                                            <option value="Баянгол">Баянгол</option>
                                            <option value="Баянзүрх">Баянзүрх</option>
                                            <option value="Сүхбаатар">Сүхбаатар</option>
                                            <option value="Хан-Уул">Хан-Уул</option>
                                            <option value="Чингэлтэй">Чингэлтэй</option>
                                            <option value="Сонгинохайрхан">Сонгинохайрхан</option>
                                            <option value="Орон нутаг">Орон нутаг</option>
                                            <option value="Бусад">Бусад</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Дэлгэрэнгүй хаяг</label><label
                                            style="color:red;">*</label>
                                        <input type="text" name="address" class="form-control"
                                            placeholder="Жишээ нь Нарны хороолол 1 байр, 9 тоот, 1 орц, орцны код *#1122">
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Нэмэлт тайлбар</label>
                                        <input type="text" name="comment" class="form-control"
                                            placeholder="Жишээ нь: оройн цагаар авах, ирэхээсээ өмнө залгах, нялх хүүхэдтэй гэх мэт...">
                                        @error('comment')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <?php $price = DB::table('settings')
                                        ->limit(4)
                                        ->get(); ?>
                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Хүргэлтийн төрөл сонгох<code></code></label>
                                        <select class="custom-select rounded-0" name="type"
                                            onchange="yesnoCheck(this);">
                                            @foreach ($price as $prices)
                                                <option value="{{ $prices->type }}">{{ $prices->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="ifYes" style="display: none;">
                                        <div style="color:green;"> Яаралтай хүргэлт нь 1-2 цагийн дотор хүргэгдэх
                                            бөгөөд нэмэлт
                                            төлбөртэй болохыг анхаарна уу. </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.col (right) -->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card  card-primary">
                            <div class="card-header">
                                <a class=" card-link">
                                    3. Барааны нэмэлт мэдээлэл
                                </a>
                            </div>
                            <div id="collapseThree">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" style="width:15px;height:15px;" id="checkbox2"
                                                name="check2" value="1" />
                                            <label class="form-check-label" for="checkbox2">Агуулахаас бараа
                                                сонгох</label>
                                        </div>
                                        <div class="autoUpdate1" id="autoUpdate1" style="display:none;">

                                            <div class="mt-3 ">
                                                <label for="regular-form-3" class="form-label proddata">Барааны нэр
                                                </label><br>
                                                <select id="products"
                                                    class="form-control formselect sm:mt-2 sm:mr-2 js-example-basic-single"
                                                    aria-label=".form-select-lg example" name="size"
                                                    style="width:600px;">

                                                    <?php
                                                    if (Auth::user()->role == 'customer') {
                                                        $good = DB::table('goods')
                                                            ->where('user_id', Auth::user()->id)
                                                            ->orderBy('goodname', 'ASC')
                                                            ->get();
                                                    } else {
                                                        $good = DB::table('goods')->get();
                                                    }
                                                    ?>
                                                    <option class="" value="#">Бараа сонгоно уу</option>

                                                    @foreach ($good as $goods)
                                                        <option class="product_id" value="{{ $goods->id }}">
                                                            {{ $goods->goodname }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                           <div class="mt-3">
                                                    <label for="input-state-3" class="form-label">Тоо ширхэг</label>
                                                    <input id="input-state-3" type="number"
                                                        class="qty-input form-control border-danger"
                                                        placeholder="Баглаа боодлын тоо" name="cart_number" style="width:600px;">
                                                </div>
                                            <div class="pro-action">
                                                <button type="button" class="add-to-cart btn btn-primary mt-5"><span>
                                                        Сагсанд
                                                        нэмэх</span> </button>
                                                <div class="row" id="cart_details">

                                                    @if (isset($cart_data))
                                                        @if (Cookie::get('shopping_cart'))
                                                            @php $total="0" @endphp
                                                            <div class="col-md-7 ms-auto">
                                                                <div class="cart-page-header">
                                                                    <h6 class="cart-page-header-title">Order list</h6>
                                                                </div>
                                                                <div class="d-flex flex-column gap-3">

                                                                    @foreach ($cart_data as $data)
                                                                        <label class="order-card col-12 cartpage"
                                                                            data-cart-item-id="123" data-product-id="12">
                                                                            <div class="order-card__body">

                                                                                <input type="hidden" class="product_id"
                                                                                    value="{{ $data['item_id'] }}">
                                                                                <div class="product-row">

                                                                                    <div class="product-row__content">
                                                                                        <h6
                                                                                            class="product-row__content-title">
                                                                                            <div style="width:200px;">
                                                                                                {{ urldecode($data['item_name']) }}
                                                                                            </div>
                                                                                            <div style="display:inline;">
                                                                                                тоо:
                                                                                                {{ number_format($data['item_quantity']) }}
                                                                                            </div>
                                                                                            <div
                                                                                                style="display:inline;margin-left:50px;">
                                                                                                Үнэ:
                                                                                                {{ number_format($data['item_price']) }}
                                                                                            </div>
                                                                                        </h6>
                                                                                        <div class="product-row__tally--price--amount"
                                                                                            style="display:inline;">
                                                                                        </div>
                                                                                        <div
                                                                                            class="product-row__content-author">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="product-row__tally"
                                                                                        style="display:inline;">
                                                                                        <div
                                                                                            class="product-row__tally--price">


                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>


                                                            <div class="col-md-3 me-auto">

                                                                <div class="cart-page__purchase">
                                                                    <div class="cart-page__purchase-lists">
                                                                        @foreach ($cart_data as $data)
                                                                            @php $total = $total + ( $data["item_price"] * $data["item_quantity"] ) @endphp
                                                                        @endforeach

                                                                    </div>
                                                                    <div class="cart-page__purchase-total">
                                                                        <div class="cart-page__purchase-total-item">
                                                                            <div class="span">Total sum:</div>
                                                                            <div class="total-price">₮
                                                                                {{ number_format($total, 2) }}</div>
                                                                        </div>
                                                                    </div>
                                                                    @auth


                                                                    @endauth
                                                                    @guest
                                                                        

                                                                        <div class="d-grid mt-3"><a href="#"><button
                                                                                    class="btn btn-success btn-lg rounded-0"
                                                                                    type="button" data-bs-toggle="modal"
                                                                                    data-bs-target="#modalLogin"
                                                                                    aria-controls="loginToSystem"
                                                                                    aria-logedin="false"
                                                                                    aria-label="Signin">Purchase</button></a>
                                                                        </div>
                                                                    @endguest
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="row">
                                                            <div class="col-md-12 mycard py-5 text-center">
                                                                <div class="mycards">
                                                                    <h4>Таны сагс одоогоор хоосон байна.</h4>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Your Product ID -->
                                            <input type="hidden" class="qty-input" value="1">
                                            <!-- Your Number of Quantity -->

                                        </div>


                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Барааны тооцоо <i
                                                style="font-weight:300 !important;">
                                                (Хэрэв хүлээн авагчаас барааны тооцоо авах бол)</i></label>
                                        <input type="text" name="price" class="form-control" placeholder="Тооцоо" id="priceInput">
                                        @error('price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                   <div class="form-group">
                                        <label for="exampleInputEmail1">Баглаа боодлын тоо</label><label
                                            style="color:red;">*</label>
                                        <input type="text" name="number" class="form-control"
                                            placeholder="Тоо ширхэг" id="mainNumber">
                                        @error('number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                                                        <div class="form-group">
                                        <label for="exampleInputEmail1">Барааны төрөл</label>
                                        <input type="text" name="goodtype" class="form-control"
                                            placeholder="Хувцас, гоо сайхан, хүнс, бичиг хэрэг, тоглоом гэх мэт">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Зураг оруулах</label>
                                        <div class="text-center image-container">
                                            <style>
                                                .image-upload>input {
                                                    display: none;
                                                }
                                            </style>
                                            <img id="imageDisplay" style="max-width: 10%">
                                            <div class="image-upload">
                                                <label for="file-input">
                                                    <img src="https://png.pngtree.com/png-vector/20190425/ourmid/pngtree-camera-icon-vector-illustration-in-line-style-for-any-purpose-png-image_989668.jpg"
                                                        width="40" />
                                                </label>
                                                <input id="file-input" type="file" name="image" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleSelectRounded0">Овор хэмжээ <code></code></label>
                                        <select class="custom-select rounded-0" id="exampleSelectRounded0"
                                            name="size">
                                            <option value="1-5 kg">1-5 kg</option>
                                            <option value="5-10 kg">5-10 kg</option>
                                        </select>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Сэгсэрч болохгүй</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Хагарна</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Хайлна</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Хөлдөнө</label>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer"
                                    style="display: flex; align-items: center; justify-content: right;">
                                    <button type="submit" class="btn btn-primary">Хадгалах</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->


            {!! Form::close() !!}
        </section>
        <!-- /.content -->
    </div>

    <script>
        window.onload = function() {
            // Function to retrieve and populate each field
            function populateField(fieldId) {
                const savedValue = localStorage.getItem(fieldId);
                if (savedValue) {
                    document.getElementById(fieldId).value = savedValue;
                }
            }

            // Select all elements with the class 'auto-save'
            const autoSaveElements = document.querySelectorAll('.auto-save');

            // Iterate over the NodeList and populate each field
            autoSaveElements.forEach(element => {
                populateField(element.id);

                // Add the event listener for auto-save
                element.addEventListener('input', function() {
                    localStorage.setItem(element.id, this.value);
                });
            });
        };

        function limitInputLength(elem, maxLength) {
            if (elem.value.length > maxLength) {
                elem.value = elem.value.slice(0, maxLength);
            }
        }
        
        document.getElementById('toggleCheckbox').addEventListener('change', function() {
            var merchantsName = document.getElementById('merchantsName');
            var merchantsPhone1 = document.getElementById('merchantsPhone1');
            var merchantsPhone2 = document.getElementById('merchantsPhone2');
            var merchantsName = document.getElementById('merchantsName');
            var merchantAddress = document.getElementById('merchantAddress');
            if (this.checked) {
                merchantsName.style.display = 'block';
                merchantsPhone1.style.display = 'block';
                merchantsPhone2.style.display = 'block';
                merchantsAddress.style.display = 'block';
                merchantDivShow.style.display = 'none';
            } else {
                merchantsName.style.display = 'none';
                merchantsPhone1.style.display = 'none';
                merchantsPhone2.style.display = 'none';
                merchantsAddress.style.display = 'none';
                merchantDivShow.style.display = 'block';
            }
        });

        const imageInput = document.getElementById('file-input');
        const imageDisplay = document.getElementById('imageDisplay');

        imageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageDisplay.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                imageDisplay.src = '';
            }
        });

        $(document).ready(function() {
            $(".collapse.show").each(function() {
                $(this)
                    .prev(".card-header")
                    .find(".fa")
                    .addClass("fa-minus")
                    .removeClass("fa-plus");
            });
            $(".collapse")
                .on("show.bs.collapse", function() {
                    $(this)
                        .prev(".card-header")
                        .find(".fa")
                        .removeClass("fa-plus")
                        .addClass("fa-minus");
                })
                .on("hide.bs.collapse", function() {
                    $(this)
                        .prev(".card-header")
                        .find(".fa")
                        .removeClass("fa-minus")
                        .addClass("fa-plus");
                });
        });

        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantPhone1').empty();
                $('#merchantPhone1').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantPhone1').empty();

                        response.forEach(element => {
                            $('#merchantPhone1').append(
                                `<option value="${element['merchantPhone1']}">${element['merchantPhone1']}</option>`
                            );
                        });
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantPhone2').empty();
                $('#merchantPhone2').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantPhone2').empty();

                        response.forEach(element => {
                            $('#merchantPhone2').append(
                                `<option value="${element['merchantPhone2']}">${element['merchantPhone2']}</option>`
                            );
                        });
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#merchantsRequest').on('change', function() {
                let id = $(this).val();
                $('#merchantAddress').empty();
                $('#merchantAddress').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'phone/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#merchantAddress').empty();

                        response.forEach(element => {
                            $('#merchantAddress').append(
                                `<option value="${element['merchantAddress']}">${element['merchantAddress']}</option>`
                            );
                        });
                    }
                });
            });
        });

        $(document).ready(function() {
            // Handle checkbox change
            $('#checkbox2').change(function() {
                if (this.checked) {
                    $('#autoUpdate1').fadeIn('slow');
                    document.getElementById('autoUpdate2').style.display = "none";
                    
                    // When checkbox is checked, sync the values
                    syncCartToForm();
                } else {
                    $('#autoUpdate1').fadeOut('slow');
                    document.getElementById('autoUpdate2').style.display = "block";
                    
                    // When unchecked, clear the synced values
                    $('#mainNumber').val('');
                    $('#priceInput').val('');
                }
            });

            // Sync cart quantity to main number field
            $(document).on('input', '#input-state-3', function() {
                if ($('#checkbox2').is(':checked')) {
                    $('#mainNumber').val($(this).val());
                }
            });

            // When cart is updated, sync the total price
            function syncCartToForm() {
                // Sync quantity
                const cartQuantity = $('#input-state-3').val();
                if (cartQuantity) {
                    $('#mainNumber').val(cartQuantity);
                }
                
                // Sync total price
                const totalPrice = $('.total-price').text().replace('₮', '').trim();
                if (totalPrice && totalPrice !== '0.00') {
                    $('#priceInput').val(totalPrice.replace(/,/g, ''));
                }
            }

            // Update form when cart is modified
            function updateFormFromCart() {
                if ($('#checkbox2').is(':checked')) {
                    syncCartToForm();
                }
            }

            // Modified add-to-cart function to update form after adding item
            $('.add-to-cart').click(function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                var product_id = $("#products option:selected").val();
                var product_name = $("#products option:selected").text();
                var quantity = $('.qty-input').val();

                $.ajax({
                    url: "/add-to-cart",
                    method: "POST",
                    data: {
                        'quantity': quantity,
                        'product_id': product_id,
                        'product_name': product_name,
                    },
                    success: function(response) {
                        console.log(response);
                        $("#cart_details").html(response);
                        cartloadDetails();
                        
                        // Update form fields after cart is updated
                        updateFormFromCart();
                    },
                });
            });

            // Modified cartloadDetails to update form after loading
            function cartloadDetails() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    }
                });
                
                $.ajax({
                    url: '/load-cart-details',
                    method: "GET",
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    success: function(response) {
                        $("#cart_details").html(response);
                        
                        // Update form fields after cart details are loaded
                        updateFormFromCart();
                    }
                });
            }

            // Also sync when the page loads if checkbox is already checked
            if ($('#checkbox2').is(':checked')) {
                syncCartToForm();
            }

            $('#good').on('change', function() {
                let id = $(this).val();
                $('#products').empty();
                $('#products').append(`<option value="0" disabled selected>Processing...</option>`);
                $.ajax({
                    type: 'GET',
                    url: 'good/' + id,
                    success: function(response) {
                        var response = JSON.parse(response);
                        console.log(response);
                        $('#products').empty();

                        response.forEach(element => {
                            $('#products').append(
                                `<option value="${element['id']}">${element['goodname']}</option>`
                            );
                        });
                    }
                });
            });
        });

        function yesnoCheck(that) {
            if (that.value == "Яаралтай") {
                document.getElementById("ifYes").style.display = "block";
            } else {
                document.getElementById("ifYes").style.display = "none";
            }
        }

        // Form submission handling
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                
                // Check if required fields are filled
                let receiverName = $('#zName').val();
                let phone = $('input[name="phone"]').val();
                let address = $('input[name="address"]').val();
                let number = $('#mainNumber').val();
                
                let missingFields = [];
                
                if (!receiverName) missingFields.push('Хүлээн авагчийн нэр');
                if (!phone) missingFields.push('Утас');
                if (!address) missingFields.push('Дэлгэрэнгүй хаяг');
                if (!number) missingFields.push('Баглаа боодлын тоо');
                
                if (missingFields.length > 0) {
                    // Show alertify error message
                    alertify.error('Дараах талбаруудыг бөглөнө үү: ' + missingFields.join(', '));
                    return false;
                }
                
                // If all required fields are filled, submit the form via AJAX
                let formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Show success message
                        alertify.success('Амжилттай хадгалагдлаа!');
                        
                        // Optionally clear the form or redirect
                        // $('form')[0].reset();
                        // localStorage.clear(); // Clear auto-saved data
                        
                        // Redirect after success if needed
                        // window.location.href = '/some-success-page';
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        alertify.error('Алдаа гарлаа: ' + (xhr.responseJSON?.message || 'Дахин оролдоно уу'));
                    }
                });
            });
        });
    </script>
@endsection