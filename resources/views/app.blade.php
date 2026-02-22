<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="noindex, nofollow" />

    <!-- Title -->
    <title>Dibi</title>

    <!-- Style sheets -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {!! \Cuonggt\Dibi\Dibi::viteAssets() !!}
</head>

<body>
    <div id="dibi"></div>

    <script>
        window.Dibi = @json($dibiScriptVariables);
    </script>
</body>
</html>
