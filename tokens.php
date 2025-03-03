<?php


$data_for_access_token = ["sub" => $user["id"], "username" => $user["username"], "exp" => time() + 3600];
  

$refresh_token_expiry = time() + 3600;
$data_for_refresh_token = ["sub"=> $user["id"], "exp" => $refresh_token_expiry];

$refresh_token = $codec->getJWTToken($data_for_refresh_token);

echo json_encode(["access_token" => $codec->getJWTToken($data_for_access_token),
                 "refresh_token" => $refresh_token]);