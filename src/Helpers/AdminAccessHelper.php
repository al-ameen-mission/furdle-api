<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * AdminAccessHelper for checking admin permissions and access.
 */
class AdminAccessHelper
{
  /**
   * Check if admin has access to a menu.
   *
   * @param mixed $admin
   * @param string $menu
   * @param ?string $action
   * @return bool
   */
  public static function hasGlobalAccess($admin, string $menu, ?string $action = null): bool
  {
    $global_accesses = json_decode($admin["global_accesses"] ?? "{}");
    if (empty($global_accesses)) {
      return false;
    }
    if ($action === null || !$action || $action === '') {
      return property_exists($global_accesses, $menu);
    }
    if (!property_exists($global_accesses, $menu)) {
      return false;
    }
    $menu_access = $global_accesses->{$menu} ?? [];
    if (!in_array($action, $menu_access)) {
      return false;
    };
    return true;
  }

  /**
   * Check if admin has second level access to a menu.
   * @param mixed $admin
   * @param string $branch
   * @param string $menu
   * @param ?string $access_key
   */
  public static function hasSecondLevelAccess($admin, string $branch, string $menu, ?string $access_key = null): bool
  {
    $second_level_access = json_decode($admin['second_level_access'] ?? "{}");
    if (empty($second_level_access)) {
      return false;
    }
    if (!property_exists($second_level_access, $branch)) {
      return false;
    };
    $second_level_access = $second_level_access->{$branch} ?? [];
    if (!property_exists($second_level_access, $menu)) {
      return false;
    };
    $menu_access = $second_level_access->{$menu} ?? [];
    if ($access_key !== null && $access_key !== '' && !in_array($access_key, $menu_access)) {
      return false;
    };
    return true;
  }
}
