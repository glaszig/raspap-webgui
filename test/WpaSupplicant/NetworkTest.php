<?php

use RaspAp\Test;
use WpaSupplicant\Network;

namespace WpaSupplicant;

class NetworkTest extends \RaspAp\Test {
  public function setUp() {
    parent::setUp();
    $props = [
      "a1:b2:c3:d4:e5:f6",
      "2412",
      "-55",
      "[WPA-PSK-TKIP][WPA2-PSK-CCMP][WPS][ESS]",
      "foo bar"
    ];
    $this->subject  = new Network($props);
  }

  public function testProperties() {
    $this->assertEqual("a1:b2:c3:d4:e5:f6", $this->subject->bssid);
    $this->assertEqual(2412, $this->subject->frequency);
    $this->assertEqual(-55, $this->subject->signal);
    $this->assertEqual([ "WPA-PSK-TKIP", "WPA2-PSK-CCMP", "WPS", "ESS" ], $this->subject->flags);
    $this->assertEqual("foo bar", $this->subject->ssid);
  }
}
