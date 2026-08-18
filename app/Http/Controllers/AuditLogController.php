<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
        if ($request->filled('aktivitas')) $query->where('aktivitas', 'like', '%' . $request->aktivitas . '%');
        if ($request->filled('dari'))      $query->whereDate('created_at', '>=', $request->dari);
        if ($request->filled('sampai'))    $query->whereDate('created_at', '<=', $request->sampai);

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('audit-log.index', compact('logs', 'users'));
    }
}
