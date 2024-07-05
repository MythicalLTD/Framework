## Roles Documentation

This documentation provides an overview of the Roles functionality in the MythicalSystemsFramework.

### Creating a Role

To create a role, you can use the `create` function provided by the `RolesDataHandler` class. This function accepts two parameters:

- `name` (string): The name of the role.
- `weight` (int, optional): The weight of the role. The default value is 1.

Please ensure that you perform input validation before calling this function. The function does not perform any input validation itself. It is recommended to validate inputs such as the role name and weight.

The function returns the role ID as a string if the role is created successfully. If the role already exists in the database, it returns the error message `ERROR_ROLE_EXISTS`. If the insert operation fails, it returns the error message `ERROR_DATABASE_INSERT_FAILED`.

### Deleting a Role

To delete a role, you can use the `delete` function provided by the `RolesDataHandler` class. This function accepts one parameter:

- `id` (int): The ID of the role to be deleted.

Before deleting the role, the function checks if the role exists using the `roleExists` function. If the role does not exist, it returns the error message `ROLE_MISSING`.

The function returns the message `ROLE_DELETED` if the role is deleted successfully. If the delete operation fails, it returns the error message `ROLE_DELETE_FAILED`.

### Updating a Role

To update a role, you can use the `update` function provided by the `RolesDataHandler` class. This function accepts three parameters:

- `id` (int): The ID of the role to be updated.
- `name` (string): The new name of the role.
- `weight` (int, optional): The new weight of the role. The default value is 1.

Before updating the role, the function checks if the role exists using the `roleExists` function. If the role does not exist, it returns the error message `ROLE_MISSING`.

The function returns the message `ROLE_UPDATED` if the role is updated successfully. If the update operation fails, it returns the error message `ROLE_UPDATE_FAILED`.

### Getting Role Information

To get specific information about a role, you can use the `getSpecificRoleInfo` function provided by the `RolesDataHandler` class. This function accepts two parameters:

- `id` (int): The ID of the role.
- `data` (string): The specific data you are looking for.

Before getting the role information, the function checks if the role exists using the `roleExists` function. If the role does not exist, it returns the error message `ROLE_MISSING`.

The function returns the requested role information as a string. If the select operation fails, it returns the error message `ERROR_DATABASE_SELECT_FAILED`.

### Checking if a Role Exists

To check if a role exists, you can use the `roleExists` function provided by the `RolesDataHandler` class. This function accepts one parameter:

- `id` (int): The ID of the role.

The function returns the message `ROLE_EXISTS` if the role exists. If the role does not exist, it returns the message `ROLE_MISSING`.
