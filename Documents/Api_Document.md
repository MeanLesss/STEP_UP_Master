# Login API

`---> URL`
```
  "url": "http://127.0.0.1:8000/api/login",
```
`---> Header`
```
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer ${token}"
  },
```
`---> Data`
```
  "data": JSON.stringify({
    "email": "test@test.com",
    "password": "1232"
  }),
```
`---> What it look like (jquery)`
```
var settings = {
  "url": "http://127.0.0.1:8000/api/login",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer ${token}"
  },
};
$.ajax(settings).done(function (response) {
  console.log(response);
});
```
`---> Return Success`
```
{
    "verified": true,
    "status": "success",
    "msg": "Login Successfully",
    "error_msg": "",
    "user_token": "11|xQ6NhyZ0iLUUK03WhlR9ONW2mOELLR6EUivdq2uC816e470b"
}
```
`---> Return Error`
```
{ 
    "verified": false,
    "status": "error",
    "msg": "",
    "error_msg": "The provided credentials are incorrect."
} 
```

# Login As Guest API

`---> URL`
```
  "url": "http://127.0.0.1:8000/api/guest",
```
`---> Header`
```
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer ${token}"
  },
```
`---> Data`
```
  "data": JSON.stringify({
    "guest": true,
    "email": "",
    "password": ""
  }),
```
`---> What it look like (jquery)`
```
var settings = {
  "url": "http://127.0.0.1:8000/api/guest",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer ${token}"
  },
};
$.ajax(settings).done(function (response) {
  console.log(response);
});
```
`---> Return Success`
```
{
    "verified": true,
    "status": "success",
    "msg": "Login Successfully",
    "error_msg": "",
    "user_token": "11|xQ6NhyZ0iLUUK03WhlR9ONW2mOELLR6EUivdq2uC816e470b"
}
```
`---> Return Error`
```
{ 
    "verified": false,
    "status": "error",
    "msg": "",
    "error_msg": "The provided credentials are incorrect."
} 
```




# User API

`---> URL`
```
  "url": "http://127.0.0.1:8000/api/user",
```
`---> Header`
```
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer O41q3hH3AxK5xCgVm23uT5uBDQ6Y4nBxH6zAfSaa219275d23"
  },
``` 
`---> What it look like (jquery)`
```
 var settings = {
  "url": "http://127.0.0.1:8000/api/user",
  "method": "GET",
  "timeout": 0,
  "headers": {
    "Authorization": "Bearer O41q3hH3AxK5xCgVm23uT5uBDQ6BxH6zAfSaa21927asdf5d7",
    "Content-Type": "application/json"
  },
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```
`---> Return Success`
```
{ 
    "id": 1,
    "name": "admin",
    "email": "admin@admin.com",
    "email_verified_at": "2023-11-22T00:00:00.000000Z",
    "created_at": "2023-11-22T00:00:00.000000Z",
    "updated_at": "2023-11-22T00:00:00.000000Z"
}
```
`---> Return Error`
```

```

# User Sign UP API

`---> URL`
```
  "url": "http://127.0.0.1:8000/api/signup",
```
`---> Header`
```
  "headers": {
    "Content-Type": "application/json", 
  },
``` 
`--->Data (User)`
```
"data": JSON.stringify({
    "guest": false,
    "name": "testmean",
    "email": "testmean@admin.com",
    "password": "123",
    "confirm_password": "123"
  }),
```
`--->Data (Guest)`
```
 "data": JSON.stringify({
    "guest": true,
    "name": "testmean",
    "email": "",
    "password": "",
    "confirm_password": ""
  }),
```
 
`---> What it look like (jquery)`
```
var settings = {
  "url": "http://127.0.0.1:8000/api/signup",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json"
  },
  "data": JSON.stringify({
    "guest": false,
    "name": "testmean",
    "email": "testmean@admin.com",
    "password": "123",
    "confirm_password": "123"
  }),
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```
`---> Return Success`
```
{
    "verified": true,
    "status": "success",
    "msg": "Sign up as guest Successfully",
    "error_msg": "",
    "user_token": "ihKK6O6tbhCrinLWotzLywE55esdxgdUEsdf0vJsB8901600abb"
}
```
`---> Return Error`
```
{
    "verified": false,
    "status": "error",
    "msg": "Sign up failed!",
    "error_msg": "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'tets@admin.com' for key 'users.users_email_unique' (Connection: mysql, SQL: ins..."
}
```

# User Update include Guest 

`---> URL`
```
  "url": "http://127.0.0.1:8000/api/user/update",
```
`---> Header`
```
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer 109|YPoIvfAGXKpg07rf9DrRLzrpz5nUpWZqTOYjN7aE13658ca8"
  },
``` 
`--->Data (User)`
```
"data": JSON.stringify({
    "guest": false,
    "name": "testmean",
    "email": "testmean@admin.com",
    "password": "123",
    "confirm_password": "123"
  }),
```
`--->Data (Guest)`
```
"data": JSON.stringify({
    "guest": true,
    "name": "tets21",
    "email": "tets122@admin.com",
    "password": "123",
    "confirm_password": "123"
  }),
```
 
`---> What it look like (jquery)`
```
var settings = {
  "url": "http://127.0.0.1:8000/api/user/update",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json",
    "Authorization": "Bearer 109|YPoIvfAGXKpg07rf9DrRLzrpz5nUpWZqTOYjN7aE13658ca8"
  },
  "data": JSON.stringify({
    "guest": true,
    "name": "tets21",
    "email": "tets122@admin.com",
    "password": "123",
    "confirm_password": "123"
  }),
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```
`---> Return Success`
```
{
    "verified":true,
    "status":"success",
    "msg":"Update Successfully!",
    "error_msg":""
}
```
`---> Return Error`
```
{
   'error' => 'Authenticated failed! Please try again!'
}
```
