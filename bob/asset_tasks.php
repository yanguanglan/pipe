<?php

namespace Bob;

use Pipe\Config;

function getConfig()
{
    static $config;
    return $config ?: $config = Config::fromYaml("pipe_config.yml");
}

function getEnvironment()
{
    static $env;
    return $env ?: $env = getConfig()->createEnvironment();
}

desc("Dumps all assets.");
task("assets:dump", function() {
    $targetDir = @$_ENV["TARGET_DIR"] ?: "htdocs/assets";

    $env = getEnvironment();
    $config = getConfig();

    $manifests = $config->get("manifests");

    foreach ($manifests as $manifest) {
        $asset = $env->find("$manifest");

        if (!$asset) {
            println("Asset $manifest not found!", STDERR);
            exit(1);
        }

        $content = $asset->getBody();

        if (!is_dir(dirname($manifest))) {
            mkdir(dirname($manifest), 0777, true);
        }

        println("Dumping '$manifest' as '{$asset->getTargetName()}'");
        $asset->write($targetDir);
    }
});

task("server", function() {
    $port = @$_ENV["port"] ?: 4000;
    $host = @$_ENV["host"] ?: "0.0.0.0";

    php(
        array("-S", "$host:$port", "-t", getcwd() . "/htdocs/", getcwd() . "/htdocs/index.php"),
        null,
        1e18
    );
});
