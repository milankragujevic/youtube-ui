$(function() {
	var root = null;
	var useHash = true;
	var hash = '#!';
	var router = new Navigo(root, useHash, hash);

	function view(target, template, data, callback) {
		$.get(window._path + 'assets/views/' + template + '.html', function(template) {
			let output = Mustache.render(template, data);
			$(target).html(output);
			router.updatePageLinks();
			if (callback) {
				callback(true);
			}
		}).fail(function() {
			if (callback) {
				callback(false);
			}
		});
	}

	function timeSince(timeStamp) {
		var now = new Date(),
			secondsPast = (now.getTime() - timeStamp.getTime()) / 1000;
		if (secondsPast < 60) {
			return parseInt(secondsPast) + 's ago';
		}
		if (secondsPast < 3600) {
			return parseInt(secondsPast / 60) + 'm ago';
		}
		if (secondsPast <= 86400) {
			return parseInt(secondsPast / 3600) + 'h ago';
		}
		if (secondsPast > 86400) {
			day = timeStamp.getDate();
			month = timeStamp.toDateString().match(/ [a-zA-Z]*/)[0].replace(" ", "");
			year = timeStamp.getFullYear() == now.getFullYear() ? "" : " " + timeStamp.getFullYear();
			return 'on ' + day + " " + month + year;
		}
	}

	jQuery.each(["put", "delete"], function(i, method) {
		jQuery[method] = function(url, data, callback, type) {
			if (jQuery.isFunction(data)) {
				type = type || callback;
				callback = data;
				data = undefined;
			}
			return jQuery.ajax({
				url: url,
				type: method,
				dataType: type,
				data: data,
				success: callback
			});
		}
	});

	let isFirefox = window.navigator.userAgent.match(/Firefox/g)

	router.on(function() {
		router.navigate('downloads')
	});

	function showURLinput() {
		$('#the-url-input').show();
	}

	function hideURLinput() {
		$('#the-url-input').hide();
	}

	router.on('downloads', function() {
		showURLinput();
		$('header nav a.active').removeClass('active')
		$('header nav a[href="downloads"]').addClass('active')
		view('#the-content', 'downloads', {}, function() {
			// 
		})
	});

	router.on('mysubscriptions', function() {
		hideURLinput();
		$('header nav a.active').removeClass('active')
		$('header nav a[href="mysubscriptions"]').addClass('active')
		$.get(window._path + 'api/subscriptions/list', function(data) {
			if (data.success && data.subscriptions.length) {
				$.get(window._path + 'api/subscriptions/feed', function(data) {
					if (data.success && data.feed.length) {
						$.each(data.feed, function(i, item) {
							data.feed[i].published_ago = timeSince(new Date(item.published * 1000));
						})
						view('#the-content', 'videos', {
							feed: data.feed
						}, function() {
							// 
						})
					} else {
						swal('Feed', 'Sorry, an error occurred. ', 'error')
					}
				})
			} else {
				view('#the-content', 'no-subscriptions', {}, function() {
					$('#subscriptions-upload-button').off('click').on('click', function() {
						var formData = new FormData();
						formData.append('file', $('#uploadTakeoutYT')[0].files[0]);
						$('#subscriptions-upload-status').show()
						$('#subscriptions-upload-status').html('Uploading...')
						$.ajax({
							url: window._path + 'api/subscriptions/upload',
							type: 'POST',
							data: formData,
							processData: false,
							contentType: false,
							success: function(data) {
								$('#subscriptions-upload-status').html('Fetching...')
								$.post(window._path + 'api/subscriptions/fetch', {}, function(data) {
									$('#subscriptions-upload-status').html('Done.')
								})
							}
						});
					});
				});
			}
		})
	});

	router.resolve();

	if (isFirefox) {
		$('body').addClass('is-firefox')
	} else {
		$('body').removeClass('is-firefox')
	}
})