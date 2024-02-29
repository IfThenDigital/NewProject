/*global wp */
/*eslint no-undef: "error"*/

/**
 * A simple class to modify any Gutenberg editor controls.
 *
 * Unfortunately, this is the only way to remove/alter these editor controls.
 */
class GutenbergEditorControls {

  constructor() {
    this._editorControlsRemoved = false;

    this.removeEditorControls = this.removeEditorControls.bind(this);

    wp.data.subscribe(this.removeEditorControls);
  }

  /**
   * Removes select Gutenberg editor controls.
   */
  removeEditorControls() {
    // Remove select editor controls.
    switch (wp.data.select("core/editor").getCurrentPostType()) {
      case "page":
      case "post":
        wp.data
          .dispatch("core/edit-post")
          .removeEditorPanel("featured-image");
        wp.data
          .dispatch("core/edit-post")
          .removeEditorPanel("post-link");

        break;

    }

    /*
    // Keep as a reference for later.
    // This is how to remove panels from Gutenberg.
    wp.data
      .dispatch("core/edit-post")
      .removeEditorPanel("taxonomy-panel-category");

    wp.data
      .dispatch("core/edit-post")
      .removeEditorPanel("taxonomy-panel-post_tag");

    wp.data
      .dispatch("core/edit-post")
      .removeEditorPanel("taxonomy-panel-topic");

    wp.data
      .dispatch("core/edit-post")
      .removeEditorPanel("post-link");

    wp.data
      .dispatch("core/edit-post")
      .removeEditorPanel("page-attributes");
    */
  }

}

function addTableSettings(settings) {
  if (settings.name === "core/table") {
    settings.attributes = {
      ...settings.attributes,
      layout: {
        type: "string",
        default: "",
      },
    };
  }
  return settings;
}

wp.domReady(() => {
  new GutenbergEditorControls();

  //window._wpLoadBlockEditor.then(editorControls.removeEditorControls);
  //editorControls.removeEditorControls();

  // wp.blocks.unregisterBlockStyle("core/table", "default");
  //wp.blocks.unregisterBlockStyle("core/table", "stripes");
  //wp.blocks.unregisterBlockStyle("core/separator", "wide");
  //wp.blocks.unregisterBlockStyle("core/separator", "dots");

  //wp.hooks.addFilter("blocks.registerBlockType", "ahc/table", addTableSettings);
});
