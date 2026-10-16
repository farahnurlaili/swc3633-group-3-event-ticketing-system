<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<script>localStorage.removeItem('auth_token'); window.location.replace('login.php');</script>
