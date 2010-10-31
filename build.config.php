<?php
//
// OPTIONAL: buildkake will still work without those!!!
//
// Buildkake can build himself and add feature to its core base like:
// - unit testing via PHPUnit
// - writing build config file in the YAML format 
//
$config = array(
  "name" => "buildkake",
  "description" => "full build for buildkake",
  "version" => "0.1",
  "deps" => array(
    "vendor" => array(
      "path" => "lib/vendor",
      "items" => array(
        array("type" => "git", "name" => "yaml", "url" => "http://github.com/fabpot/yaml.git", "branch" => "master")
      )
    )
  ),
  "build-command" => ""
);
