# The docs for the user class

This is the documentation for the users the class.

### What data is encrypted

When you are using the framework there is a `server end` encryption

- `username`
- `first_name`
- `last_name`
- `email`
- `password`
- `first_ip`
- `last_ip`

:::: WARNING ::::

Some of our functions automatically decrypt the data by default any you may have to set the params to `false`!

## Creating a user

When you are using the create user function there are some things you may need to know!

This function does not perform any input validation so please always make sure you perform
input validation like if the email is right or the password is a strong one and so on!

There are anti SQL injection methods what so ever!

When using this function you can either get the user token: `mythicalframework_` in this format of the begging or you may get and error!
Every error starts with an `ERROR_` incase something fails!

Here are a list of errors that you may get!

- `ERROR_USERNAME_EXISTS` This means that the username was found in the database already
- `ERROR_EMAIL_EXISTS` This means that the email was found in teh database already
- `ERROR_DATABASE_INSERT_FAILED` This means that the insert operation has failed and you have to check the logs on why that happened!

## Login a user in

When you are using the login user function there are some things you may need to know!
THIS FUNCTION SUPPORTS BOTH `usernames` & `emails` to log the user in!!!!!!!!!

This function does not perform any input validation so please always make sure you perform
input validation like if the email is right or the password is a strong one and so on!

There are anti SQL injection methods what so ever!

When using this function you can either get the user token: `mythicalframework_` in this format of the begging or you may get and error!
Every error starts with an `ERROR_` incase something fails!

Here are a list of errors that you may get!

- `ERROR_USER_NOT_FOUND` This means that the email or username was not found in the database!!
- `ERROR_PASSWORD_INCORRECT` This just means that the password is wrong but the user was found in the database!
- `ERROR_DATABASE_SELECT_FAILED` This is a database select error please check the logs!!!!
- `ERROR_USER_BANNED` The user is banned!
- `USER_NOT_VERIFIED`: Indicates that the user is not verified.
- `ERROR_USER_DELETED` Indicates that the user is deleted.

## Check a user's session

With the function called: `isSessionValid` you can check if the user session is valid by checking if the token that the user has matches with the one in the database!

This function will only return `true` or `false`!!

