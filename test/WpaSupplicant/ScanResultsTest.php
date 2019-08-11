<?php

use RaspAp\Test;
use WpaSupplicant\ScanResults;

namespace WpaSupplicant;

class ScanResultsTest extends \RaspAp\Test {
  public function setUp() {
    parent::setUp();
    $this->subject  = new ScanResults("wlan0");
  }

  public function testScan() {
    $this->assertEqual(18, sizeof($this->subject->scan()));
  }
}
