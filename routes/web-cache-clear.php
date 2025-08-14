<?php
// Add this temporarily to routes/web.php

Route::get('/clear-cache-emergency', function() {
    if (app()->environment('production')) {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        
        return 'Cache cleared successfully!';
    }
    return 'Only available in production';
});
