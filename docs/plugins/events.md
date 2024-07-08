# MythicalFramework Events Handler

This is a class where you can use the events like: user logins page load and much more!

Here is a list of events that you can use:


- `OnUserLogin` When the user logs in
- `OnUserLogOut` When the user logs out
- `OnUserBan` When the user gets banned
- `OnUserUnBan` When the user get unbanned 
- `OnUserDelete` When a user gets deleted
- `OnUserRestore` When a user gets undeleted
- `OnUserVerify` When a user gets verified
- `OnUserUnVerify` When a user needs to verify again
- `OnUserLastSeen` When a user gets last seen
- `OnUserRegister` When a new user registers
- `OnUserUpdate` When a user gets updated in the database
- `OnRoleCreate` When a new role gets created
- `OnRoleUpdate` When a role gets updated
- `OnRoleDelete` When a role gets deleted
- `OnPermissionCreate` When a new permission gets created
- `OnPermissionUpdate` When a permission gets updated
- `OnPermissionDelete` When a permission gets deleted
- `OnConfigUpdate` When the config file gets updated
- `OnConfigSet` When a new value gets set to the config
- `OnConfigRemove` When a new config value gets removed!
- `OnLanguageLoad` When the language loads
- `OnTranslationKeyNotFound` When a translation code key is not found!
- `OnSettingUpdate` When is getting updated in the database
- `OnSettingsDelete` When a setting is getting deleted form the database!
- `OnSettingsSet` When a new setting is getting set to the database!
- `OnNewUserID` When a new user id is getting created!
- `OnEncrypt` When a new value gets encrypted
- `OnDecrypt` When a new value gets decrypted
- `OnAddActivity` When a new activity gets added to the database!
- `OnRemoveUserActivities` When a user id activity is getting purged!
- `OnRemoveAllActivities` When all activity's get purged!
- `OnNewAnnouncement` When there is a new Announcement
- `OnAnnouncementEdit` When a Announcement get edited
- `OnAnnouncementDelete` When a new Announcement gets deleted
- `OnAnnouncementDeleteAll` When every Announcement gets deleted!
- `OnAnnouncementRemoveDisLike` When a new Announcement dislike gets removed!
- `OnAnnouncementRemoveLike` When an Announcement gets his liked removed!
- `OnAnnouncementDisLike` When a new Announcement gets disliked!
- `OnAnnouncementLike` When an Announcement gets liked!
- `OnAnnouncementRead` When an Announcement gets read!
- `OnNewNotification` When a new notification gets created! 
- `OnNotificationEdit` When a Notification get edited
- `OnNotificationDelete` When a new Notification gets deleted
- `OnNotificationDeleteAll` When every Notification gets deleted!
- `OnNotificationRead` When a notification gets read!
- `OnDatabaseConnect` When something connects to the database!
- `OnCLICommand` When there is a cli command executed!
- `OnException` When something fails
- `OnPageLoad` When a page loads
- `OnCronTabRun` When a crontab gets executed!
- `OnDatabaseMigration` When the database gets migrated!
- `OnSettingMigration` When the config gets migrated!
- `OnUnitTest` When a unit test gets tested!
- `OnRouterLoad` When the router loads!
- `OnApplicationDown` When the application goes down
- `OnApplicationUp` When teh application goes up
- `OnApplicationStartup` When the app starts!
- `OnRendererLoad` When the renderer starts!
- `OnLog` When something gets logged!
- `onLogsPurge` When the logs get purged!