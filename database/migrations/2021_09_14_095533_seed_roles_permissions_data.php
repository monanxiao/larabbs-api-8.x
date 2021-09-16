<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolesPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 先创建权限
        Permission::create(['name' => 'manage_contents']);// 内容管理
        Permission::create(['name' => 'manage_users']); // 用户管理
        Permission::create(['name' => 'edit_settings']);// 站点设置

        // 创建站长角色，并赋予权限
        $founder = Role::create(['name' => 'Founder']);// 站长角色
        $founder->givePermissionTo('manage_contents'); // 内容权限
        $founder->givePermissionTo('manage_users');// 用户权限
        $founder->givePermissionTo('edit_settings');// 站点设置

        // 创建管理员角色，并赋予权限
        $maintainer = Role::create(['name' => 'Maintainer']);// 管理员角色
        $maintainer->givePermissionTo('manage_contents');// 内容管理
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
}
