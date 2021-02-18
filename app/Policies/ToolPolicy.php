<?php

namespace App\Policies;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ToolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Tool $tool
     * @return mixed
     */
    public function view(User $user, Tool $tool)
    {
        return $user->id === $tool->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tool $tool
     * @return mixed
     */
    public function update(User $user, Tool $tool)
    {
        return $user->id === $tool->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tool $tool
     * @return mixed
     */
    public function delete(User $user, Tool $tool)
    {
        return $user->id === $tool->user_id;
    }
}
