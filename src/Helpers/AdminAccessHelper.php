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
   * @param string|null $action
   * @return bool
   */
  public static function hasGlobalAccess($admin, string $menu, string $action = null): bool
  {
    $global_accesses = json_decode($admin["global_accesses"] ?? "{}");
    if (empty($global_accesses)) {
      return false;
    }
    if ($action === null) {
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
   * @param string|null $action
   */
  public static function hasSecondLevelAccess($admin, string $branch, string $menu, string $action = null): bool
  {
    // TODO: Implement logic to check second level access
    return true;
  }

}
