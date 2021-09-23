# Creating a user
To create a user, your account must have the role of administrator.

Then pass a POST request to `/api/v1/register`.

Body:
```json
{
	"name": "Red",
	"email": "email",
	"password": "password with a minimum of 6 character",
	"role": "user/administrator"
}
```
