## Twic Doc Bundle

[![Image](docs/resources/images/qossmic.png)](https://qossmic.com) Brought to you by qossmic! 

1. [Installation](#installation)
2. Configuration
   1. [Bundle Configuration](docs/BundleConfiguration.md)
   2. [Component Configuration](docs/ComponentConfiguration.md)
3. [Routing](#routing)
4. [Customization](#customizing-the-design)
5. [Usage](docs/Usage.md)

---

Allows you to create an overview for your Twig Components, be it either [UX-Components](https://symfony.com/bundles/ux-twig-component/current/index.html), [UX-Live-Components](https://symfony.com/bundles/ux-live-component/current/index.html) or simple snippet templates.

Components will be grouped in categories and optional sub-categories.

### Installation

As long as the bundle is not publicly released, you need to add a repository to your composer.json file:

```json
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/qossmic/twig-doc-bundle.git"
    }
  ]
}
```

Install the bundle

`composer req qossmic/twig-doc-bundle`

### Routing

As symfony never creates routes for bundles, you need to configure this on your own!

Create a config file: configs/routes/twig_doc.yaml

```yaml
twig_doc:
  resource: '@TwigDocBundle/config/routing/documentation.xml'
  prefix: /twig/doc
  # or for localized: prefix: /{_locale}/twig/doc/
```

### Customizing the design

To customize the design of the documentation, you can override any template of the bundle in your project.

See: [How to override any Part of a Bundle](https://symfony.com/doc/current/bundles/override.html)
