<?php

namespace App\Http\Controllers;

use App\Models\PermissionCategory;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use DB;
use Mail;
use Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function list(){
        $roles = Role::all();
        return view('admin.role.list', compact('roles'));
    }

    public function index(){
        return view('admin.role.index');
    }

    public function create(Request $request){
        $data = $request->all();

        $role = Role::where('name',$request->name)->first();
        if(!$role){

            Role::create($data);
        }

        $log = new Log();
        $log -> value = Auth::user()->name.', нь '.$role->name.' эрх үүсгэлээ.';
        $log -> phone = '';
        $log->staff=Auth::user()->name;
        $log -> save();

        return redirect('/role/list');
    }

    public function editRole($id){
        $role = Role::find($id);
        if($role){
            $categories = PermissionCategory::with('permissions')->get();
            $roleper = $role->permissions()->pluck('permissions.id')->toArray();
            return view('admin.role.edit', compact('role','categories','roleper'));
        }
        return redirect(route('role.manage'));
    }

    public function updateRole($id,Request $request){
        $role = Role::find($id);
        if($role){
            $oldPermission = \DB::table('role_has_permissions')
                ->where('role_id',$role->id)
                ->get()->pluck('permission_id')->toArray();
            if(count($oldPermission)>0){
                $oldPermission = Permission::whereIn('id',$oldPermission)->get();
                $role->revokePermissionTo($oldPermission);
            }
            if($request->permissions)
            {
                foreach($request->permissions as $permission) {
                    $role->givePermissionTo(Permission::find($permission));
                }
            }
        }
        return redirect('/role/list');
    }
}

?>
