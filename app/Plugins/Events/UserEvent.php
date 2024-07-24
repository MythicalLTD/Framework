<?php

namespace MythicalSystemsFramework\Plugins\Events;

class UserEvent
{
    /**
     * Event: OnUserLogin
     * Description: Triggered when a user logs in.
     */
    public static function onUserLogin(): void
    {
        //This is where you can add your code to run when a user logs in
    }
    /**
     * Event: OnUserLogOut
     * Description: Triggered when a user logs out.
     */
    public static function OnUserLogOut(): void
    {
    }
    /**
     * Event: OnUserBan
     * Description: Triggered when a user gets banned.
     */
    public static function OnUserBan(): void
    {
    }
    /**
     * Event: OnUserUnBan
     * Description: Triggered when a user gets unbanned.
     */
    public static function OnUserUnBan(): void
    {
    }
    /**
     * Event: OnUserDelete
     * Description: Triggered when a user gets deleted.
     */
    public static function OnUserDelete(): void
    {
    }
    /**
     * Event: OnUserRestore
     * Description: Triggered when a user gets undeleted.
     */
    public static function OnUserRestore(): void
    {
    }
    /**
     * Event: OnUserVerify
     * Description: Triggered when a user gets verified.
     */
    public static function OnUserVerify(): void
    {
    }
    /**
     * Event: OnUserUnVerify
     * Description: Triggered when a user needs to verify again.
     */
    public static function OnUserUnVerify(): void
    {
    }
    /**
     * Event: OnUserLastSeen
     * Description: Triggered when a user gets last seen.
     */
    public static function OnUserLastSeen(): void
    {
    }
    /**
     * Event: OnUserRegister
     * Description: Triggered when a new user registers.
     */
    public static function OnUserRegister(): void
    {
    }
    /**
     * Event: OnUserUpdate
     * Description: Triggered when a user gets updated in the database.
     */
    public static function OnUserUpdate(): void
    {
    }
}
