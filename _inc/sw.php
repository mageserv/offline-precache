<?php
require_once( OFFLINE_PRECACHE__PLUGIN_DIR . 'class.offlineprecacheadmin.php' );
header("HTTP/1.1 200 OK");
header("Content-Type: application/javascript");
 ?>
	'use strict';

	// Import and initialise external dependencies
	// #####################################

<?php
$externalScripts = [
	plugin_dir_url( __FILE__ ).'js/lib/workbox-sw.prod.v1.0.1.js'
];
?>
<?php if (esc_attr(get_option('offline_precache_enabled_ga'))){
	$externalScripts[] = plugin_dir_url( __FILE__ ).'js/lib/workbox-google-analytics.prod.v1.0.0.js';
}?>
	importScripts(<?php echo implode(", ", array_map(function ($url) { return "'{$url}'"; }, $externalScripts)) ?>);

<?php if (esc_attr(get_option('offline_precache_enabled_ga'))): ?>
	workbox.googleAnalytics.initialize();
<?php endif; ?>

	// Initialize the workbox service worker
	// #####################################

	const wbsw = new WorkboxSW({
	clientsClaim: true,
	skipWaiting: true,
	});

	// Pre-cache fallback responses
	// #####################################
<?php
$pageId = esc_attr(get_option('offline_precache_page_id'));
?>
	const OFFLINE_PAGE_URL = '<?php echo get_permalink($pageId) ?>';

	wbsw.precache([
	{
	url: OFFLINE_PAGE_URL,
	revision: '<?php echo "ofprsw" . time() ?>',
	}
	]);

	// Serve assets using cache-first strategy
	// #####################################

	wbsw.router.registerRoute(/\.(png|jpeg|jpg|gif)$/, wbsw.strategies.cacheFirst());
	wbsw.router.registerRoute(/\.(js|css)$/, wbsw.strategies.cacheFirst());

	// Serve backend requests with network-only strategy
	// #####################################

	wbsw.router.registerRoute('<?php echo get_admin_url() . "*"; ?>', wbsw.strategies.networkOnly());

	// Serve paths with configured custom strategies
	// #####################################

	const custom_strategies = <?php echo json_encode(OfflinePrecacheAdmin::get_custom_strategies(true)) ?>;

	for (let route of custom_strategies) {
	wbsw.router.registerRoute(route.path, ({event}) => {
	return wbsw.strategies[route.strategy]().handle({event})
	.then((response) => {
	if (!response && event.request.mode == 'navigate') {
	return caches.match(OFFLINE_PAGE_URL);
	}
	return response;
	})
	.catch(() => event.request.mode == 'navigate' ? caches.match(OFFLINE_PAGE_URL) : Response.error());
	});
	}

	// Set default strategies
	// #####################################

	wbsw.router.setDefaultHandler({
	handler: ({event}) => {
	switch (event.request.method) {
	case 'GET':
	// For GET requests, use network-first with offline page fallback
	return wbsw.strategies.networkFirst().handle({event})
	.then((response) => {
	if (!response && event.request.mode == 'navigate') {
	return caches.match(OFFLINE_PAGE_URL);
	}
	return response;
	});
	case 'POST':
	// For POST requests, use network-only with offline page fallback
	return wbsw.strategies.networkOnly().handle({event})
	.catch(() => event.request.mode == 'navigate' ? caches.match(OFFLINE_PAGE_URL) : Response.error());
	default:
	// Use network-only for all other request types
	return wbsw.strategies.networkOnly().handle({event});
	}
	},
	});