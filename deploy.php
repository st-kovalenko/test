<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/st-kovalenko/test.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('95.179.246.104')
    ->set('remote_user', 'laravel')
    ->set('branch', 'master')
    ->set('deploy_path', '~/artisan-sandbox');

// Hooks

after('deploy:failed', 'deploy:unlock');
