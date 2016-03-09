<?php
echo password_hash($_REQUEST['p'], PASSWORD_BCRYPT, array('cost' => 12));