# Advanced Custom Fields Blocks - IfThen []
This plugin provides some helpful services and classes for building ACF blocks.

## Recommended Usage
Create a new plugin for all site specific blocks. Inherit from the `BaseBlock` from this plugin.

## `BaseBlock`
`BaseBlock` is designed to be inherited from when creating new ACF blocks. It provides some common functionality like:
- Handling the render callback for the block.
- Determining the Twig template to use for rendering.
- Functions for setting and retrieving cache values, if persistent caching is used.

## `BlockTemplateManager`
`BlockTemplateManager` handles locating the template for each block. It searches the template directory and returns the path to a Twig template with a matching block id annotation.

### Template Annotation Example
An example of how a block is tied to a template.

Block definition:
```
[
  'name' => 'hero',
  'title' => __('Hero'),
  'description' => __('A hero image with associated text and links.'),
  'render_callback' => '',
  'category' => 'custom',
  'keywords' => [
    'hero',
  ]
]
```

A Twig  template can be tied to this block with a comment annotation. Create a template anywhere in the `templates` directory, and match the `@BlockId` to the id of the block.
```
example-file.twig:

{#
/**
 * @BlockID: hero
 */
#}
```

The above `@BlockID` annotation will match the template to the block, and will be used on render.