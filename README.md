# FlexiDB
_When connecting straight to a database isn't an option_

## Example:

Multiple calls:
```json
{
    "actions": [
        { "create table" },
        { "seed with basic data, insert admins" },
        ...
        { "read data" }
    ]
}
```

Calls supported:
```json
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
            "type": "string",
            "length": 40,
            "nullable": true
        },
        {
            "name": "role",
            "type": "array",
            "values": [ "player", "admin" ],
            "default": "player"
        },
        {
            "name": "created",
            "type": "datetime",
            "default": "now"
        },
        {
            "name": "updated",
            "type": "datetime",
            "default": "now"
        }
    ],
    "key": "id"
}

{
    "action": "drop",
    "table": "players"
}

{
    "action": "insert",
    "table": "players",
    "fields": [ "name", "role" ],
    "values": [
        {
            "name": "Red"
        },
        {
            "role": "admin"
        }
    ]
}

{
    "action": "read",
    "table": "players",
    "fields": [ "id", "name", "role" ],
    "selector": "id",
    "value": 69
}

{
    "action": "update",
    "table": "players",
    "fields": [ "name" ],
    "values": [
        {
            "name": "Nytt namn"
        }
    ],
    "selector": "id",
    "value": 69
}

{
    "action": "delete",
    "table": "players",
    "selector": "id",
    "value": 69
}
```
