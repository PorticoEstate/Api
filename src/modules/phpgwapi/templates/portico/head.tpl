<!DOCTYPE html>
<!-- BEGIN head -->
<html lang="{userlang}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		<meta name="author" content="phpGroupWare http://www.phpgroupware.org">
		<meta name="description" content="phpGroupWare">
		<meta name="keywords" content="phpGroupWare">
		<meta name="robots" content="none">
		<title>{site_title}</title>
		<link rel="icon" href="{img_icon}" type="image/x-ico">
		<link rel="shortcut icon" href="{img_icon}">
		<!-- BEGIN stylesheet -->
        	<link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->

		{css}

		<script>
		<!--
			var strBaseURL = '{str_base_url}';
			{win_on_events}
		//-->
		</script>
		{javascript}
		<script>
		<!--
			var border_layout_config = {border_layout_config};
			var navbar_config = {navbar_config};
			var noheader = {noheader};
			var nofooter = {nofooter};
			var menu_selection = '{menu_selection}';
			var sessionid = '{sessionid}';
		//-->
		</script>
		<!-- BEGIN javascript -->
		<script src="{javascript_uri}"></script>
    	<!-- END javascript -->
		<script>
		// Helper function
			let domReady = (cb) => {
			document.readyState === 'interactive' || document.readyState === 'complete'
				? cb()
				: document.addEventListener('DOMContentLoaded', cb);
			};

			domReady(() => {
			// Display body when DOM is loaded
			document.body.style.visibility = 'visible';
			});
		</script>
	</head>
<!-- END head -->
