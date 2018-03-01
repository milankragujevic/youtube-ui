<!DOCTYPE html>
<html lang="en">
<head>
	<title>YouTube Downloader</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="assets/app.css?v=<?php echo time(); ?>" />
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://unpkg.com/navigo@6.0.2/lib/navigo.min.js"></script>
	<script src="https://unpkg.com/mustache@2.3.0/mustache.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2"></script>
	<script src="https://momentjs.com/downloads/moment.js"></script>
	<script>window._path = '<?php echo $path; ?>'</script>
	<script src="assets/app.js?v=<?php echo time(); ?>"></script>
</head>
<body>
	<header>
		<section class="container">
			<div class="branding">
				<a href="#" data-navigo>YouTube</a>
			</div>
			<div class="beta">BETA</div>
			<div class="spacing"></div>
			<nav>
				<a href="downloads" class="downloads active" data-navigo>Downloads</a>
				<a href="mysubscriptions" class="mysubscriptions" data-navigo>My Subscriptions</a>
			</nav>
		</section>
	</header>
	<main>
		<section class="url-form" id="the-url-input">
			<span>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link">
					<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
					<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
				</svg>
			</span>
			<input type="url" placeholder="https://youtube.com/watch?v=XXXXXXXXX" />
		</section>
		<div id="the-content"></div>
	</main>
	<footer>
		<p>&copy; 2018 Milan Kragujević and contributors. <a href="https://github.com/milankragujevic/youtube-ui" target="_blank">Fork me on Github!</a></p>
	</footer>
</body>
</html>