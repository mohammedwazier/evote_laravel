@extends('template.login')

@section('title', 'Evote Login')

@section('content')
<form class="form-signin" method="POST" action="{{ route('homepage.login.process') }}">
    @csrf
    <div class="text-center mb-4">
        <h1>Evote Login</h1>
    </div>

    <div class="form-label-group">
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required="" autofocus="" autocomplete="off">
        <label for="inputEmail">Email address</label>
    </div>

    <div class="form-label-group">
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="">
        <label for="inputPassword">Password</label>
        <img style="position:absolute; height: 30px; max-width: 100%; right: .5em;top:20%;filter:contrast(.5);cursor: pointer;" class="show_password" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTExLjk5OSA1MTEuOTk5IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTEuOTk5IDUxMS45OTk7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNTA4Ljc0NSwyNDYuMDQxYy00LjU3NC02LjI1Ny0xMTMuNTU3LTE1My4yMDYtMjUyLjc0OC0xNTMuMjA2UzcuODE4LDIzOS43ODQsMy4yNDksMjQ2LjAzNQ0KCQkJYy00LjMzMiw1LjkzNi00LjMzMiwxMy45ODcsMCwxOS45MjNjNC41NjksNi4yNTcsMTEzLjU1NywxNTMuMjA2LDI1Mi43NDgsMTUzLjIwNnMyNDguMTc0LTE0Ni45NSwyNTIuNzQ4LTE1My4yMDENCgkJCUM1MTMuMDgzLDI2MC4wMjgsNTEzLjA4MywyNTEuOTcxLDUwOC43NDUsMjQ2LjA0MXogTTI1NS45OTcsMzg1LjQwNmMtMTAyLjUyOSwwLTE5MS4zMy05Ny41MzMtMjE3LjYxNy0xMjkuNDE4DQoJCQljMjYuMjUzLTMxLjkxMywxMTQuODY4LTEyOS4zOTUsMjE3LjYxNy0xMjkuMzk1YzEwMi41MjQsMCwxOTEuMzE5LDk3LjUxNiwyMTcuNjE3LDEyOS40MTgNCgkJCUM0NDcuMzYxLDI4Ny45MjMsMzU4Ljc0NiwzODUuNDA2LDI1NS45OTcsMzg1LjQwNnoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTI1NS45OTcsMTU0LjcyNWMtNTUuODQyLDAtMTAxLjI3NSw0NS40MzMtMTAxLjI3NSwxMDEuMjc1czQ1LjQzMywxMDEuMjc1LDEwMS4yNzUsMTAxLjI3NQ0KCQkJczEwMS4yNzUtNDUuNDMzLDEwMS4yNzUtMTAxLjI3NVMzMTEuODM5LDE1NC43MjUsMjU1Ljk5NywxNTQuNzI1eiBNMjU1Ljk5NywzMjMuNTE2Yy0zNy4yMywwLTY3LjUxNi0zMC4yODctNjcuNTE2LTY3LjUxNg0KCQkJczMwLjI4Ny02Ny41MTYsNjcuNTE2LTY3LjUxNnM2Ny41MTYsMzAuMjg3LDY3LjUxNiw2Ny41MTZTMjkzLjIyNywzMjMuNTE2LDI1NS45OTcsMzIzLjUxNnoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg==" />
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    <a href="{{ route('homepage.register') }}" class="btn btn-lg btn-secondary btn-block">Register</a>
    <p class="mt-5 mb-3 text-muted text-center">© 2021</p>
</form>
@endsection

@push('js')
<script>
    $(document).ready(function(){
        $('.show_password').on('click', function(){
            let _this = $(this);
            let par = $(_this.parent().find('input'))
            let type = par.attr('type');
            if(type === 'password'){
                par.attr('type', 'text');
            }else{
                par.attr('type', 'password');
            }
        })
    })
</script>
@endpush