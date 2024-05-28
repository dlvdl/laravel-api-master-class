<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{
    public const CreateTicket = 'ticket:create';
    public const UpdateTicket = 'ticket:update';
    public const ReplaceTicket = 'ticket:replace';
    public const DeleteTicket = 'ticket:delete';

    public const DeleteOwnTicket = 'ticket:own:delete';
    public const UpdateOwnTicket = 'ticket:own:update';
    public const CreateOwnTicket = 'ticket:own:create';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    public static function getAbilities(User $user)
    {
        if ($user->is_manager) {
            return [
                self::CreateUser,
                self::UpdateUser,
                self::ReplaceUser,
                self::DeleteUser,
                self::CreateTicket,
                self::UpdateTicket,
                self::ReplaceTicket,
                self::DeleteTicket,
            ];
        } else {
            return [
                self::CreateOwnTicket,
                self::DeleteOwnTicket,
                self::UpdateOwnTicket,
            ];
        }
    }
}
