<?php

namespace RaspAp;

class Test {
  public static function runAll() {
    $start = microtime(true);
    $num_tests = 0;

    $classes = self::getTestClasses();
    foreach ($classes as $class) {
      $num_tests+= sizeof($class::getTestMethods($class));
      $class::run();
    }

    $seconds = round(microtime(true) - $start, 3);
    echo "\nRan $num_tests tests in $seconds seconds\n";
  }

  protected static function getTestClasses() {
    return array_filter(get_declared_classes(), function($name) {
      return preg_match('/\\\.+Test$/', $name) != false;
    });
  }

  public static function run() {
    $test = new static();
    $test_methods = static::getTestMethods($test);
    foreach ($test_methods as $test_method) {
      $test->runTest($test_method);
    }
  }

  protected static function getTestMethods($instance) {
    return array_filter(get_class_methods($instance), function($name) {
      return preg_match('/^test.+/', $name) != false;
    });
  }

  protected function runTest($name) {
    echo "=> " . get_called_class() . "::$name\n";
    $this->setup();
    $this->$name();
    $this->teardown();
  }

  public function setUp() {}
  public function tearDown() {}

  protected function assertEqual($expected, $actual) {
    if ($expected !== $actual) {
      echo __FUNCTION__ . ": $expected != $actual\n";
    }
  }
}
