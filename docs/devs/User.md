# The docs for the user class

This is the documentation for the users the class.

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
- `ERROR_DATABASE_INSERT_FAILED` This means that the insert operation has failed and you have to check the logs on why that happend!

