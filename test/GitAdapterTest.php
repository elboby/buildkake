<?php

include('../lib/Adapter.class.php');
include('../lib/adapters/GitAdapter.class.php');

$testfield_root = dirname(__FILE__).'/field';

$c = new GitAdapter('test', $testfield_root, array())