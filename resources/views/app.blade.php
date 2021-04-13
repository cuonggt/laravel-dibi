<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="noindex, nofollow">

    <title>Dibi</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset(mix('app.css', 'vendor/dibi')) }}" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="dibi" class="font-sans antialiased h-screen flex overflow-hidden bg-gray-200" v-cloak>
        <div class="flex flex-shrink-0">
            <div class="flex flex-col">
                <div class="flex flex-col h-0 flex-1">
                    <!-- Logo -->
                    <div class="flex items-center h-16 px-4 bg-gray-900 text-xl text-white font-medium">
                        <icon-database size="12" fill="#25C4F2"></icon-database>
                        <div class="ml-2" style="padding-top: 2px;">Dibi</div>
                    </div>

                    <div class="flex-1 flex flex-col overflow-y-auto bg-gray-800">
                        <!-- Tables tabs -->
                        <h3 class="px-3 mt-8 text-xs leading-4 font-semibold text-gray-500 uppercase tracking-wider">
                            Tables
                        </h3>

                        <nav class="px-2 py-4 bg-gray-800">
                            @foreach ($tables as $table)
                            <router-link
                                to="/tables/{{ $table->name }}"
                                active-class="text-white bg-gray-900 focus:outline-none focus:bg-gray-900 transition ease-in-out duration-150"
                                class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
                            >
                                <icon-table size="6" class="mr-3 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"></icon-table>
                                {{ $table->name }}
                            </router-link>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <router-view></router-view>
    </div>

<script>
    window.Dibi = @json($dibiScriptVariables);
</script>

<!-- Scripts -->
<script src="{{ asset(mix('app.js', 'vendor/dibi')) }}"></script>
</body>
</html>
