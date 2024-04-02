### Component Configuration

1. [In Template](#in-template)
2. [In Configuration File](#config-file)
3. [Template Parameters](#parameter-provision)
4. [Custom Data Provider](#custom-data-provider)

You have two possibilities to let the bundle know of your components:

1. Directly in the template of the component itself (you should stick to this)
2. In the config file

You can use both possibilities, but it is recommended to use only one to avoid scattering documentation over different places.

When you do not provide a category for the component, it will be added to the default-category.

#### In Template

We "abused" the comment tag from twig to allow the component configuration directly in the template.
This won't hurt twig at all as comments are totally ignored by the twig-parser.

When providing the config in the template, you do not need to provide the name, this is automatically resolved from the template file.

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

The bundle tries to resolve the path of the template in a compiler pass based on the name of the component.

E.g.: name: Button -> bundle looks for a Button.html.twig

For this to work, you need to ensure that your components are unique among all configured directories.

```yaml
...
components:
  - name: Button
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
```

Alternatively, you can provide a path for your component in the configuration (parameters are resolved automatically):

```yaml
...
components:
  - name: Button
    path: '%twig.default_path%/snippets/FancyButton.html.twig'
```

### Parameter Provision

You must provide the types of your template parameters in the configuration. 
As twig templates are not aware of types, there is no other possibility at the moment.
As this bundle makes use of [Nelmio/Alice](https://github.com/nelmio/alice) and [FakerPhp](https://fakerphp.github.io), all you need to do is
define the types of your parameters in the component configuration.
The bundle will take care of creating a set of parameters for every component.

E.g. when your template optionally requires a User object, you can say the template needs a parameter named user that is of type App\Entity\User:
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
      user: App\Entity\User
#TWIG_DOC}

{% if user %}
    Hello {{ user.name }}
{% endif %}
<button class="btn btn-{{ type }}">{{ text }}</button>
```

As we do not provide an explicit variation, the bundle creates a default variation for this component. 
This default variation will contain a fixture for the user object, as well as random values for other parameters. 
If the property "name" of the user object is writable, the bundle will also create a random text-value for the name.

So, what to do if you want an example of both possibilities (user as object and as NULL)? Answer: provide variations for both cases:
```twig
{#TWIG_DOC
    title: Fancy Button
    description: This is a really fancy button
    category: Buttons
    tags:
      - button
    parameters:
      user: App\Entity\User
      type: String
      text: String
    variations:
      logged-in:
        user: 
          name: superadmin
        type: primary
      anonymous:
        user: null
        text: Button Text
#TWIG_DOC}

{% if user %}
    Hello {{ user.name }}
{% endif %}
<button class="btn btn-{{ type }}">{{ text }}</button>
```

For all parameters that are missing from the variations configuration, the bundle will create random-values with FakerPHP.
It is possible to mix explicitly defined parameter-values and randomly created ones.

### Custom Data Provider

This bundle comes with 3 default data providers to create fake data for your components:

- FixtureGenerator
  - creates fixtures for classes with nelmio/alice and fakerphp/faker
- ScalarGenerator
  - creates scalar values for string/bool/number parameters in your components with fakerphp
- NullGenerator
  - creates null values for any unknown type

You can easily add your own data generator by creating an implementation of `Qossmic\TwigDocBundle\Component\Data\GeneratorInterface` 
and tagging it with `twig_doc.data_generator`. The higher the priority, the earlier the generator will be used.
This works by using the ["tagged_iterator" functionality](https://symfony.com/doc/current/service_container/tags.html#tagged-services-with-priority) of symfony.
```php
#[AutoconfigureTag('twig_doc.data_generator', ['priority' => 10])]
class CustomGenerator implements GeneratorInterface
{
    public function supports(string $type, mixed $context = null): bool
    {
        return $type === Special::class;
    }

    public function generate(string $type, mixed $context = null): Special
    {
        return new Special([
            'key' => 'value',
        ]);
    }
}
```
