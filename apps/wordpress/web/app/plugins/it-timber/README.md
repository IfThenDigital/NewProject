# Timber Helper - IfThen []
Provides some helper functionality when using the Timber plugin.

## `TemplateResolver`
A single place to hold custom logic for resolving Twig template paths. For example, providing a file pattern for post type templates.

## `TimberRenderer`
Intended to wrap any calls to `Timber::render`, and support rendering of templates. Notably the renderer will allow for filters to be used to alter data before post types are rendered.