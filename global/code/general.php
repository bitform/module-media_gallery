<?php

/**
 * This content is inserted into the <head> of the client and admin submission listing page.
 *
 * @param array $info
 * @return array
 */
function mg_insert_head_js($info)
{
	global $g_root_url, $LANG, $L;

	$page = $info["g_smarty"]->_tpl_vars["page"];

	if ($page != "client_forms" && $page != "admin_forms")
	  return;

	$module_settings = ft_get_module_settings("", "media_gallery");
	if ($module_settings["status"] == "disabled")
	  return;

	// This should detect the page and only do stuff for the two submission listing pages... menu page name?
	$form_id             = $info["g_smarty"]->_tpl_vars["SESSION"]["curr_form_id"];
  $page_submission_ids = $info["g_smarty"]->_tpl_vars["page_submission_ids"];

  $onload_show_gallery = ft_load_module_field("media_gallery", "onload_show_gallery", "onload_show_gallery", "no");

	$g_smarty = $info["g_smarty"];
	$head_string = $g_smarty->_tpl_vars["head_string"];
	$head_string .=<<< EOF
	<script type="text/javascript" src="{$g_root_url}/modules/media_gallery/global/scripts/swfobject.js"></script>
  <script type="text/javascript">
  var mg = {
    flashVars: {
	    form_id:        $form_id,
	    submission_ids: "$page_submission_ids",
	    g_root_url:     "$g_root_url"
    },

    showGallery: function()
	  {
	    // fade out the submissions table
	    new Effect.Fade("media_gallery_table_format_div");

	    setTimeout(function() {
	      swfobject.embedSWF("{$g_root_url}/modules/media_gallery/global/flash/gallery.swf", "media_gallery", "740", "600", "8.0.0", "{$g_root_url}/modules/media_gallery/global/flash/expressInstall.swf", mg.flashVars);
	      $("gallery_toggle_link").innerHTML = "<img src=\"{$g_root_url}/modules/media_gallery/images/table_format.gif\" border=\"0\" alt=\"{$LANG["phrase_table_format"]}\" title=\"{$LANG["phrase_table_format"]}\" width=\"34\" height=\"34\" />";
	      $("gallery_toggle_link").onclick = function() { return mg.hideGallery(); };
	    }, 1000);

	    var page_url = "$g_root_url/modules/media_gallery/global/code/actions.php?action=remember_media_gallery_mode&onload_show_gallery=yes";
	    new Ajax.Request(page_url, { method: 'get' });

	    return false;
	  },

	  hideGallery: function() {
			new Effect.Fade("media_gallery");
			new Effect.Appear("media_gallery_table_format_div", { delay: 1 });

	    setTimeout(function() {
	      $("media_gallery").innerHTML = "";
	      $("gallery_toggle_link").innerHTML = "<img src=\"{$g_root_url}/modules/media_gallery/images/media_gallery.gif\" border=\"0\" width=\"34\" height=\"34\" alt=\"{$L["module_name"]}\" title=\"{$L["module_name"]}\" />";
	      $("gallery_toggle_link").onclick = function() { return mg.showGallery(); };
	    }, 1000);

	    var page_url = "$g_root_url/modules/media_gallery/global/code/actions.php?action=remember_media_gallery_mode&onload_show_gallery=no";
	    new Ajax.Request(page_url, { method: 'get' });

	    return false;
	  }
  };

  // if the user was previously in Media Gallery mode,
  Event.observe(document, "dom:loaded", function() {
  	if ('yes' == '$onload_show_gallery')
  	{
  	  mg.showGallery();
  	}
	});

  </script>
EOF;

  $info["g_smarty"]->assign("head_string", $head_string);

  return $info;
}


/**
 * This inserts the Media Gallery markup just above the table listing all submissions on the admin
 * and client submission listings page.
 *
 * @param string $location the name of the template hook "location" attribute.
 * @param array $info
 */
function mg_open_gallery_table($location, $info)
{
	global $g_root_url, $LANG;

	ft_include_module("media_gallery");

	$module_settings = ft_get_module_settings("", "media_gallery");
	if ($module_settings["status"] == "disabled")
	  return;

	$form_id = $info["form_info"]["form_id"];
  $image_field_info = mg_deserialize_image_field_info_string($module_settings["image_field_info"]);
	$form_ids = array_keys($image_field_info);

	// check that there is one or more media files for this form
	if (!in_array($form_id, $form_ids))
		return;

	$onload_show_gallery = ft_load_module_field("media_gallery", "onload_show_gallery", "onload_show_gallery", "no");

  $hide_submission_listing_table_style = "";
  $icon = "media_gallery.gif";
  if ($onload_show_gallery == "yes")
  {
  	$hide_submission_listing_table_style = "style=\"display:none\"";
  	$icon = "table_format.gif";
  }

  echo <<< EOF
<a href="#" id="gallery_toggle_link" style="float:right; margin-top: -40px;"
  onclick="return mg.showGallery();"><img src="{$g_root_url}/modules/media_gallery/images/$icon" border="0"
  width="34" height="34" alt="{$LANG["media_gallery"]["module_name"]}" title="{$LANG["media_gallery"]["module_name"]}" /></a>
<div id="media_gallery"></div>

<div id="media_gallery_table_format_div" $hide_submission_listing_table_style>
EOF;
}


