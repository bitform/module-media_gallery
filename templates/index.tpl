{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="images/media_gallery.gif" width="34" height="34" /></td>
    <td class="title">{$L.module_name|upper}</td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.module_description}
  </div>

  <form action="{$same_page}" method="post">

    <table cellspacing="1" cellpadding="1" border="0" width="100%">
    <tr>
      <td width="200">{$LANG.word_status}</td>
      <td>
        <input type="radio" name="status" value="enabled" id="status1" {if $module_settings.status == "enabled"}checked{/if} />
          <label for="status1" class="green">{$LANG.word_enabled}</label>
        <input type="radio" name="status" value="disabled" id="status2" {if $module_settings.status == "disabled"}checked{/if} />
          <label for="status2" class="red">{$LANG.word_disabled}</label>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_preserve_thumb_aspect_ratio}</td>
      <td>
        <input type="radio" name="preserve_thumb_aspect_ratio" value="yes" id="ar2"
          {if $module_settings.preserve_thumb_aspect_ratio == "yes"}checked{/if} />
          <label for="ar2">{$LANG.word_yes}</label>
        <input type="radio" name="preserve_thumb_aspect_ratio" value="no" id="ar1"
          {if $module_settings.preserve_thumb_aspect_ratio == "no"}checked{/if} />
          <label for="ar1">{$LANG.word_no}</label>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_default_thumb_width}</td>
      <td>
        <input type="text" name="default_thumb_width" value="{$module_settings.default_thumb_width}" style="width:60px" />
        <span class="medium_grey">{$L.phrase_thumb_size_notes}<span>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_default_thumb_height}</td>
      <td>
        <input type="text" name="default_thumb_height" value="{$module_settings.default_thumb_height}" style="width:60px" />
        <span class="medium_grey">{$L.phrase_thumb_size_notes}<span>
      </td>
    </tr>
    </table>

    <hr size="1" />

    <p class="bold">{$L.phrase_image_fields}</p>

    <p>
      {$L.text_image_field_config_notes}
    </p>

    <table cellspacing="1" cellpadding="0" class="list_table margin_bottom_large">
    <tr>
      <th rowspan="2">{$LANG.word_form}</th>
      <th rowspan="2">{$LANG.word_field}</th>
      <th colspan="4">{$LANG.phrase_field_type}</th>
      <th rowspan="2">{$L.word_title}</th>
    </tr>
    <tr>
      <th>{$L.phrase_non_media}</th>
      <th>{$L.word_image}</th>
      <th>{$L.word_video}</th>
      <th>{$L.word_audio}</th>
    </tr>
    {foreach from=$forms item=form name=row}
      {assign var=num_file_fields value=$form.file_fields|@count}
      {assign var=form_id value=$form.form_id}
      <tr>
        <td {if $num_file_fields > 1}rowspan="{$num_file_fields}"{/if} class="pad_left_small"
          valign="top" width="150"><a href="../../admin/forms/submissions.php?form_id={$form_id}">{$form.form_name}</a></td>

      {if $form.file_fields|@count == 0}
        <td class="medium_grey" colspan="3">This form has no file fields</td>
      {else}
        {foreach from=$form.file_fields item=field name=row}
          {if $smarty.foreach.row.iteration != 1}
            <tr>
          {/if}

          {assign var=media_type value="non_media"}
          {foreach from=$image_field_info.$form_id.media_info item=field_info name=row2}
            {if $field.field_id == $field_info.field_id}
              {assign var=media_type value=$field_info.media_type}
            {/if}
          {/foreach}

          <td><label for="f{$form_id}_{$field.field_id}">{$field.field_title}</label></td>

          <td align="center" width="60"><input type="radio" id="f{$form_id}_{$field.field_id}" {if $media_type == "non_media"}checked{/if}
            name="form_field__f{$form_id}_{$field.field_id}" value="non_media" /></td>
          <td align="center" width="60"><input type="radio" id="f{$form_id}_{$field.field_id}" {if $media_type == "image"}checked{/if}
            name="form_field__f{$form_id}_{$field.field_id}" value="image" /></td>
          <td align="center" width="60"><input type="radio" id="f{$form_id}_{$field.field_id}" {if $media_type == "video"}checked{/if}
            name="form_field__f{$form_id}_{$field.field_id}" value="video" disabled /></td>
          <td align="center" width="60"><input type="radio" id="f{$form_id}_{$field.field_id}" {if $media_type == "audio"}checked{/if}
            name="form_field__f{$form_id}_{$field.field_id}" value="audio" disabled /></td>

          {if $smarty.foreach.row.iteration == 1}
            {assign var=title value=$image_field_info.$form_id.title}
            <td {if $num_file_fields > 1}rowspan="{$num_file_fields}"{/if}
              valign="top"><input type="text" name="form_field__f{$form_id}_title" value="{$title|escape}" style="width:98%" /></td>
          {/if}
        </tr>
        {/foreach}
      {/if}
    {/foreach}
    </table>

    <hr size="1" />

    <p class="bold">{$L.phrase_form_title_placeholders}</p>

    <p>
      {$L.text_placeholder_notes}
    </p>

    {foreach from=$form_placeholders key=form_id item=fields row=r}
       <div class="grey_box margin_bottom_large">
        <div class="">{$LANG.word_form_c} {$form_id|regex_replace:"/\|\d+$/":""}</div>

        {assign var=has_placeholders value="false"}
        {foreach from=$fields item=field_info name=r2}
          {if $smarty.foreach.r2.iteration == 1}
            {assign var=has_placeholders value="true"}
            <table cellpadding="1" cellspacing="1" width="100%">
            <tr>
              <th>{$LANG.phrase_field_label}</th>
              <th>Placeholder</th>
            </tr>
          {/if}

          {if $field_info.field_type != "file" && $field_info.field_type != "system"}
            <tr>
              <td>{$field_info.field_title}</td>
              <td class="blue">
                {literal}{$ANSWER{/literal}_{$field_info.field_name}{literal}}{/literal}
              </td>
            </tr>
          {/if}
        {/foreach}

        {* if there was at least one row, close the table. Otherwise inform the user that this
           form doesn't have any usable placeholder *}
        {if $has_placeholders == "true"}
          </table>
        {else}
          <p>{$L.text_no_placeholders}</p>
        {/if}
      </div>
    {/foreach}

    <p>
      <input type="submit" name="update" value="{$LANG.word_update|upper}" />
    </p>

  </form>

{include file='modules_footer.tpl'}