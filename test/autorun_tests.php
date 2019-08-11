<?php

putenv("PATH=test/bin_stubs:" . getenv("PATH"));
set_include_path(implode(PATH_SEPARATOR, [ get_include_path(), "lib", "test" ]));

spl_autoload_register(function($name) {
  require_once str_replace('\\', DIRECTORY_SEPARATOR , $name) . '.php';
});

$dir_iterator = new RecursiveDirectoryIterator("test");
$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
foreach ($iterator as $file) {
  if (preg_match('/Test\.php$/', $file) != false) {
    require_once $file;
  }
}

RaspAp\Test::runAll();
