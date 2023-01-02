<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sign In</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link href="https://getbootstrap.com/docs/5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.0/examples/sign-in/signin.css" rel="stylesheet">
    <!-- Style -->
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(60, 12, 60, 0.1);
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
            border-top: 4px #3c0c3c solid;
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

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .form-control:focus {
            border-color: #3c0c3c;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(255, 100, 255, 0.5);
        }

    </style>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
</head>

<body class="text-center">
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>
    <main class="form-signin">

        <form id="signinform">
            {{ Html::image(asset('img/norrbudet-min.png'), 'logo') }}
            <h1 class="h3 mb-3 fw-bold mx-auto" style="color: #3c0c3c">Please sign in</h1>
            <span id="form_result"></span>

            <label for="inputEmail" class="visually-hidden">Email address</label>
            <input type="email" id="inputEmail" name="email" class="form-control mb-2" placeholder="Email address"
                required autofocus>
            <label for="inputPassword" class="visually-hidden">Password</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password"
                required>
            <button id="signin" class="w-100 btn btn-lg" type="button"
                style="background-color: #fccc0b;color: #3c0c3c">Sign in</button>
        </form>
    </main>
</body>

</html>

<script>
    $(document).ajaxSend(function() {
        $("#overlay").fadeIn(300);
    });
    $('#inputEmail , #inputPassword').keypress(function(e) {
        var key = e.which;
        if (key == 13) // the enter key code
        {
            $('#signin').trigger('click');
        }
    });
    $('#signin').on('click', function(e) {
        e.preventDefault();

        var form = $('#signinform');
        $.ajax({
            url: "/api/login",
            type: "POST",
            data: form.serialize(),
            success: function(data) {
                if (data.role == 'customer') {
                    html = '<div class="alert alert-danger">';
                    html += '<p>Customer can not login!</p>'
                    html += '</div>';
                    $('#form_result').html(html);

                } else {
                    html = '<div class="alert alert-success">';
                    html += '<p>Login success</p>'
                    html += '</div>';
                    $('#form_result').html(html);
                    Cookies.set('token', data.token);
                    if (data.role == 'admin' || data.role == 'editor') {
                        window.location.href = "/bookings?status=0";
                    } else if (data.role == 'driver' || data.role == 'driver_manager') {
                        window.location.href = "/calendar";
                    }
                }
            },
            error: function(data) {
                if (data.responseJSON.message) {
                    html = '<div class="alert alert-danger">';
                    html += '<p>' + data.responseJSON.message + '</p>'
                    html += '</div>';
                    $('#form_result').html(html);
                }
            }
        }).done(function() {
            setTimeout(function() {
                $("#overlay").fadeOut(300);
            }, 500);
        }).fail(function() {
            setTimeout(function() {
                $("#overlay").fadeOut(300);
            }, 500);
        });
    });
</script>
