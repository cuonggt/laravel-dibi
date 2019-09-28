<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dibi</title>
</head>

<body>
    <div id="dibi" v-cloak>
        <app />
    </div>

    <script>
        window.config = {
            perPage: {{ config('dibi.limit', 100) }}
        };
    </script>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js', 'vendor/dibi') }}"></script>
    <script src="{{ mix('js/vendor.js', 'vendor/dibi') }}"></script>
    <script src="{{ mix('js/app.js', 'vendor/dibi') }}"></script>
</body>
</html>
