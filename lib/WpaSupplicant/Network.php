<?php

namespace WpaSupplicant;

class Network {
  const PROPERTY_NAMES = [ "bssid", "frequency", "signal", "flags", "ssid" ];
  const FORMATTERS = [
    "frequency" => "intval",
    "signal"    => "intval",
    "flags"     => "self::formatFlags",
  ];

  protected $properties = [];

  public function __construct($props) {
    $this->properties = array_combine(self::PROPERTY_NAMES, $props);
    $this->formatProperties();
  }

  public function __get($prop) {
    if (array_key_exists($prop, $this->properties)) {
      return $this->properties[$prop];
    }
  }

  protected function formatProperties() {
    foreach ($this->properties as $key => $value) {
      $callable = self::FORMATTERS[$key];
      if ($callable) {
        $this->properties[$key] = call_user_func($callable, $value);
      }
    }
  }

  public static function formatFlags($value) {
    if (false != preg_match_all("/\[([A-Z0-9-]+)\]/", $value, $matches)) {
      return $matches[1];
    } else {
      return [];
    }
  }
}
