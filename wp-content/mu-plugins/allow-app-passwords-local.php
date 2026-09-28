<?php
// Local-only: allow Application Passwords over HTTP
add_filter('wp_is_application_passwords_available', '__return_true');
