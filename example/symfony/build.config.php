<?php
$config = array(
  "name" => "test",
  "description" => "test build for buildkake",
  "version" => "0.1",
  "deps" => array(
    "plugins" => array(
      "path" => "plugins",
      "items" => array(
        array("type" => "git", "name" => "asOauthPlugin", "url" => "git@github.com:elboby/asOauthPlugin.git", "branch" => "master")
      )
    )  ,
    "vendor" => array(
      "path" => "lib/vendor",
      "items" => array(
        array("type" => "svn", "name" => "symfony", "url" => "http://svn.symfony-project.com", "branch" => "tags/RELEASE_1_4_6")
      )
    )
  ),
  "build-command" => "php lib/vendor/symfony/data/bin/symfony generate:project test;"
);
