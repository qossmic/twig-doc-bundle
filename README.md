## Twic Doc Bundle

Allows you to create an overview for your Twig Components, be it either UX-Components, UX-Live-Components or simple snippet templates.

Components will be grouped in categories and optional sub-categories.

The categories must be configured in the bundle-configuration, see below.

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
  resource: '@TwigDocBundle/Resources/config/routing/documentation.xml'
  prefix: /twig/doc
  # or for localized: prefix: /{_locale}/twig/doc/
```

### Configuration

Create a config file: configs/packages/twig_doc.yaml

#### Configure categories

```yaml
twig_doc:
  categories:
    - name: Button
      sub_categories:
        - Action
        - Submit
    - name: Heading
    - name: Notification
      sub_categories:
        - Alert
        - Flash
#... component config
```

#### Example for a Twig UX-Component

```yaml
twig_doc:
  #... categories config
  components:
    - name: ActionButton # will render %kernel.project_dir%/templates/components/ActionButton.html.twig
      title: Action Button
      description: An Action button
      category: Buttons
      sub_category: Action
      tags:
        - buttons
      parameters:
        color: String
        text: String
        link: String
      variations:
        secondary:
          color: secondary
          text: Secondary Button
          link: '#'
        primary:
          color: primary
          text: Primary Button
          link: '#'
```
The bundle will look for this component in the folder configured for the ux-twig-component bundle (default: %kernel.project_dir%/templates/components/COMPONENT_NAME.html.twig).

#### Example for a non ux-twig-component

The only difference is that non-ux components use another default-path and the naming is not specified.

```yaml
twig_doc:
  #... categories config
  components:
    - name: snippets/alert # will render %kernel.project_dir%/templates/snippets/alert.html.twig
      title: Alert
      description: non twig-ux-component component
      category: Notification
      sub_category:
        - Alert
      tags:
        - highlight
        - nameIt
      parameters:
        type: String
        msg: String
      variations:
        primary:
          type: primary
          msg: Primary Alert
        danger:
          type: danger
          msg: Danger Alert
```
The bundle will look for this template in the twig template folder (default: %kernel.project_dir%/templates/COMPONENT_NAME.html.twig).