function mg_close_gallery_table($location, $info)
{
	$form_id = $info["form_info"]["form_id"];
  $image_field_info = mg_deserialize_image_field_info_string($module_settings["image_field_info"]);
	$form_ids = array_keys($image_field_info);

	// check that there is one or more media files for this form
	if (!in_array($form_ids, $form_id))
		return;

	echo "</div>";
}


/**
 * Called on the (only) admin page for the Media Gallery module. This updates all the settings listed
 * at the top of the page.
 *
 * @param array $info
 * @return array
 */
function mg_update_settings($info)
{
  global $L;

  $image_field_info = mg_serialize_image_field_info($info);

  $settings = array(
    "status"                      => $info["status"],
    "preserve_thumb_aspect_ratio" => $info["preserve_thumb_aspect_ratio"],
    "default_thumb_width"         => $info["default_thumb_width"],
    "default_thumb_height"        => $info["default_thumb_height"],
    "image_field_info"            => $image_field_info
  );

  ft_set_module_settings($settings);

  return array(true, $L["notify_settings_updated"]);
}


/**:
 * This serializes the image field IDs, form IDs and title fields and logs the media format of each field type.
 *
 * The string format is:
 *
 *   	formID1[,formID2[,formID3[...]]]|formID1:fieldID1fieldtype[,fieldID2fieldtype[,fieldID3fieldtype[...]]]-Title[;formID2: ...]
 *
 * Note the use of the commas, pipes, semi-colons and dashes to break up the string. This should make it
 * simple to parse. fieldtype is a string: "non_media", "image", "video" or "audio"
 *
 * @param array $info
 * @return string
 */
function mg_serialize_image_field_info($info)
{
	$data     = array();
	$form_ids = array();

	while (list($key, $value) = each($info))
	{
		if (preg_match("/^form_field__f(\d+)_(title|\d+)/", $key, $matches))
		{
			$form_id           = $matches[1];
			$field_id_or_title = $matches[2];

			if (!in_array($form_id, $form_ids))
			{
				$form_ids[] = $form_id;
				$data["$form_id"] = array(
					"field_ids" => array(),
				  "title" => ""
				);
			}

			if (is_numeric($field_id_or_title))
				$data["$form_id"]["field_ids"][] = "{$field_id_or_title}{$value}";
			else
			{
			  $data["$form_id"]["title"] = $value;
			}
		}
	}

	// now serialize the info
	$serialized_str = join(",", $form_ids) . "|";

	$form_data_row = array();
	while (list($form_id, $curr_info) = each($data))
	{
		$form_data_row[] = "$form_id:" . join(",", $curr_info["field_ids"]) . "`" . $curr_info["title"];
	}

  $serialized_str .= join(";", $form_data_row);

	return $serialized_str;
}


/**
 * This deserializes an image field info string.
 *
 * @param string $str
 * @return array
 */
function mg_deserialize_image_field_info_string($str)
{
  list($form_ids, $all_form_data) = explode("|", $str);

  $single_form_data = explode(";", $all_form_data);

  $data = array();
  if (!is_array($form_ids))
    $form_ids = array($form_ids);

  foreach ($form_ids as $form_id)
  {
		$data["$form_id"] = array(
			"field_ids" => array(),
		  "title"     => ""
		);
  }

  foreach ($single_form_data as $form_data)
  {
  	list($form_id, $curr_form_data) = explode(":", $form_data);
  	list($field_ids, $title) = split("`", $curr_form_data);

  	$field_info = explode(",", $field_ids);
    $media_info = array();
  	foreach ($field_info as $row)
  	{
  		preg_match("/^(\d+)(.*)$/", $row, $matches);
  		$media_info[] = array(
  		  "field_id"   => $matches[1],
  		  "media_type" => $matches[2]
  		);
  	}

  	$data["$form_id"]["media_info"] = $media_info;
  	$data["$form_id"]["title"] = $title;
  }

  return $data;
}
