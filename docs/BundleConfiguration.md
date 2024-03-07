## Configuring the bundle

When you do not want to customize, you do not need any config file. 

The bundle provides following defaults:

```yaml
twig_doc:
  doc_identifier: TWIG_DOC
  directories:
    - '%twig.default_path%/components'
  categories:
    - name: Components
```

### Directories

By default, the bundle looks for your components in this directory: `%twig.default_path%/components`

You can provide additional directories in the config-file:

```yaml
twig_doc:
    directories:
      - '%twig.default_path%/snippets'
      - '%kernel.project_dir%/resources/components'
```

### Documentation identifier

By default, the bundle uses this identifier: `TWIG_DOC`

To use another one:

```yaml
twig_doc:
  doc_identifier: 'MY_DOC_IDENTIFIER'
```

In your component template, you can then mark up your documentation in the template:

```twig
{#MY_DOC_IDENTIFIER
title: My component
...
MY_DOC_IDENTIFIER#}
<div class="fancy-component"></div>
```

### Categories

The bundle groups components into categories and optionally into sub-categories.

Example:

```yaml
twig_doc:
  categories:
    - name: Buttons
      sub_categories:
        - Action
        - Submit
    - name: Headings
    - name: Alerts
...
```

The default category is always merged into the configuration.
