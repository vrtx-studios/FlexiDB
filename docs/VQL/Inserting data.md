# Inserting data

See the following example.

## Example
```json
{
	"actions": [
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
