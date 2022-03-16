<?php
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
echo json_encode($params);
