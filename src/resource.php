<?php

require_once("cache.php");
require_once("validate.php");

//place you code here
printf("Current timestamp is %s", (new \DateTimeImmutable())->getTimestamp());
