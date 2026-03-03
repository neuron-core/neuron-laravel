---
name: Neuron AI Tools
description: Use when creating custom tools, implementing toolkits, or configuring tools for agent capabilities.
---

# Neuron AI Tools

Tools provide callable units of functionality that agents can invoke.

## Creating a Tool

### Using the Static Constructor (Recommended)

```php
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\PropertyType;

$weatherTool = Tool::make('get_weather', 'Get weather for a city')
    ->addProperty(ToolProperty::make('city', PropertyType::STRING, 'City name', required: true))
    ->setCallback(fn(string $city): string => json_encode([
        'temp' => 25,
        'city' => $city
    ]));
```

### Using Class Extension (RECOMMENDED)

```php
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\PropertyType;

class WeatherTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'get_weather',
            description: 'Get weather for a city',
        );
    }

    protected function properties(): array
    {
        return [
            ToolProperty::make('city', PropertyType::STRING, 'City name', required: true),
        ];
    }

    public function __invoke(string $city): string
    {
        return json_encode(['temp' => 25, 'city' => $city]);
    }
}
```

## Tool Constructor Parameters

```php
Tool::__construct(
    string $name,              // Required: tool identifier
    ?string $description = null, // Optional: description for LLM
    array $properties = [],      // Optional: ToolProperty[] array
    array $parameters = [],      // Optional: additional parameters
    array $annotations = []      // Optional: metadata annotations
)
```

## Property Types

Available types:

- `PropertyType::STRING` - Text strings
- `PropertyType::NUMBER` - Floating point numbers
- `PropertyType::INTEGER` - Whole numbers
- `PropertyType::BOOLEAN` - true/false
- `PropertyType::ARRAY` - Arrays
- `PropertyType::OBJECT` - JSON objects

## Using with Agent

```php
use NeuronAI\Agent\Agent;
use NeuronAI\Tools\Toolkits\CalculatorToolkit;

$agent = Agent::make()
    ->setAiProvider($provider)
    ->addTool($weatherTool)  // Custom tool
    ->addTool(CalculatorToolkit::make());  // Built-in toolkit
```

## Built-in Toolkits

Available toolkits in `src/Tools/Toolkits/`:

- `CalculatorToolkit` - Mathematical calculations
- `MySQLToolkit` - Database queries
- `PostgreSQLToolkit` - PostgreSQL queries
- `TavilyToolkit` - Web search via Tavily
- `ZepToolkit` - Zep memory store
- `AwsSESToolkit` - Email via AWS SES
- `JinaToolkit` - Jina AI services
- `SupadataToolkit` - Supadata services
- `FileSystemToolkit` - File system operations

## Tool Callback Configuration

```php
// Using closure
$tool->setCallback(fn(string $input): string => process($input));

// Using class method
$tool->setCallback([$processor, 'handle']);

// Using static method
$tool->setCallback([MyClass::class, 'staticMethod']);
```

## Tool Callback Parameters

The callback receives parameters as named arguments:

```php
Tool::make('search', 'Search database')
    ->addProperty(ToolProperty::make('query', PropertyType::STRING, required: true))
    ->addProperty(ToolProperty::make('limit', PropertyType::INTEGER, required: false))
    ->setCallback(function(string $query, int $limit = 10): string {
        return "Searching for: $query (limit: $limit)";
    });
```

## Key File Locations

- `src/Tools/ToolInterface.php` - Tool contract
- `src/Tools/Tool.php` - Base tool implementation
- `src/Tools/ToolProperty.php` - Property definition
- `src/Tools/Toolkits/` - Built-in toolkits

## Best Practices

- **Single purpose**: Each tool should do one thing well
- **Clear naming**: Use descriptive tool names (e.g., `get_weather` not `tool1`)
- **Detailed descriptions**: Help LLM understand when and how to use the tool
- **JSON responses**: Return JSON for easy parsing by the LLM
- **Error handling**: Return structured error messages instead of throwing exceptions
- **Required vs optional**: Mark only truly required parameters as required
- **Enums for validation**: Use `setEnum()` to restrict valid values

## Tool Callback Signature

```php
/**
 * Tool callback function signature.
 *
 * @param mixed ...$params Named parameters matching tool properties
 * @return string Tool result (must be string)
 */
function toolCallback(mixed ...$params): string
{
    // Process and return result
}
```
