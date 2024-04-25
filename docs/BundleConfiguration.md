## Configuring the bundle

When you do not want to customize, you do not need any config file.

The bundle provides following defaults:

```yaml
twig_doc:
  doc_identifier: TWIG_DOC
  use_fake_parameter: false
  directories:
    - '%twig.default_path%/components'
  categories:
    - name: Components
  breakpoints:
    small: 240
    medium: 640
    large: 768
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

### Fake Parameters

By default, the creation of fake parameters is disabled!

When enabled, the bundle fakes parameters based on parameter-config of the component

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


### Breakpoints

To use custom breakpoints, simply provide a breakpoint-config. You can name the breakpoints as you like:

```yaml
twig_doc:
  breakpoints:
    iphone: 598
    unusual: 743
...
```
