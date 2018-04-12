<?php
session_start();

unset($_SESSION['user']);

// Get $redirect_url
@$redirect_url = $_SERVER['HTTP_REFERER'];
@$redirect_url = (string) $redirect_url;
@$redirect_url = trim($redirect_url);
if (strlen($redirect_url) <= 0) $redirect_url = './';

// Redirect To URL
header("Location: {$redirect_url}");
?>