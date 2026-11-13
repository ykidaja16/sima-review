<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HakAksesController extends Controller
{
    public function index(): View
    {
        MenuService::seedDefaultsIfEmpty();

        $roles = Role::orderByDesc('level')->get();
        $menuCatalog = MenuService::getMenuCatalog();
        $matrix = MenuService::getPermissionsMatrix();

        return view('hak-akses.index', compact('roles', 'menuCatalog', 'matrix'));
    }

    public function update(Request $request): RedirectResponse
    {
        $submitted = $request->input('permissions', []);
        $allKeys = MenuService::getAllMenuKeys();
        $roles = Role::all();

        DB::transaction(function () use ($submitted, $allKeys, $roles) {
            RoleMenuPermission::query()->delete();

            $records = [];
            $now = now();

            foreach ($roles as $role) {
                $rolePerms = $submitted[$role->id] ?? [];
                foreach ($allKeys as $key) {
                    if (!empty($rolePerms[$key])) {
                        $records[] = [
                            'role_id'    => $role->id,
                            'menu_key'   => $key,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            if (!empty($records)) {
                // Chunk insert untuk efisiensi
                foreach (array_chunk($records, 100) as $chunk) {
                    RoleMenuPermission::insert($chunk);
                }
            }

            Cache::forget('role_menu_permissions_matrix');
        });

        // Audit log
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'UPDATE',
            'model'      => 'RoleMenuPermission',
            'model_id'   => null,
            'keterangan' => 'Mengubah matriks hak akses menu masing-masing role',
        ]);

        return redirect()->route('hak-akses.index')
            ->with('success', 'Hak akses menu berhasil diperbarui.');
    }
}

