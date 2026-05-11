<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Profil;
use App\Models\User;

class ProfilPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->type === 'admin';
    }

    private function isEditeur(User $user): bool
    {
        return in_array($user->type, ['admin', 'editeur'], true);
    }

    private function owns(User $user, Profil $profil): bool
    {
        return (int) $profil->user_id === (int) $user->id;
    }

    public function viewAny(User $user): bool
    {
        return $this->isEditeur($user);
    }

    public function view(User $user, Profil $profil): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isEditeur($user) && $this->owns($user, $profil);
    }

    public function create(User $user): bool
    {
        return $this->isEditeur($user);
    }

    public function update(User $user, Profil $profil): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isEditeur($user) && $this->owns($user, $profil);
    }

    public function delete(User $user, Profil $profil): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Profil $profil): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Profil $profil): bool
    {
        return $this->isAdmin($user);
    }

    public function approve(User $user, Profil $profil): bool
    {
        // Un éditeur ne peut pas approuver son propre profil.
        return $this->isAdmin($user);
    }

    public function reject(User $user, Profil $profil): bool
    {
        return $this->isAdmin($user);
    }

    public function archive(User $user, Profil $profil): bool
    {
        return $this->isAdmin($user);
    }

    public function import(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function viewAudit(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
