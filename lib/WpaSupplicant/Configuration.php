<?php

namespace WpaSupplicant;

class Configuration {
  protected $config = [];

  public static function load(string $path) {
    $config = file_get_contents($path);
    $parsed = static::parse($config);
    return new self($parsed);
  }

  public static function parse(string $config) {
    $deserializer = new ConfigurationDeserializer($config);
    return $deserializer->deserialize();
  }

  public function __construct(array $config = []) {
    foreach ($config as $key => $value) {
      $this->{$key} = $value;
    }
  }

  public function __get($option) {
    return $this->config[$option];
  }

  public function __set($option, $value) {
    if (is_string($value) && is_numeric($value)) {
      $value = intval($value);
    }
    return $this->config[$option] = $value;
  }

  public function __toString() {
    $serializer = new ConfigurationSerializer($this->config);
    return $serializer->serialize();
  }
}

class ConfigurationDeserializer {
  const STATE_NONE = 0;
  const STATE_NETWORK = 1;
  const STATE_NETWORK_END = 2;

  protected $config = "";

  public function __construct(string $config) {
    $this->config = $config;
  }

  public function deserialize() {
    $options = [];
    $network = [];
    $lines = explode("\n", $this->config);

    $state = self::STATE_NONE;
    foreach ($lines as $line) {
      // $line = trim($line);
      if (strlen($line) == 0 || $line[0] == "#") {
        continue;
      }

      list($option, $value) = array_map("trim", explode("=", $line, 2));
      $value = trim($value, '"');

      if ($option == "network") {
        $state = self::STATE_NETWORK;
        $network = [];
        continue;
      } elseif ($option == "}") {
        $state = self::STATE_NETWORK_END;
      }

      switch ($state) {
        case self::STATE_NETWORK:
          $network[$option] = $value;
          break;
        case self::STATE_NETWORK_END:
          if (empty($options['network'])) { $options['network'] = []; }
          $options['network'][] = $network;
          $state = self::STATE_NONE;
          break;
        default:
          $options[$option] = $value;
          break;
      }
    }

    return $options;
  }
}

class ConfigurationSerializer {
  const STRING_OPTIONS = [ "ssid", "#psk" ];

  protected $config = [];

  public function __construct(array $config) {
    $this->config = $config;
  }

  public function serialize() {
    $props = [];
    foreach ($this->config as $option => $value) {
      if ($option == "network") { continue; }
      $props[] = "$option=$value";
    }

    return implode("\n", $props) . "\n" . $this->serializeNetworks();
  }

  protected function serializeNetworks() {
    if (empty($this->config['network'])) { return ""; }
    $string = "";

    foreach ($this->config['network'] as $network) {
      $directive = [];
      foreach ($network as $option => $value) {
        $directive[] = "  $option=" . $this->formatValue($option, $value);
      }
      $string .= "network={\n" . implode("\n", $directive) . "\n}\n";
    }

    return $string;
  }

  protected function formatValue($option, $value) {
    if (in_array($option, static::STRING_OPTIONS)) {
      return "\"$value\"";
    } else {
      return $value;
    }
  }
}