If it returns `false` make sure you will execute the function [killSession](#killing-a-users-session)
And after make sure you redirect the user to the `/auth/login` page!

## Killing a user's session!

This is just the function to kill the users session

It is just a function that will delete the cookie token value

## Getting information about a user!

With the function called `getSpecificUserData` you can get information's about a specific user

When using this function you can use 3 parameters

- `account_token` The token of the user you are looking for!
- `data` The column name from the `framework_users` table
- `encrypted` If set to true will decrypt the data from table!

You may get some errors when you are trying to use this functions and here is a list with the errors you may get!

- `ERROR_ACCOUNT_NOT_VALID` Means that the token provided was wrong and does not match any account!
- `ERROR_FIELD_NOT_FOUND` Means the column that you specified does not exist in the table!
- `ERROR_DATABASE_SELECT_FAILED` Something failed when executing the SQL script in the database!

If you get no errors this means you got some valid data!

## Updating the user info in the database!

This is in case you want to update a user inside the database!

When using this function you can use 4 parameters

- `account_token` The token of the user you are looking for!
- `data` The column name from the `framework_users` table!
- `value` The value you want to set to the users table!
- `encrypted` If set to true will decrypt the data from table!

You may get some errors when you are trying to use this functions and here is a list with the errors you may get!

- `ERROR_ACCOUNT_NOT_VALID` Means that the token provided was wrong and does not match any account!
- `ERROR_DATABASE_SELECT_FAILED` Something failed when executing the SQL script in the database!

And if you are getting `SUCCESS` as a return that means that you are good!

## Checking if a user is banned

To check if a user is banned, you can use the `isUserBanned` function. This function takes the `account_token` of the user as a parameter and returns a string indicating whether the user is banned or not.

To use this function, pass the `account_token` of the user you want to check. It will return one of the following strings:

- `USER_NOT_BANNED`: Indicates that the user is not banned.
- `USER_BANNED`: Indicates that the user is banned.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.
- `ERROR_DATABASE_SELECT_FAILED`: Indicates that there was an error executing the SQL script in the database.

Remember to handle the return value appropriately in your code.

## Banning a user

To ban a user from the system, you can use the `banUser` function. This function takes two parameters:

- `account_token`: The account token of the user you want to ban.
- `reason`: The reason why you are banning the user.

The function will return a string indicating the response of the ban. Here are the possible return values:

- `USER_BANNED`: Indicates that the user has been successfully banned.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.

## Unbanning a user

To unban a user from the system, you can use the `unbanUser` function. This function takes one parameter:

- `account_token`: The account token of the user you want to unban.

The function will return a string indicating the response of the unban. Here are the possible return values:

- `USER_UNBANNED`: Indicates that the user has been successfully unbanned.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.


## Deleting a user

To delete a user from the system, you can use the `deleteUser` function. This function takes one parameter:

- `account_token`: The account token of the user you want to delete.

The function will return a string indicating the response of the delete. Here are the possible return values:

- `USER_DELETED`: Indicates that the user has been successfully deleted.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.

## Restoring a user

To restore a soft deleted user in the system, you can use the `restoreUser` function. This function takes one parameter:

- `account_token`: The account token of the user you want to restore.

The function will return a string indicating the response of the restore. Here are the possible return values:

- `USER_RESTORED`: Indicates that the user has been successfully restored.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.

## Checking if a user is deleted

To check if a user is soft deleted, you can use the `isUserDeleted` function. This function takes the `account_token` of the user as a parameter and returns a string indicating whether the user is deleted or not.

To use this function, pass the `account_token` of the user you want to check. It will return one of the following strings:

- `USER_NOT_DELETED`: Indicates that the user is not deleted.
- `USER_DELETED`: Indicates that the user is deleted.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.
- `ERROR_DATABASE_SELECT_FAILED`: Indicates that there was an error executing the SQL script in the database.

Remember to handle the return value appropriately in your code.

## Checking if a user is verified

To check if a user is verified, you can use the `isUserVerified` function. This function takes the `account_token` of the user as a parameter and returns a string indicating whether the user is verified or not.

To use this function, pass the `account_token` of the user you want to check. It will return one of the following strings:

- `USER_NOT_VERIFIED`: Indicates that the user is not verified.
- `USER_VERIFIED`: Indicates that the user is verified.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.
- `ERROR_DATABASE_SELECT_FAILED`: Indicates that there was an error executing the SQL script in the database.

Remember to handle the return value appropriately in your code.

## Verifying a user

To verify a user, you can use the `verifyUser` function. This function takes the `account_token` of the user as a parameter and returns a string indicating the response of the verification.

To use this function, pass the `account_token` of the user you want to verify. It will return one of the following strings:

- `USER_VERIFIED`: Indicates that the user has been successfully verified.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.

## Unverifying a user

To unverify a user, you can use the `unverifyUser` function. This function takes the `account_token` of the user as a parameter and returns a string indicating the response of the unverification.

To use this function, pass the `account_token` of the user you want to unverify. It will return one of the following strings:

- `USER_UNVERIFIED`: Indicates that the user has been successfully unverified.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.

## Updating the last seen and last IP of a user

To update the last seen and last IP of a user, you can use the `updateLastSeen` function. This function takes two parameters:

- `account_token`: The account token of the user you want to update.
- `ip`: The IP address of the user.

The function will return a string indicating the response of the update. Here are the possible return values:

- `SUCCESS`: Indicates that the last seen and last IP of the user have been successfully updated.
- `ERROR_DATABASE_UPDATE_FAILED`: Indicates that there was an error updating the database.
- `ERROR_ACCOUNT_NOT_VALID`: Indicates that the provided `account_token` is not valid.

Make sure to handle the return value appropriately in your code.
