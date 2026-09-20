<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Indococo - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::to('/') }}/assets/images/favicon.ico">

    <!-- App css -->
    <link href="{{ URL::to('/') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <style>
        .account-pages {
            position: relative;
        }

        .account-pages .card {
            width: 540px;
            height: 100vh;
            margin-bottom: 0;
        }

    </style>
</head>

<body class="account-pages">

    <!-- Begin page -->
    <div class="card shadow-none mx-auto">
        <div class="card-block my-auto">

            <div class="account-box">

                <div class="card-box shadow-none p-4 mt-2">
                    <h2 class="text-uppercase text-center pb-3">
                        <a href="#" class="text-success">
                            <span><img src="{{ url('assets/images/logo-arlion.png') }}" alt="" height="50"></span>
                        </a>
                    </h2>

                    <form id="form-login" class="parsley-examples" action="{{ route('auth.authenticate') }}"
                        method="post">
                        @csrf

                        <div class="form-group row">
                            <div class="col-12 validate-input">
                                <label for="username">Username</label>
                                <input class="form-control" type="username" id="username" name="username"
                                    value="{{ old('username') }}" required>
                                @error('username')
                                    <span class="help-block">
                                        <mdall>{{ $message }}</mdall>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12 validate-input">
                                <label for="password">Password</label>
                                <input class="form-control" type="password" required id="password"
                                    placeholder="Enter your password" name="password">
                                @error('password')
                                    <span class="help-block">
                                        <mdall>{{ $message }}</mdall>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row text-center">
                            <div class="col-12">
                                <button type="submit" class="btn btn-block btn-primary waves-effect waves-light"
                                    id="submit-login">
                                    Sign In</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
            <div class="text-center">
                <p class="account-copyright">
                    2023 &copy; Indococo by <a href="">PT. D&W International</a>
                </p>
            </div>
        </div>
    </div>


    <!-- Vendor js -->
    <script src="{{ URL::to('/') }}/assets/js/vendor.min.js"></script>

    <!-- Plugin js-->
    <script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>

    <!-- Validation init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>

    <!-- App js -->
    <script src="{{ URL::to('/') }}/assets/js/app.min.js"></script>

    <script>
        $(function() {
            // $('#submit-login').click(function (e) {
            //     e.preventDefault();

            //     console.log($('#form-login').serialize())

            //     $.ajax({
            //         type: 'POST',
            //         url: $('#form-login').attr('action'),
            //         data: $('#form-login').serialize(),
            //         success: function (data) {
            //             // Handle success response
            //             $('.validate-input').children('.help-block').remove()
            //         },
            //         error: function (xhr, status, error) {
            //             // Handle error response
            //             let response = xhr.responseJSON;

            //             if (!$.isEmptyObject(response)) {
            //                 let errorFields = Object.keys(response.errors);

            //                 $.each(response.errors, function (key, value) {
            //                     $('#' + key).siblings('.help-block').remove()

            //                     if (errorFields.includes(key)) {
            //                         $('#' + key).parent().append(
            //                             '<span class="help-block"><mdall>' + value + '</mdall></span>');
            //                     }
            //                 });
            //             }
            //         }
            //     });
            // });
        });
    </script>

</body>

</html>
