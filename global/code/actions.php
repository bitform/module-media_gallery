<?php

$folder = dirname(__FILE__);
require_once("$folder/../../../../global/session_start.php");
ft_check_permission("user");

// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$request = array_merge($_GET, $_POST);
$action  = $request["action"];

switch ($action)
{
	case "remember_media_gallery_mode":
		$onload_show_gallery = ft_load_module_field("media_gallery", "onload_show_gallery", "onload_show_gallery");
		break;
}
