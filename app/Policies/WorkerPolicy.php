<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use App\Models\Worker;

class WorkerPolicy
{
    /**
     * Determine whether the user can view any workers.
     */
    public function viewAny(User $user): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }
    /**
     * Determine whether the user can view the worker.
     */
    public function view(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }
    /**
     * Determine whether the user can create workers.
     */
    public function create(User $user): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }
    /**
     * Determine whether the user can update the worker.
     */
    public function update(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

    /**
     * Determine whether the user can delete the worker.
     */
    public function delete(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

    /**
     * Determine whether the user can restore the worker.
     */
    public function restore(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

    /**
     * Determine whether the user can permanently delete the worker.
     */
    public function forceDelete(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

    public function viewContract(User $user, Worker $worker): bool
    {
        return Contract::contractExists($worker->id) && in_array('MANTRA',$user->role->permissions);
    }

    public function settlement(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

    public function viewSettlement(User $user, Worker $worker): bool
    {
        return in_array('MANTRA',$user->role->permissions);
    }

}
