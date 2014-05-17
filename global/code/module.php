<?php


/**
 * The module installation function.
 *
 * @param integer $module_id
 * @return array
 */
function media_gallery__install($module_id)
{
  global $g_table_prefix, $LANG;

  $queries = array();
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('status', 'enabled', 'media_gallery')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('preserve_thumb_aspect_ratio', 'no', 'media_gallery')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('default_thumb_width', '50', 'media_gallery')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('default_thumb_height', '50', 'media_gallery')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('image_field_info', '', 'media_gallery')";

  // register the hooks
  ft_register_hook("template", "media_gallery", "admin_submission_listings_top", "", "mg_open_gallery_table");
  ft_register_hook("template", "media_gallery", "admin_submission_listings_bottom", "", "mg_close_gallery_table");
  ft_register_hook("template", "media_gallery", "client_submission_listings_top", "", "mg_open_gallery_table");
  ft_register_hook("template", "media_gallery", "client_submission_listings_bottom", "", "mg_close_gallery_table");
  ft_register_hook("code", "media_gallery", "main", "ft_display_page", "mg_insert_head_js");

	$has_problem = false;
	foreach ($queries as $query)
  {
  	$result = @mysql_query($query);
	  if (!$result)
	  {
	    $has_problem = true;
	    break;
	  }
  }

  // if there was a problem, remove all the table and return an error
  $success = true;
  $message = "";
  if ($has_problem)
	{
		$success = false;
		$mysql_error = mysql_error();
		$message     = ft_eval_smarty_string($LANG["media_gallery"]["notify_problem_installing"], array("error" => $mysql_error));
	}

	return array($success, $message);
}


/**
 * The module uninstallation function.
 *
 * @return array
 */
function media_gallery__uninstall()
{
	return array(true, "");
}
