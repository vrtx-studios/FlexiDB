# Updating a row

## Example
```json
{
	"actions": [
		{
			"action": "update",
			"table": "players",
			"fields": [ "name" ],
			"values": [
					{
							"name": "Blue"
					}
			],
			"selector": "id",
			"value": 2
		}	
	]
}
```
