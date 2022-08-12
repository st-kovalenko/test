@servers(['local' => '127.0.0.1', 'production' => 'root@95.179.246.104'])

@setup
    $repo = 'https://time-to.dev:oFWm7dyCSkg7zAz-B3aD@gitlab.com/st_kovalenko/artisan-sandbox.git';
    $branch = 'stage';

    date_default_timezone_set('Europe/Berlin');
    $date = date('YmdHis');

    $appDir = '/srv/time-to.dev';

    $buildsDir = $appDir . '/releases';

    $deploymentDir = $buildsDir . '/' . $date;

    $serve = $appDir . '/live';
    $env = $appDir . '/.env';
    $storage = $appDir . '/storage';

    $productionPort = 22;
    $productionHost = 'root@95.179.246.104';
@endsetup

@task('build', ['on' => 'local'])
    npm run build
@endtask

@task('git', ['on' => 'production'])
    git clone --depth 1 -b {{ $branch }} "{{ $repo }}" {{ $deploymentDir }}
@endtask

@task('install', ['on' => 'production'])
    cd {{ $deploymentDir }}

    rm -rf {{ $deploymentDir }}/storage

    ln -nfs {{ $env }} {{ $deploymentDir }}/.env
    ln -nfs {{ $storage }} {{ $deploymentDir }}/storage

    composer install --prefer-dist --no-dev

    php artisan migrate --force

    php artisan storage:link
@endtask

@task('assets', ['on' => 'local'])
    scp -P{{ $productionPort }} -rq public/build/ {{ $productionHost }}:{{ $deploymentDir }}/public
@endtask

@task('live', ['on' => 'production'])
    ln -nfs {{ $deploymentDir }} {{ $serve }}
    chown -R www-data:www-data /srv/time-to.devrm
@endtask

@story('deploy')
    build
    git
    install
    assets
    live
@endstory
