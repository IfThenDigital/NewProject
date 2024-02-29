# IfThen[] Starter Theme
Based upon the [Timber starter theme](https://github.com/timber/starter-theme).

## Theme Structure
- `.storybook`: Storybook configuration.
- `lib`: Wordpress theme code. Intended to be re-usable.
- `src`: Frontend SASS and javascript code.
- `templates`: Twig templates.


## Starting Point
### Javascript and SASS
This theme comes with some basic frontend setup, including:
- Basic Webpack configuration.
- Basic structure for SASS, including Bootstrap.
- Some commonly used JS in Wordpress themes.

This is a starting point. Frontend JS frameworks (React, Vue, etc) are not included on purpose.

### Wordpress Theme Support
- `lib/BaseSite`: A theme reset class for use with Timber. functions.php inherits this.