<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="noindex, nofollow">

    <title>Dibi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="{{ mix('app.css', 'vendor/dibi') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="dibi" class="font-sans antialiased h-screen flex overflow-hidden bg-gray-200" v-cloak>
        <div class="flex shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1">
                    <!-- Logo -->
                    <div class="flex items-center h-16 px-4 bg-gray-900 text-xl text-white font-medium">
                        <icon-database size="12" fill="#25C4F2"></icon-database>
                        <div class="ml-2" style="padding-top: 2px;">Dibi</div>
                    </div>

                    <nav-list :tables="{{ json_encode($tables) }}" />
                </div>
            </div>
        </div>

        <router-view></router-view>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>
    </div>

<script>
    window.Dibi = @json($dibiScriptVariables);
</script>

<!-- Scripts -->
<script src="{{ mix('app.js', 'vendor/dibi') }}"></script>
</body>
</html>
