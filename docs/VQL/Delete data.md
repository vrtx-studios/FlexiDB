# Deleting data

Use `selector` to specify which column you like to base your query on.  
Use `value` as the value to query on, if set to an asterix, VQL will query all rows.


## Example
```json
{
	"actions": [
		{
			"action": "delete",
			"table": "players",
			"selector": "id",
            "value": "*"
		}	
	]
}
```
