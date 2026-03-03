---
name: Neuron AI Structured Output
description: Use when extracting typed data from unstructured text, creating JSON schemas from PHP classes, or validating AI responses.
---

# Neuron AI Structured Output

Extract typed, validated data from text by defining output classes.

## Basic Usage

```php
class Person
{
    public string $name;
    public int $age;
    public string $email;
}

$person = $agent->structured(
    new UserMessage('John is 30, email is john@example.com'),
    Person::class
);

// Returns Person object with populated properties
```

## Supported Types

- Scalar: `string`, `int`, `float`, `bool`
- Arrays: `string[]`, `int[]`, `array`
- Objects: Nested classes
- Enums: PHP 8.1+ enums
- Nullable: `?string`, `?int`

## Validation Attributes

```php
use NeuronAI\StructuredOutput\Validation\{Required, Email, MinLength};

class User
{
    #[Required]
    public string $name;

    #[Email]
    public string $email;

    #[MinLength(8)]
    public string $password;
}
```

## Complex Objects

```php
class Address { public string $street; public string $city; }

class Person
{
    #[SchemaProperty(description: 'Full name of the user.', required: true)]
    public string $name;

    #[SchemaProperty(description: 'Residency address of the user', required: true)]
    public Address $address;

    #[SchemaProperty(
        description: 'Previous addresses of the user',
        anyOf: [Address::class]
    )]
    public Address[] $previousAddresses;
}
```

## Configuration

```php
// Retry if validation fails
$agent->structured($message, Person::class, maxRetries: 3);
```

## Best Practices

- Use public properties
- Add SchemaProperty attributes for schema specification
- Apply validation attributes to allow the framework to validate the output and give error feedback to the model
- Set appropriate retry limits
- Provide clear instructions about the output format
