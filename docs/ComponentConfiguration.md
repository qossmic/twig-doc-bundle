### Component Configuration

You have two possibilities to let the bundle know of your components:

1. Directly in the template of the component itself (you should stick to this)
2. In the config file

You can use both possibilities, but it is recommended to use only one to avoid scattering documentation over different places.

When you do not provide a category for the component, it will be added to the default-category.

#### In Template

We "abused" the comment tag from twig to allow the component configuration directly in the template.
This won't hurt twig at all as comments are totally ignored by the twig-parser.

When providing the config in the template, you do not need to provide the name, path or renderPath of it, this is automatically resolved from the template files.

```twig
{#TWIG_DOC
    title: Fancy Button
    description: This is a really fancy button
    category: Buttons
    tags:
      - button
    parameters:
      type: String
      text: String
    variations:
      primary:
        type: primary
        text: Hello World
      secondary:
        type: secondary
        text: Welcome to Hell!
#TWIG_DOC}

<button class="btn btn-{{ type }}">{{ text }}</button>
```

#### Config file

This is only recommended for small sets of components.

```yaml
...
components:
  - name: Button
    title: Fancy Button
    description: This is a really fancy button
    category: Buttons
    path: '%twig.default_path%/components/Button.html.twig'
    renderPath: 'components/Button.html.twig'
    tags:
      - button
    parameters:
      type: String
      text: String
    variations:
      primary:
        type: primary
        text: Hello World
      secondary:
        type: secondary
        text: Welcome to Hell!
```
