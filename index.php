<?php

require_once("../../global/library.php");
ft_init_module_page();

$folder = dirname(__FILE__);
require_once("$folder/library.php");

if (isset($_POST["update"]))
{
  list($g_success, $g_message) = mg_update_settings($_POST);
}

$module_settings = ft_get_module_settings();

$image_field_info = array();
if (!empty($module_settings["image_field_info"]))
  $image_field_info = mg_deserialize_image_field_info_string($module_settings["image_field_info"]);

$forms = ft_get_forms();

$updated_forms = array();
$form_field_placeholder_info = array();

foreach ($forms as $form_info)
{
	$fields = ft_get_form_fields($form_info["form_id"]);

	// a bit hackish, but it's solid. We make the key of the form "form name|form_id", to ensure
	// it's unique. Then, we can use the form name safely in the table, even for systems that have
	// two forms with the same name
	$key = $form_info["form_name"] . "|" . $form_info["form_id"];
  $form_field_placeholder_info[$key] = $fields;

	$file_fields = array();
	foreach ($fields as $field_info)
	{
	  if ($field_info["field_type"] == "file")
	  	$file_fields[] = $field_info;
	}

	$form_info["file_fields"] = $file_fields;
	$updated_forms[] = $form_info;
}

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["module_name"];
$page_vars["module_settings"] = $module_settings;
$page_vars["forms"] = $updated_forms;
$page_vars["form_placeholders"] = $form_field_placeholder_info;
$page_vars["image_field_info"] = $image_field_info;

ft_display_module_page("templates/index.tpl", $page_vars);