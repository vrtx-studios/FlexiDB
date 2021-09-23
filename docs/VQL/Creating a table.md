# Creating a table using VQL
To create a table using VQL, you define the action as `create`.
The tablename is specified in the parameter `table``

The columns to be created are to be specified in the parameter `fields`, see the bottom of the page for an example.

The parameter `key` is used to identify which column to be the primary key.

## Field-types
The values used in the `type`-parameter for each field in fields.

### integer
Property|values|description
:-|-|-
name| `<string>` | Defines the name of the column to be created
nullable| `true\|false`| Determines of the field is allowed be null, default: true
autoinc| `true\|false`| Determines if the field should automatically increase in value, often used with `key`-attribute

### string
Property|values|description
:-|-|-
name| `<string>` | Defines the name of the column to be created
length| `<int>` | Determines how many characters the field is allowed to have, exclude the parameter to use the default setting of 100 characters
nullable| `true\|false`| Determines of the field is allowed be null, default: true

### double
Property|values|description
:-|-|-
name| `<string>` | Defines the name of the column to be created
precision| `<float>`| Defines the precision of the double-value, exclude to use the default which is 15.
nullable| `true\|false`| Determines of the field is allowed be null, default: true

### array
Property|values|description
:-|-|-
name| `<string>` | Defines the name of the column to be created
values| `[string,string]`| Array of strings that are allowed in the array
default| `<string>`| Which default value the column should have, exclude if you plan on setting this when posting the request

### datetime
Property|values|description
:-|-|-
name| `<string>` | Defines the name of the column to be created
precision| `<int>`|How many fractions of a microsecond to store, exclude if not needed
default| `<string>`| The default value, to use the current timestamp, specify `now`
nullable| `true\|false`| Determines of the field is allowed be null, default: true


## Example
```json
{
	"actions": [
		{
			"action": "create",
			"table": "players",
			"fields": [
					{ 
							"name": "id",
							"type": "integer",
							"nullable": true,
							"autoinc": true
					},
					{
							"name": "name",
							"type": "string"
					},
					{
							"name": "role",
							"type": "array",
							"values": [ "player", "admin" ],
							"default": "player"
					},
					{
						"name": "money",
						"type": "double",
						"precision": "3,2",
						"nullable": true
					},
					{
							"name": "created",
							"type": "datetime",
							"precision": 6,
							"default": "now"
					},
					{
							"name": "updated",
							"type": "datetime",
							"precision": 6,
							"default": "now"
					}
			],
			"key": "id"
		},
		{
			"action": "insert",
			"table": "players",
			"fields": [ "name" ],
			"values": [
					{
							"name": "Red"
					}
			]
		},
		{
			"action": "insert",
			"table": "players",
			"fields": [ "name", "role" ],
			"values": [
					{
							"name": "Fear"
					},
					{
							"role": "admin"
					}
			]
		}
	]
}
```
