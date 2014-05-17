<?php

/**
 * This file is called by the Flash movie whenever it's been embedded in the page.
 */

$folder = dirname(__FILE__);
require_once("$folder/../../../../global/session_start.php");

// this script is called by the Flash script after loading
$request = array_merge($_POST, $_GET);
$form_id            = $request["formID"];
$submission_ids_str = $request["submissionIDs"];

if (empty($submission_ids_str))
  return;

ft_include_module("media_gallery");
$module_settings = ft_get_module_settings();
$image_field_info = mg_deserialize_image_field_info_string($module_settings["image_field_info"]);


// confirm that this form HAS been configured with the module
if (!array_key_exists($form_id, $image_field_info))
  return;

$account_type = $_SESSION["ft"]["account"]["account_type"];
$media_field_info = array();
for ($i=0; $i<count($image_field_info[$form_id]["media_info"]); $i++)
{
	$field_id   = $image_field_info[$form_id]["media_info"][$i]["field_id"];
	$media_type = $image_field_info[$form_id]["media_info"][$i]["media_type"];
	$media_field_info[$field_id] = $media_type;
}

$title_format = $image_field_info[$form_id]["title"];

$submission_ids = explode(",", $submission_ids_str);
$submission_info = array();
foreach ($submission_ids as $submission_id)
{
	$curr_submission_fields = ft_get_submission($form_id, $submission_id);

  // for each submission, extract the following information for each image field
  $placeholders = array();
  foreach ($curr_submission_fields as $field_info)
  {
    if ($field_info["field_type"] == "system")
      continue;

  	$field_name = $field_info["field_name"];
  	$placeholders["ANSWER_{$field_name}"] = $field_info["content"];
  }

  foreach ($curr_submission_fields as $field_info)
  {
  	if (!array_key_exists($field_info["field_id"], $media_field_info))
  	  continue;

  	$media_type = $media_field_info[$field_info["field_id"]];

  	$filename = $field_info["content"];
  	if (empty($filename))
  	  continue;

  	$field_settings = ft_get_extended_field_settings($field_info["field_id"], "core");
  	$folder_url = $field_settings["file_upload_url"];

  	$title = ft_eval_smarty_string($title_format, $placeholders);

		$submission_info[] = array(
		  "submission_id" => $submission_id,
		  "folder_url"    => $folder_url,
		  "filename"      => $filename,
		  "title"         => $title,
		  "media_type"    => $media_type
		);
  }
}


//header()
echo <<<EOF
<?xml version="1.0" encoding="UTF-8" ?>

<data>
	<settings>
		<thumbMaxHeight>{$module_settings["default_thumb_height"]}</thumbMaxHeight>
		<thumbMaxWidth>{$module_settings["default_thumb_width"]}</thumbMaxWidth>
		<thumbPreserveAspectRatio>{$module_settings["preserve_thumb_aspect_ratio"]}</thumbPreserveAspectRatio>
	</settings>

	<environment>
		<accountType>{$account_type}</accountType>
	</environment>

	<lang>
		<phrase_submission_id>{$LANG["phrase_submission_id"]}</phrase_submission_id>
		<word_filename>{$LANG["media_gallery"]["word_filename"]}</word_filename>
		<word_dimensions>{$LANG["media_gallery"]["word_dimensions"]}</word_dimensions>
		<phrase_no_media_files>{$LANG["media_gallery"]["phrase_no_media_files"]}</phrase_no_media_files>
		<phrase_gallery_settings>{$LANG["media_gallery"]["phrase_gallery_settings"]}</phrase_gallery_settings>
	</lang>

	<submissions>

EOF;

foreach ($submission_info as $submission)
{
  echo <<<EOF
		<submission>
			<submissionID>{$submission["submission_id"]}</submissionID>
			<title>{$submission["title"]}</title>
			<filename>{$submission["filename"]}</filename>
			<folderURL>{$submission["folder_url"]}</folderURL>
			<mediaType>{$submission["media_type"]}</mediaType>
		</submission>

EOF;
}

echo <<<EOF
	</submissions>
</data>
EOF;
