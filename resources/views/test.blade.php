<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    @livewireStyles



    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Favicons -->
    <link rel="icon" href="{{ asset('img/favicon.jpg') }}">


    <!-- App css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />

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
            z-index: 10000;
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
                margin-top: 15px;
            }
        }

        .modal-backdrop {
            display: none;
        }

        .modal {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>


    <!-- Custom styles for this template -->
    <!--<link href="https://getbootstrap.com/docs/5.0/examples/dashboard/dashboard.css" rel="stylesheet">-->

    @yield('css')

</head>


<body class="loading"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>
    <div class="wrapper">
        <div class="leftside-menu">
            <a href="/bookings" class="logo text-center logo-light">
                <span class="logo-lg">
                    {{ Html::image('img/hoopy-transfer-admin-logo-light.png', 'logo', ['class' => 'img-fluid', 'width' => 175, 'height' => 16]) }}
                </span>
                <span class="logo-sm">
                    {{ Html::image('img/icon-sm.png', 'logo', ['height' => 26]) }}
                </span>
            </a>

            <div class="h-100" id="leftside-menu-container" data-simplebar>

                <!--- Sidemenu -->
                <ul class="side-nav">
                    @if (trim($__env->yieldContent('role') == 'Admin') || trim($__env->yieldContent('role') == 'Editor'))
                        <li class="side-nav-title side-nav-item">Reservation</li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin') || trim($__env->yieldContent('role') == 'Editor'))
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false"
                                aria-controls="sidebarDashboards" class="side-nav-link">
                                <i class="uil-home-alt"></i>
                                <span> Bookings </span>
                            </a>
                            <div class="collapse" id="sidebarDashboards">
                                <ul class="side-nav-second-level">
                                    <li>
                                        <a href="/bookings">All Bookings</a>
                                    </li>
                                    @foreach (App\Models\Booking::getAllStatus() as $status)
                                        <li>
                                            <a id="count{{ $loop->index }}" aria-current="page"
                                                href="/bookings?status={{ $loop->index }}">
                                                {{ App\Models\Booking::getCount($loop->index) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin') ||
                            trim($__env->yieldContent('role') == 'Driver') ||
                            trim($__env->yieldContent('role') == 'DriverManager'))
                        <li class="side-nav-item">
                            <a href="/calendar" class="side-nav-link">
                                <i class="uil-calender"></i>
                                <span>Calendar</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/customers" class="side-nav-link">
                                <i class="uil-user"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin') ||
                            trim($__env->yieldContent('role') == 'Driver') ||
                            trim($__env->yieldContent('role') == 'DriverManager'))
                        <li class="side-nav-title side-nav-item">Services</li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/vehicles" class="side-nav-link">
                                <i class="uil-truck"></i>
                                <span>Vehicles</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin') || trim($__env->yieldContent('role') == 'DriverManager'))
                        <li class="side-nav-item">
                            <a href="/drivers" class="side-nav-link">
                                <i class="uil-user"></i>
                                <span>Drivers</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/prices" class="side-nav-link">
                                <i class="uil-dollar-alt"></i>
                                <span>Services & Prices</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/stations" class="side-nav-link">
                                <i class="mdi mdi-bus-stop"></i>
                                <span>Stations</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-title side-nav-item">Accounting</li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/accounting" class="side-nav-link">
                                <i class="mdi mdi-safe"></i>
                                <span>Kasa</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-title side-nav-item">System</li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/settings" class="side-nav-link">
                                <i class="mdi mdi-cogs"></i>
                                <span>Setting</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/users" class="side-nav-link">
                                <i class="uil-user"></i>
                                <span>Users</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/logs" class="side-nav-link">
                                <i class="uil-newspaper"></i>
                                <span>Logs</span>
                            </a>
                        </li>
                    @endif
                    @if (trim($__env->yieldContent('role') == 'Admin'))
                        <li class="side-nav-item">
                            <a href="/contracts" class="side-nav-link">
                                <i class="uil-newspaper"></i>
                                <span>Contracts</span>
                            </a>
                        </li>
                    @endif
                </ul>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                <div class="navbar-custom">
                    <ul class="list-unstyled topbar-menu float-end mb-0">
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="account-user-avatar">
                                    <img src="{{ asset('img/settings.png') }}" alt="user-image"
                                        class="rounded-circle">
                                </span>
                            </a>
                            <div
                                class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">

                                <!-- item-->
                                <a id='accBtn' href="#" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <span>My Account</span>
                                </a>
                                <!-- item-->
                                <a href="/logout" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout me-1"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>

                    </ul>
                    <button class="button-menu-mobile open-left">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>

                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">@yield('title') </h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    @if (!empty($accounting))
                        @yield('content')
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            @yield('content')
                                        </div> <!-- end row -->
                                    </div> <!-- end card body-->
                                </div> <!-- end card -->
                                <!-- Add New Event MODAL -->
                            </div>
                            <!-- end col-12 -->
                        </div> <!-- end row -->
                    @endif


                </div> <!-- container -->

            </div> <!-- content -->
            <div class="modal top fade" id="acc_modal" tabindex="-1" aria-labelledby="edit_modal_label"
                aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-lg ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edit_modal_label">My Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <span id="acc_result"></span>
                            <form id="acc_form" method="GET">
                                <div class="row ">
                                    <div class="col-md-6">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="acc_email">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="phone" id="acc_phone">
                                    </div>

                                </div>
                                <hr class="mt-2 mb-3" />
                                <div class="row ">

                                    <div class="col-md-6">
                                        <label for="password">New Password</label>
                                        <input type="password" class="form-control" name="password"
                                            id="acc_password">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirm">Password Again</label>
                                        <input type="password" class="form-control" name="password_confirm">
                                    </div>

                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn btn-primary" id="accSubmitBtn">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © HoopyTransfer
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->


    <!-- bundle -->
    <script src="{{ asset('js/vendor.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js'></script>

    @yield('script')

    <script>
        $('#accBtn').on('click', function() {

            $.ajax({
                url: "/api/getAcc",
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                },
                success: function(data) {
                    console.log(data);
                    $('#acc_phone').val(data.phone);
                    $('#acc_email').val(data.email);
                    $('#acc_modal').modal('show');
                }
            });
        });
        $('#acc_form').validate({
            rules: {
                password_confirm: {
                    equalTo: "#acc_password"
                }
            }
        });
        $('#accSubmitBtn').on('click', function() {
            if ($("#acc_form").valid()) {
                var form = $('#acc_form').serialize();
                $.ajax({
                    url: '/api/updateAcc',
                    type: 'POST',
                    data: form,
                    success: function(data) {
                        html = '<div class="alert alert-success">';
                        html += '<p>Save Success</p>'
                        html += '</div>';
                        $('#acc_result').html(html);
                    },
                    error: function(data) {
                        if (data.responseJSON.message) {
                            html = '<div class="alert alert-danger">';
                            html += '<p>' + data.responseJSON.message + '</p>'
                            html += '</div>';
                            $('#modal_result').html(html);
                        }
                    }
                })
            }
        });
        var role = '{{ trim($__env->yieldContent('role')) }}';
    </script>
    @livewireScripts
</body>

</html>
