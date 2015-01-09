<?php

/**
 * Register default filters
 */

register_route_filter('int', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_INT);
});

register_route_filter('float', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_FLOAT);
});

register_route_filter('email', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_EMAIL);
});

register_route_filter('ip', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_IP);
});

register_route_filter('mac', function($partial) {
    return filter_var($partial, FILTER_VALIDATE_MAC);
});