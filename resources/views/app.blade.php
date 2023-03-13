<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>




    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Favicons -->
    <link rel="apple-touch-icon" href="https://getbootstrap.com/docs/5.0/assets/img/favicons/apple-touch-icon.png"
        sizes="180x180">
    <link rel="manifest" href="https://getbootstrap.com/docs/5.0/assets/img/favicons/manifest.json">
    <link rel="mask-icon" href="https://getbootstrap.com/docs/5.0/assets/img/favicons/safari-pinned-tab.svg"
        color="#7952b3">
    <link rel="icon" href="{{ asset('img/favicon.jpg') }}">
    <meta name="theme-color" content="#7952b3">

    <style>
        .modal-confirm {
            color: #636363;
            width: 400px;
        }

        .modal-confirm .modal-content {
            padding: 20px;
            border-radius: 5px;
            border: none;
            text-align: center;
            font-size: 14px;
        }

        .modal-confirm .modal-header {
            border-bottom: none;
            position: relative;
        }

        .modal-confirm h4 {
            text-align: center;
            font-size: 26px;
            margin: 30px 0 -10px;
        }

        .modal-confirm .close {
            position: absolute;
            top: -5px;
            right: -2px;
        }

        .modal-confirm .modal-body {
            color: #999;
        }

        .modal-confirm .modal-footer {
            border: none;
            text-align: center;
            border-radius: 5px;
            font-size: 13px;
            padding: 10px 15px 25px;
        }

        .modal-confirm .modal-footer a {
            color: #999;
        }

        .modal-confirm .icon-box {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            z-index: 9;
            text-align: center;
            border: 3px solid #f15e5e;
        }

        .modal-confirm .icon-box i {
            color: #f15e5e;
            font-size: 46px;
            display: inline-block;
            margin-top: 13px;
        }

        .modal-confirm .btn,
        .modal-confirm .btn:active {
            color: #fff;
            border-radius: 4px;
            background: #60c7c1;
            text-decoration: none;
            transition: all 0.4s;
            line-height: normal;
            min-width: 120px;
            border: none;
            min-height: 40px;
            border-radius: 3px;
            margin: 0 5px;
        }

        .modal-confirm .btn-secondary {
            background: #c1c1c1;
        }

        .modal-confirm .btn-secondary:hover,
        .modal-confirm .btn-secondary:focus {
            background: #a8a8a8;
        }

        .modal-confirm .btn-danger {
            background: #f15e5e;
        }

        .modal-confirm .btn-danger:hover,
        .modal-confirm .btn-danger:focus {
            background: #ee3535;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }

        @media (max-width: 767px) {
            .btn-margin {
                margin-top: 3px;
            }
        }

    </style>


    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/5.0/examples/dashboard/dashboard.css" rel="stylesheet">

    @yield('css')
</head>

<body>

    <header class="navbar navbar sticky-top flex-md-nowrap p-0 shadow" style="background-color: #3a0a3c ">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3"
            href="#">{{ Html::image('img/hoopy-transfer-admin-logo.png', 'logo', ['class' => 'img-fluid', 'width' => 125, 'height' => 200]) }}</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="/logout">Sign out</a>
            </li>
        </ul>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse"
                style="background-color: rgba(253, 206, 8, 0.1) ">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        @if (trim($__env->yieldContent('role') == 'admin') || trim($__env->yieldContent('role') == 'editor'))
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/bookings">
                                    <span data-feather="home"></span>
                                    Bookings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-success" id="count" aria-current="page"
                                    href="/bookings?status=0">
                                    &emsp;- Waiting for Booking({{ App\Models\Booking::getCount(0) }})
                                </a>
                                <a class="nav-link " aria-current="page" href="/bookings?status=1">
                                    &emsp;- Trip is expected({{ App\Models\Booking::getCount(1) }})
                                </a>
                                <a class="nav-link" aria-current="page" href="/bookings?status=2">
                                    &emsp;- Waiting for Confirmation({{ App\Models\Booking::getCount(2) }})
                                </a>
                                <a class="nav-link" aria-current="page" href="/bookings?status=3">
                                    &emsp;- Trip is completed({{ App\Models\Booking::getCount(3) }})
                                </a>
                                <a class="nav-link" aria-current="page" href="/bookings?status=4">
                                    &emsp;- Trip is not Completed({{ App\Models\Booking::getCount(4) }})
                                </a>
                                <a class="nav-link" aria-current="page" href="/bookings?status=5">
                                    &emsp;- Canceled by Customer({{ App\Models\Booking::getCount(5) }})
                                </a>
                                <a class="nav-link" aria-current="page" href="/bookings?status=6">
                                    &emsp;- Canceled by System({{ App\Models\Booking::getCount(6) }})
                                </a>
                            </li>

                        @endif
                        @if (trim($__env->yieldContent('role') == 'admin'))
                            <li class="nav-item ">
                                <a class="nav-link active" href="/prices">
                                    <span data-feather="dollar-sign"></span>
                                    Price List
                                </a>
                            </li>
                        @endif
                        @if (trim($__env->yieldContent('role') == 'admin') || trim($__env->yieldContent('role') == 'driver') || trim($__env->yieldContent('role') == 'driver_manager'))
                            <li class="nav-item ">
                                <a class="nav-link active" href="/calendar">
                                    <span data-feather="calendar"></span>
                                    Calendar
                                </a>
                            </li>
                        @endif
                        @if (trim($__env->yieldContent('role') == 'admin') || trim($__env->yieldContent('role') == 'driver_manager'))
                            <li class="nav-item ">
                                <a class="nav-link active" href="/drivers">
                                    <span data-feather="truck"></span>
                                    Drivers
                                </a>
                            </li>
                        @endif

                    </ul>
                </div>
            </nav>
            <div id="overlay">
                <div class="cv-spinner">
                    <span class="spinner"></span>
                </div>
            </div>
            @yield('content')
        </div>
    </div>

    <script>
        var role = '{{ trim($__env->yieldContent('role')) }}';
    </script>
    <script src="https://getbootstrap.com/docs/5.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://getbootstrap.com/docs/5.0/examples/dashboard/dashboard.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fileDownload/1.4.2/jquery.fileDownload.min.js"></script>


</body>

</html>

@yield('script')
