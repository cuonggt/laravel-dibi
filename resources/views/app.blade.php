<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('/vendor/dibi/img/favicon.png') }}">

    <meta name="robots" content="noindex, nofollow">

    <title>Dibi</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset(mix('app.css', 'vendor/dibi')) }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="dibi" v-cloak>
    <div class="container-fluid mb-5">
        <div class="d-flex align-items-center py-4 header">
            <a href="{{ config('dibi.path') }}" class="d-flex" style="text-decoration: none;">
                <img class="logo" src="{{ asset('/vendor/dibi/img/logo.png') }}" />

                <h4 class="mb-0 ml-3"><strong>Dibi</strong></h4>
            </a>
        </div>

        <div class="row mt-4">
            <div class="col-2 sidebar">
                <ul class="nav flex-column">
                    @foreach ($tables as $table)
                    <li class="nav-item">
                        <router-link active-class="active" to="/tables/{{ $table->name }}" class="nav-link d-flex align-items-center pt-0">
                            <img src="{{ asset('/vendor/dibi/img/table.svg') }}" />
                            <span>{{ $table->name }}</span>
                        </router-link>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-10">
                @if (! $assetsAreCurrent)
                    <div class="alert alert-warning">
                        The published Dibi assets are not up-to-date with the installed version. To update, run:<br/><code>php artisan dibi:publish</code>
                    </div>
                @endif

                <router-view />
            </div>
        </div>
    </div>
</div>

<script>
    window.Dibi = @json($dibiScriptVariables);
</script>

<!-- Scripts -->
<script src="{{ asset(mix('app.js', 'vendor/dibi')) }}"></script>
</body>
</html>
