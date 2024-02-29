module.exports = {
  "stories": [
    "../src/**/*.stories.mdx",
    "../src/**/*.stories.@(js|jsx|ts|tsx)"
  ],
  "addons": [
    "@storybook/addon-links",
    "@storybook/addon-essentials"
  ],
  webpackFinal: async (config, { configType }) => {

    // Enable SASS compiler.
    config.module.rules.push({
      test: /\.scss$/,
      use: [
        "style-loader",
        "css-loader",
        "sass-loader"
      ]
    });

    // Enable the Twig loader.
    config.module.rules.push({
      test: /\.twig$/,
      loader: "twigjs-loader",
      options: {
        renderTemplate(twigData, dependencies, isHot) {
          const hmrFix = isHot ? '\n    require("twig").cache(false);' : "";

          return `
            ${dependencies} ${hmrFix}
            var twig = require("twig").twig;
            var tpl = twig(${JSON.stringify({
            ...twigData,
            autoescape: true
          })});
            module.exports = function(context) { return tpl.render(context); };
            module.exports.id = ${JSON.stringify(twigData.id)};
            module.exports.default = module.exports;
          `.replace(/^\s+/gm, "");
        }
      }
    });

    // Return the altered config
    return config;
  }
}