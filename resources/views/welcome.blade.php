<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            :root {
                --background: #ffffff;
                --text: #24303a;
                --link: #254d72;
                --focus: #005fcc;
            }

            html, body {
                background-color: var(--background);
                color: var(--text);
                font-family: 'Nunito', system-ui, sans-serif;
                line-height: 1.5;
                font-weight: 400;
                min-height: 100vh;
                margin: 0;
            }

            .full-height {
                min-height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                padding: 1rem;
            }

            .title {
                font-size: clamp(2.5rem, 10vw, 5.25rem);
                font-weight: 600;
                line-height: 1.15;
            }

            .links > a {
                color: var(--link);
                display: inline-block;
                padding: .65rem .8rem;
                font-size: .875rem;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .links > a:hover { text-decoration: underline; }
            .links > a:focus-visible {
                outline: 3px solid var(--focus);
                outline-offset: 3px;
                border-radius: 3px;
                text-decoration: underline;
            }
            @media (max-width: 600px) {
                .top-right { position: static; padding: .75rem 0 0; }
                .flex-center { flex-direction: column; }
                .links > a { padding: .65rem .5rem; }
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    --background: #111827;
                    --text: #f3f4f6;
                    --link: #bfdbfe;
                    --focus: #fbbf24;
                }
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <nav class="top-right links" aria-label="Account">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif

            <main id="main" class="content">
                <h1 class="title m-b-md">Laravel</h1>

                <nav class="links" aria-label="Laravel resources">
                    <a href="https://laravel.com/docs">Docs</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://blog.laravel.com">Blog</a>
                    <a href="https://nova.laravel.com">Nova</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </nav>
            </main>
        </div>
    </body>
</html>
