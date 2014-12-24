<!DOCTYPE html>
<html lang="zh">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			绵羊墙
			@show
		</title>

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- CSS
		================================================== -->
        <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap-theme.min.css')}}">

		<style>
        body {
            padding: 70px 0;
			font-family: "Microsoft YaHei", "Arial", "Verdana", "Tahoma";
			color: #424242;
        }
		@media (min-width: 1024px) {
			.container {
				max-width: 1024px;
			}
		}
		h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
			font-family: "Microsoft YaHei UI", "Microsoft YaHei", "Arial", "Verdana", "Tahoma";
			font-weight: 500;
		}
		@section('styles')
		@show
		</style>
		<script src="{{ asset('js/jquery.min.js') }}"></script>
		<script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
        @yield('scripts')

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Favicons
		================================================== -->
	</head>

	<body>
		<!-- To make sticky footer need to wrap in a div -->
		<div id="wrap">
		<!-- Navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			 <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
					<a class="navbar-brand" href="{{{ URL::to('') }}}"><b>绵羊墙</b></a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
						<li {{ (Request::is('/') ? ' class="active"' : '') }}><a href="{{{ URL::to('') }}}">会话劫持</a></li>
						<li {{ (Request::is('wifiphish') ? ' class="active"' : '') }}><a href="{{{ URL::to('wifiphish') }}}">WIFI钓鱼</a></li>
						<li {{ (Request::is('cmccphish') ? ' class="active"' : '') }}><a href="{{{ URL::to('cmccphish') }}}">CMCC账号钓鱼</a></li>
					</ul>
					<!-- ./ nav-collapse -->
				</div>
			</div>
		</div>
		<!-- ./ navbar -->

		<!-- Container -->
		<div class="container">
			<!-- Content -->
			@yield('content')
			<!-- ./ content -->
		</div>
		<!-- ./ container -->

		<!-- the following div is needed to make a sticky footer -->
		<div id="push"></div>
		</div>
		<!-- ./wrap -->
	</body>
</html>
