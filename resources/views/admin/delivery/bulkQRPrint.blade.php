@extends('admin.master')

@section('mainContent')
    <style>
        .qr {
            right: 10px;
            position: absolute;
            top: 0;
        }

        @media print {

            body {
                -webkit-print-color-adjust: exact !important;
            }

            html body {
                -webkit-font-smoothing: antialiased;
                font-family: 'Rubik', sans-serif;
                font-size: .875rem;
                /* line-height: 1.25rem; */
                overflow-x: hidden;
                padding: 0.75rem 2rem;
                --tw-text-opacity: 1;
                color: #000;
            }
        }
    </style>

    <div class="text-center">

        <button class="btn btn-primary shadow-md mr-2 mt-10" id="__btnPrint" style="color:black;">Print </button>
    </div>

    <hr>
    <div id="print_wrapper" hidden1>
        <?php $i = 0;
        $float = ''; ?>
        @foreach ($dataQR as $key => $row)
            <?php
            if ($i % 2 == 0) {
                $float = 'float: right;';
            } else {
                $float = 'float: left;';
            }
            ?>
            <div class='qr_details'
                style=" {{ $float }} width: 48%; height:25%; margin-top: 47px;position: relative;margin-left:12px;border: 2px solid blue;
  border-radius: 25px; padding: 20px 20px 20px 20px">
                @php
                    $qr_details = $row->id;
                @endphp
                <h2>ХҮРГЭЛТИЙН МЭДЭЭЛЭЛ</h2>
                <div class="font-medium text-base">Tracking number - {{ $row->track }}</div>
                <div class="font-medium text-base" style="font-size:14px;font-weight:bold;">Илгээгч</div>
                <div style="float:left;" class="grid grid-cols-4 gap-1 gap-y-0 mt-0">
                    <div class="col-span-12 sm:col-span-6">
                        {{ $row->shop }}
                    </div>
                    <!-- Энэ хэсэгт мерчант нэр нэмж оруулах-->
                    {{ $row->merchantName }}
                    <div class="col-span-12 sm:col-span-6">
                        <label for="input-wizard-1" class="form-label" style="font-weight:bold;">Хүлээн авагч:</label>
                        {{ $row->receivername }}
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label for="input-wizard-1" class="form-label" style="font-weight:bold;">Утас:</label>
                        {{ $row->phone, $row->phone2 }}
                    </div>
                    <!-- <div class="col-span-12 sm:col-span-6">
                            <label for="input-wizard-1" class="form-label" style="font-weight:bold;">Утасны дугаар 2:</label>
                            {{ $row->phone2 }}
                        </div> -->
                    <!-- <div class="col-span-12 sm:col-span-6">
                            <label for="input-wizard-1" class="form-label" style="font-weight:bold;">Tracking:</label>
                            {{ $row->track }}
                        </div> -->
                    <div class="col-span-12 sm:col-span-6" style="height:30px;">
                        <label for="input-wizard-5" class="form-label" style="font-weight:bold;">Дэлгэрэнгүй хаяг:</label>
                        {{ substr($row->address, 0, 90) }}
                    </div>
                    <br>

                </div>
                <img class="qr" style="position: absolute;right:10px; top:10px;"
                    src="data:image/png;base64, {!! base64_encode(
                        QrCode::format('png')->size(100)->generate($qr_details),
                    ) !!} ">
            </div>
            <?php $i++; ?>
        @endforeach
    </div>



    <script type="text/javascript">
        // Handle to Print Data
        function printData(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;
            //Reset the page's HTML with div's HTML only
            document.body.innerHTML =
                "<html><head><style type='text/css'>" +
                ".form-label {display: inline-block;margin-bottom: 0;}" +
                " html{background-color: white;    color: #000 !important; overflow: visible !important;background-repeat: repeat !important;float: left;width: 100%; }" +
                " body{background-color: white;background-attachment: unset !important;background-repeat: repeat !important;float: left;width: 100%;color: #000 !important; }" +
                " .qr_details {float: left; width: 48%; height:25%; margin-top: 25px;position: relative;}" +
                ".qr {position: absolute;right: 0; top:0;}" +
                " </style>" +
                "<title></title></head><body>" +
                divElements + "</body>";
            //Print Page
            window.print();
            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        $(document).on('click', '#__btnPrint', function() {
            printData("print_wrapper");
            window.location = '/delivery/new';
        });
    </script>
@endsection
