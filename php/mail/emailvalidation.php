<?php
$email= $_POST['email'];

if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
  echo 1;
} else {
  echo 2;
}
?> 