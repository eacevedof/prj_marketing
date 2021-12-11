<?php

namespace App\Components\Auth;
use App\Components\Session\SessionComponent;
use App\Enums\ProfileType;
use App\Factories\SessionFactory as SF;
use App\Enums\KeyType;

final class AuthComponent
{
    private SessionComponent $session;
    private ?array $user;
    
    public function __construct()
    {
        $this->session = SF::get();
        $this->user = $this->session->get(KeyType::AUTH_USER);
    }

    public function get_user(): ?array
    {
        return $this->user;
    }

    public function is_user_logged(): bool
    {
        return (bool)($this->user["id"] ?? "");
    }

    public function is_user_allowed(string $action): bool
    {
        if($this->is_root()) return true;
        
        $permissions = SF::get()->get(KeyType::AUTH_USER_PERMISSIONS);
        return in_array($action, $permissions);
    }

    public function is_root(): bool
    {
        return (($this->user["id_profile"] ?? "") === ProfileType::ROOT);
    }

    public function is_sysadmin(): bool
    {
        return (($this->user["id_profile"] ?? "") === ProfileType::SYS_ADMIN);
    }

    public function is_business_owner(): bool
    {
        return (($this->user["id_profile"] ?? "") === ProfileType::BUSINESS_OWNER);
    }

    public function is_business_manager(): bool
    {
        return (($this->user["id_profile"] ?? "") === ProfileType::BUSINESS_MANAGER);
    }
}