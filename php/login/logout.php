<?php

if (!isset($_SESSION)) {
  session_start();
};
session_unset();
session_destroy();
print "<script>window.location='../../index.php';</script>";
