<?php


namespace IfThen\Acf\Blocks\Block;

class HtmlBlock extends BaseBlock {

  public function __construct() {
    $this->block_definition = [
      'name' => 'html',
      'title' => __('Custom HTML'),
      'description' => __('Write custom HTML.'),
      'render_callback' => '',
      'category' => '',
      'icon' => 'html',
      'keywords' => [],
    ];

    $this->register();

    $this->disable_wptexturize();
  }


  public function pre_render($block, $isPreview) {
    $context = parent::pre_render($block, $isPreview);

    if ($isPreview) {
      // For preview mode, we want to remove script tags.
      $html = $context['fields']['html'];

      $has_script_tags = true;

      while ($has_script_tags) {
        if ($html != null && str_contains($html,'<script')) {
          $script_tag_start = strpos($html, '<script');
          $script_tag_end = strpos($html, '</script>', $script_tag_start);

          $html = substr($html, 0, $script_tag_start) . substr($html, $script_tag_end + 9);
        }
        else {
          $has_script_tags = false;
        }
      }

      $context['fields']['html'] = $html;
    }
    else {
      // For rendering to the page, we want to wrap the script tags in a special Vue component.
      $html = $context['fields']['html'];

      $has_script_tags = true;
      $last_script_position = 0;
      while ($has_script_tags) {
        if ($html != null && strpos($html,'<script', $last_script_position)) {
          // First, let's pull out the first script tag and construct the required
          // Vue component.
          $script_tag_start = strpos($html, '<script', $last_script_position);
          $script_tag_end = strpos($html, '</script>', $script_tag_start);

          $script_tag = substr($html, $script_tag_start, ($script_tag_end - $script_tag_start) + 9);

          $vue_component = "<vue-script-component script='$script_tag'></vue-script-component>";

          $html = substr($html, 0, $script_tag_start) . $vue_component . substr($html, $script_tag_end + 9);

          $last_script_position = $script_tag_start + strlen($vue_component);
        }
        else {
          $has_script_tags = false;
        }
      }

      $context['fields']['html'] = $html;
    }

    return $context;
  }
}