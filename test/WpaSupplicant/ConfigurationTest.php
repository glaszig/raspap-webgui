<?php

use RaspAp\Test;

namespace WpaSupplicant;

class ConfigurationTest extends \RaspAp\Test {
  const CONFIG_FILE = "./test/fixtures/wpa_supplicant.conf";

  public function testParse() {
    $config = file_get_contents(self::CONFIG_FILE);
    $actual = Configuration::parse($config);
    $expected = [
      "ctrl_interface" => "DIR=/var/run/wpa_supplicant GROUP=netdev",
      "update_config" => "1",
      "network" => [
        [
          "ssid" => "foo",
          "#psk" => "foo-pre-shared-key",
          "psk"  => "f30d129b93b135cc0ec9d2c98cdccce67eb17b3d04c778cc50d923d90cffab61"
        ],
        [
          "ssid" => "bar",
          "#psk" => "bar-pre-shared-key",
          "psk"  => "f099b280e2391b25f2aec79c7c223883a17bede8296c4f8816a527ed0dc5bd40"
        ]
      ]
    ];
    $this->assertEqual($expected, $actual);
  }

  public function testLoad() {
    $this->assertInstanceOf("WpaSupplicant\Configuration", Configuration::load(self::CONFIG_FILE));
  }

  public function testGetter() {
    $config = Configuration::load(self::CONFIG_FILE);
    $this->assertEqual(1, $config->update_config);
  }

  public function testToString() {
    $config = Configuration::load(self::CONFIG_FILE);
    $expected = file_get_contents(self::CONFIG_FILE);
    $this->assertEqual($expected, $config->__toString());
  }

  public function testSerialization() {
    $config = new Configuration([ "update_config" => 1 ]);
    $expected = "update_config=1\n";
    $this->assertEqual($expected, "$config");
  }
}
