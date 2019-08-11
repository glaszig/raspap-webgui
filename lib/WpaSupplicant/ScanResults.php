<?php

namespace WpaSupplicant;

class ScanResults {
  const NETWORK_REGEXP = "/(\S+)\t+(\S+)\t+(\S+)\t+(\S+)\t+(.*+)/";

  protected $interface;
  protected $networks = [];

  public function __construct($interface) {
    $this->interface = $interface;
  }

  public function scan() {
    $i = escapeshellarg($this->interface);
    exec("sudo wpa_cli -i $i scan");
    exec("sudo wpa_cli -i $i scan_results", $stdout);
    $this->parse($stdout);
    return $this->networks;
  }

  protected function parse($lines) {
    $this->networks = [];

    foreach ($lines as $line) {
      if (false != preg_match(self::NETWORK_REGEXP, $line, $matches)) {
        array_shift($matches);
        $this->networks[] = new Network($matches);
      }
    }
  }
}
