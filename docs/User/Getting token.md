# Getting a token.
Getting a token for using with this platform is quite easy.  
Simply POST a request to `/api/v1/login`  and pass the following data in the body as JSON:
```json
{
	"email": "rickard@ahlstedt.xyz",
	"password": "Test123"
}
```
And you will get the following response:
```json
{
  "access_token": "1|0Oql616g0dPkZQPR7prmjiJ4YiR5DHpofoW9JIg8",
  "token_type": "Bearar",
  "user": {
    "id": 1,
    "name": "rickard",
    "email": "rickard@ahlstedt.xyz",
    "email_verified_at": null,
    "role": "administrator",
    "created_at": null,
    "updated_at": null
  }
}
```
