<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);

        Model::reguard();
        // $this->call('UsersTableSeeder');
        // $this->call('RolesTableSeeder');
        // $this->call('UserHasRolesTableSeeder');
        // $this->call('PermissionsTableSeeder');
        // $this->call('UserHasPermissionsTableSeeder');
        // $this->call('RoleHasPermissionsTableSeeder');
        // $this->call('ScaffoldinterfacesTableSeeder');
        // $this->call('RelationsTableSeeder');
        // $this->call('MigrationsTableSeeder');
        // $this->call('PasswordResetsTableSeeder');
        $this->call('RoutesTableSeeder');
        $this->call('RoleRouteTableSeeder');
        // $this->call('CountriesTableSeeder');
        // $this->call('OperatorsTableSeeder');
        // $this->call('TypesTableSeeder');
        // $this->call('CategoriesTableSeeder');
        // $this->call('ContentsTableSeeder');
        // $this->call('RbtsTableSeeder');
        // $this->call('PostsTableSeeder');
        // $this->call('SettingsTableSeeder');
    }
}
