<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Vé xe</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-sm {
            font-size: 14px;
        }

        .text-xs {
            font-size: 12px;
        }

        .border {
            border: 1px solid #000;
        }

        .border-b {
            border-bottom: 1px solid #000;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .w-full {
            width: 100%;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
    </style>
</head>

<body class="px-4 py-2">

    <div class="border px-4 py-2 w-full">
        <div class="text-center font-bold text-sm mb-2">
            CÔNG TY XE NHÀ MÌNH <br>
            <span class="text-xs font-normal">Vé điện tử - Xe {{$number}}</span>
        </div>

        <div class="grid grid-cols-2 text-sm mb-2">
            <div>
                <p><span class="font-bold">Tên hành khách:</span> {{ $name }}</p>
                <p><span class="font-bold">SĐT:</span> {{ $phone }}</p>
                <p><span class="font-bold">CCCD:</span> {{ $cccd }}</p>
            </div>
            <div>
                <p><span class="font-bold">Tuyến:</span> {{ $route }}</p>
                <p><span class="font-bold">Ngày đi:</span> {{ $date }}</p>
                <p><span class="font-bold">Giờ khởi hành:</span> {{ $hour ?? '06:30' }}</p>
            </div>
        </div>

        <div class="border-b mb-2"></div>

        <div class="text-sm mb-2">
            <p><span class="font-bold">Nơi đón:</span> {{ $pickup }}</p>
            <p><span class="font-bold">Nơi trả:</span> {{ $dropoff }}</p>
        </div>

        <div class="text-xs text-center mt-4">
            Xin vui lòng có mặt trước giờ khởi hành 15 phút. <br>
            Quý khách giữ vé để đối chiếu khi cần thiết.
        </div>
    </div>

</body>

</html>