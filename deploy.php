<?php

namespace Deployer;

require 'recipe/symfony4.php';

set('application', 'booking');

//set('repository', '{{git_url}}');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('allow_anonymous_stats', false);
set('clear_use_sudo', true);

host('{{host}}') //TODO: maybe alias here
    ->stage('dev')
    ->set('repository', '{{git_url}}')
    ->user('dev')
    ->set('branch', 'dev')
    ->port(22)
    ->configFile('~/.ssh/config')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '~/.ssh/known_hosts')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set('deploy_path', '~/projects/{{project_dir}}') //TODO: maybe git repo here
;

// Tasks

task('build', function () {
    run('cd {{release_path}} && npm install && npm run build');
});


after('deploy:failed', 'deploy:unlock');

// TODO: Migrate database before symlink new release.
//before('deploy:symlink', 'database:migrate');

after('deploy', 'build');